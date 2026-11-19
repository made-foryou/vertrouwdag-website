<?php

namespace App\Checklist\Data;

use Carbon\CarbonImmutable;

/**
 * A confirmed address as it goes to the newsletter service, with the moments of signing up and
 * confirming as proof of consent.
 */
final readonly class NewsletterContact
{
    public function __construct(
        public string $email,
        public string $firstName,
        public ?CarbonImmutable $weddingDate,
        public ?string $source,
        public CarbonImmutable $signedUpAt,
        public CarbonImmutable $confirmedAt,
    ) {}
}
