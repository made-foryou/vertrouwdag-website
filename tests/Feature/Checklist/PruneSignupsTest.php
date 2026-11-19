<?php

use App\Checklist\Models\Signup;
use Illuminate\Console\Scheduling\Event;
use Illuminate\Console\Scheduling\Schedule;

test('signups that were not confirmed within thirty days are pruned', function () {
    $expired = Signup::factory()->create(['signed_up_at' => now()->subDays(31)]);
    $recent = Signup::factory()->create(['signed_up_at' => now()->subDays(29)]);
    $confirmed = Signup::factory()->confirmed()->create(['signed_up_at' => now()->subDays(31)]);

    $this->artisan('checklist:prune')
        ->expectsOutput('1 onbevestigde aanmelding opgeruimd.')
        ->assertSuccessful();

    expect(Signup::query()->pluck('id')->sort()->values()->all())
        ->toBe(collect([$recent->id, $confirmed->id])->sort()->values()->all())
        ->and(Signup::query()->find($expired->id))->toBeNull();
});

test('pruning runs every day', function () {
    $events = collect(app(Schedule::class)->events())
        ->filter(fn (Event $event) => str_contains((string) $event->command, 'checklist:prune'));

    expect($events)->toHaveCount(1)
        ->and($events->first()->expression)->toBe('0 0 * * *');
});
