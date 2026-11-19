<?php

namespace App\Checklist\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Support\Facades\URL;

/**
 * The one mail after signing up: a personal link that opens the checklist and confirms the address.
 * The link is signed and never expires, so it keeps opening the checklist. It carries the id of the
 * signup instead of the model, because the signup is gone once the address is in the newsletter list.
 */
final class ChecklistMail extends Mailable implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public readonly string $firstName,
        public readonly string $signupId,
    ) {}

    public function envelope(): Envelope
    {
        return new Envelope(subject: __('checklist.mail.subject'));
    }

    public function content(): Content
    {
        return new Content(
            view: 'mail.checklist',
            text: 'mail.checklist-text',
            with: ['checklistUrl' => $this->checklistUrl()],
        );
    }

    public function checklistUrl(): string
    {
        return URL::signedRoute('checklist.open', ['signup' => $this->signupId]);
    }
}
