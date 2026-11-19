<?php

use App\Checklist\Actions\PruneUnconfirmedSignups;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

Artisan::command('checklist:prune', function () {
    $pruned = PruneUnconfirmedSignups::run();

    $this->info(trans_choice('checklist.prune.pruned', $pruned, ['count' => $pruned]));
})->purpose('Delete checklist signups that were not confirmed within thirty days');

Schedule::command('checklist:prune')->daily();
