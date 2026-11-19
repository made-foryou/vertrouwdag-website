<?php

use App\Checklist\Jobs\SubscribeToNewsletterJob;
use App\Checklist\Models\Signup;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Queue;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Str;

beforeEach(function () {
    Queue::fake();

    $this->pdf = UploadedFile::fake()->create('checklist.pdf', 12, 'application/pdf');
    config(['vertrouwdag.checklist.pdf' => $this->pdf->getPathname()]);
});

test('the signed link opens the checklist and confirms the address', function () {
    $signup = Signup::factory()->create();

    $response = $this->get(URL::signedRoute('checklist.open', ['signup' => $signup->id]));

    $response->assertOk()
        ->assertHeader('Content-Type', 'application/pdf')
        ->assertHeader('X-Robots-Tag', 'noindex');

    expect($response->headers->get('Content-Disposition'))->toBe('inline; filename=vertrouwdag-trouwplanning-checklist.pdf')
        ->and($response->headers->getCacheControlDirective('private'))->toBeTrue()
        ->and($signup->fresh()->confirmed_at)->not->toBeNull();

    Queue::assertPushed(SubscribeToNewsletterJob::class, fn (SubscribeToNewsletterJob $job) => $job->signupId === $signup->id);
});

test('opening the link twice confirms the address once', function () {
    $signup = Signup::factory()->create();
    $link = URL::signedRoute('checklist.open', ['signup' => $signup->id]);

    $this->get($link)->assertOk();
    $confirmedAt = $signup->fresh()->confirmed_at;

    $this->travel(5)->minutes();
    $this->get($link)->assertOk();

    expect($signup->fresh()->confirmed_at?->toIso8601String())->toBe($confirmedAt?->toIso8601String());
    Queue::assertPushedTimes(SubscribeToNewsletterJob::class, 1);
});

test('the link keeps opening the checklist once the signup is on the list', function () {
    $this->get(URL::signedRoute('checklist.open', ['signup' => (string) Str::ulid()]))
        ->assertOk()
        ->assertHeader('Content-Type', 'application/pdf');

    Queue::assertNothingPushed();
});

test('a link without a valid signature opens nothing and confirms nothing', function () {
    $signup = Signup::factory()->create();
    $other = Signup::factory()->create();
    $link = URL::signedRoute('checklist.open', ['signup' => $signup->id]);

    $this->get(route('checklist.open', ['signup' => $signup->id]))->assertForbidden();
    $this->get(str_replace($signup->id, $other->id, $link))->assertForbidden();

    expect($signup->fresh()->confirmed_at)->toBeNull()
        ->and($other->fresh()->confirmed_at)->toBeNull();
    Queue::assertNothingPushed();
});
