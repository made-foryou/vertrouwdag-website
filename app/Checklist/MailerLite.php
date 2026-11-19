<?php

namespace App\Checklist;

use App\Checklist\Data\NewsletterContact;
use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\RequestException;
use Illuminate\Support\Facades\Http;

/**
 * The newsletter list of vertrouwdag lives in MailerLite. This is the only class that knows that: if
 * the list moves to another service, only this class changes.
 *
 * MailerLite's own double opt-in for the API is switched off in the account, because the checklist
 * mail already confirms the address. A contact that unsubscribed earlier only becomes active again with
 * "resubscribe", which MailerLite reserves for someone who asked to subscribe again. A confirmed signup
 * is exactly that: the visitor ticked the consent box and opened the link in the mail.
 *
 * @see https://developers.mailerlite.com/docs/subscribers
 */
final class MailerLite
{
    /**
     * Creates the contact, or updates the existing one with the same address. MailerLite only adds the
     * group and leaves fields that are not sent untouched.
     *
     * @throws RequestException when MailerLite refuses the contact or is unavailable
     */
    public function subscribe(NewsletterContact $contact): void
    {
        $this->request()
            ->post('subscribers', [
                'email' => $contact->email,
                'fields' => array_filter([
                    'name' => $contact->firstName,
                    'trouwdatum' => $contact->weddingDate?->format('Y-m-d'),
                    'bron' => $contact->source,
                ], fn (?string $value) => $value !== null),
                'groups' => [config()->string('services.mailerlite.group')],
                'status' => 'active',
                'resubscribe' => true,
                'subscribed_at' => $contact->signedUpAt->utc()->format('Y-m-d H:i:s'),
                'opted_in_at' => $contact->confirmedAt->utc()->format('Y-m-d H:i:s'),
            ])
            ->throw();
    }

    private function request(): PendingRequest
    {
        return Http::baseUrl(config()->string('services.mailerlite.url'))
            ->withToken(config()->string('services.mailerlite.key'))
            ->acceptJson()
            ->asJson()
            ->timeout(10);
    }
}
