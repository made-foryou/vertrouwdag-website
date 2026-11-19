<?php

namespace App\Checklist\Actions;

use App\Checklist\Data\NewSignup;
use App\Checklist\Mail\ChecklistMail;
use App\Checklist\Models\Signup;
use Illuminate\Support\Facades\Mail;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Saves a signup and sends the checklist mail. An address has one signup at most: signing up again
 * updates it and sends the mail with the same link again.
 */
final class RegisterSignup
{
    use AsAction;

    public function handle(NewSignup $newSignup): Signup
    {
        $signup = Signup::query()->updateOrCreate(
            ['email' => $newSignup->email],
            [
                'first_name' => $newSignup->firstName,
                'wedding_date' => $newSignup->weddingDate,
                'source' => $newSignup->source,
                'signed_up_at' => now(),
            ],
        );

        Mail::to($signup->email, $signup->first_name)->queue(new ChecklistMail($signup->first_name, $signup->id));

        return $signup;
    }
}
