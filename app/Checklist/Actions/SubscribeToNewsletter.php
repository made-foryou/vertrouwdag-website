<?php

namespace App\Checklist\Actions;

use App\Checklist\Data\NewsletterContact;
use App\Checklist\MailerLite;
use App\Checklist\Models\Signup;
use LogicException;
use Lorisleiva\Actions\Concerns\AsAction;

/**
 * Puts a confirmed signup in the newsletter list. Once the list has the address, the site no longer
 * needs to keep the signup.
 */
final class SubscribeToNewsletter
{
    use AsAction;

    public function __construct(private readonly MailerLite $mailerLite) {}

    public function handle(Signup $signup): void
    {
        if ($signup->confirmed_at === null) {
            throw new LogicException('Only a confirmed signup goes to the newsletter list.');
        }

        $this->mailerLite->subscribe(new NewsletterContact(
            email: $signup->email,
            firstName: $signup->first_name,
            weddingDate: $signup->wedding_date,
            source: $signup->source,
            signedUpAt: $signup->signed_up_at,
            confirmedAt: $signup->confirmed_at,
        ));

        $signup->delete();
    }
}
