<?php

namespace App\Http\Requests;

use App\Checklist\Data\NewSignup;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;

/**
 * The checklist form on the homepage and on /checklist. The field names are the ones of the form.
 * The field "website" is a honeypot: people never see it, so only a bot fills it in.
 */
final class StoreChecklistSignupRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, list<string>>
     */
    public function rules(): array
    {
        return [
            'voornaam' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email:rfc', 'max:254'],
            'trouwdatum' => ['nullable', 'date_format:Y-m-d'],
            'avg' => ['accepted'],
            'bron' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function isSpam(): bool
    {
        return filled($this->input('website'));
    }

    public function toData(): NewSignup
    {
        return new NewSignup(
            firstName: $this->string('voornaam')->trim()->value(),
            email: $this->string('email')->value(),
            weddingDate: $this->date('trouwdatum', 'Y-m-d')?->toImmutable()->startOfDay(),
            source: $this->filled('bron') ? $this->string('bron')->trim()->value() : null,
        );
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => Str::lower(trim((string) $this->input('email')))]);
    }

    /**
     * Without JavaScript the visitor lands back on the form, with the errors and what they filled in.
     */
    protected function getRedirectUrl(): string
    {
        return url()->previous().'#checklist-form';
    }
}
