<?php

namespace App\Checklist\Actions;

use App\Checklist\Models\Signup;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Deletes the signups that nobody confirmed in time, so the site does not keep addresses that never
 * asked for the newsletter. A confirmed signup stays until it is in the newsletter list.
 */
final class PruneUnconfirmedSignups
{
    use AsAction;

    public const int KEEP_DAYS = 30;

    public function handle(int $days = self::KEEP_DAYS): int
    {
        return Signup::query()
            ->unconfirmed()
            ->signedUpBefore(now()->subDays($days))
            ->delete();
    }
}
