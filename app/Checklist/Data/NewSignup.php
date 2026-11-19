<?php

namespace App\Checklist\Data;

use Carbon\CarbonImmutable;

/**
 * What a visitor filled in on the checklist form. The source is the page the form was on, such as
 * "homepage" or "checklist".
 */
final readonly class NewSignup
{
    public function __construct(
        public string $firstName,
        public string $email,
        public ?CarbonImmutable $weddingDate,
        public ?string $source,
    ) {}
}
