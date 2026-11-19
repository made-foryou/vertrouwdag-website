<?php

namespace App\Checklist\Actions;

use App\Checklist\Jobs\SubscribeToNewsletterJob;
use App\Checklist\Models\Signup;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Confirms the address of a signup when the visitor opens the link in the checklist mail, and sends
 * it to the newsletter list. The update only matches an unconfirmed signup, so two clicks at the same
 * moment still confirm it once.
 */
final class ConfirmSignup
{
    use AsAction;

    public function handle(string $signupId): void
    {
        $confirmed = Signup::query()
            ->whereKey($signupId)
            ->unconfirmed()
            ->update(['confirmed_at' => now()]);

        if ($confirmed === 1) {
            SubscribeToNewsletterJob::dispatch($signupId);
        }
    }
}
