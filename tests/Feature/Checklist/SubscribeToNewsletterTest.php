<?php

use App\Checklist\Jobs\SubscribeToNewsletterJob;
use App\Checklist\Models\Signup;
use Carbon\CarbonImmutable;
use Illuminate\Http\Client\Request;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    Http::preventStrayRequests();

    config([
        'services.mailerlite.key' => 'test-api-key',
        'services.mailerlite.group' => '172836491',
    ]);
});

test('a confirmed signup becomes an active contact in the MailerLite group and leaves the database', function () {
    Http::fake(['connect.mailerlite.com/*' => Http::response(['data' => ['id' => '31897397363737859']], 201)]);

    $signup = Signup::factory()->confirmed()->create([
        'first_name' => 'Anna',
        'email' => 'anna@example.com',
        'wedding_date' => '2027-06-12',
        'source' => 'checklist',
        'signed_up_at' => CarbonImmutable::parse('2026-11-19 09:15:00', 'UTC'),
        'confirmed_at' => CarbonImmutable::parse('2026-11-19 09:20:30', 'UTC'),
    ]);

    (new SubscribeToNewsletterJob($signup->id))->handle();

    Http::assertSent(function (Request $request) {
        return $request->method() === 'POST'
            && $request->url() === 'https://connect.mailerlite.com/api/subscribers'
            && $request->hasHeader('Authorization', 'Bearer test-api-key')
            && $request->data() === [
                'email' => 'anna@example.com',
                'fields' => [
                    'name' => 'Anna',
                    'trouwdatum' => '2027-06-12',
                    'bron' => 'checklist',
                ],
                'groups' => ['172836491'],
                'status' => 'active',
                'resubscribe' => true,
                'subscribed_at' => '2026-11-19 09:15:00',
                'opted_in_at' => '2026-11-19 09:20:30',
            ];
    });

    expect(Signup::query()->count())->toBe(0);
});

test('a contact without a wedding date or source does not overwrite those fields in MailerLite', function () {
    Http::fake(['connect.mailerlite.com/*' => Http::response([], 200)]);

    $signup = Signup::factory()->confirmed()->create(['wedding_date' => null, 'source' => null]);

    (new SubscribeToNewsletterJob($signup->id))->handle();

    Http::assertSent(fn (Request $request) => array_keys($request->data()['fields']) === ['name']);
});

test('an outage at MailerLite throws, so the job tries again later and the signup stays', function () {
    Http::fake(['connect.mailerlite.com/*' => Http::response(['message' => 'Server Error'], 503)]);

    $signup = Signup::factory()->confirmed()->create();

    expect(fn () => (new SubscribeToNewsletterJob($signup->id))->handle())->toThrow(RequestException::class)
        ->and(Signup::query()->count())->toBe(1);
});

test('a rate limit at MailerLite releases the job for as long as MailerLite asks', function () {
    Http::fake(['connect.mailerlite.com/*' => Http::response(['message' => 'Too Many Attempts.'], 429, ['Retry-After' => '42'])]);

    $signup = Signup::factory()->confirmed()->create();
    $job = (new SubscribeToNewsletterJob($signup->id))->withFakeQueueInteractions();

    $job->handle();

    $job->assertReleased(delay: 42);
    expect(Signup::query()->count())->toBe(1);
});

test('a contact that MailerLite refuses fails at once instead of trying again', function () {
    Http::fake(['connect.mailerlite.com/*' => Http::response(['message' => 'The email must be a valid email address.'], 422)]);

    $signup = Signup::factory()->confirmed()->create();
    $job = (new SubscribeToNewsletterJob($signup->id))->withFakeQueueInteractions();

    $job->handle();

    $job->assertFailed();
    expect(Signup::query()->count())->toBe(1);
});

test('a signup that is gone or not confirmed is not sent to MailerLite', function () {
    $unconfirmed = Signup::factory()->create();

    (new SubscribeToNewsletterJob('01JD0000000000000000000000'))->handle();
    (new SubscribeToNewsletterJob($unconfirmed->id))->handle();

    Http::assertNothingSent();
});
