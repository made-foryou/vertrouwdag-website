{{--
    Het aanmeldformulier voor de gratis checklist, op de homepage en op
    /checklist. Het post naar checklist.signup (#25). Met JavaScript
    verstuurt resources/js/modules/checklist-formulier.js het op de
    achtergrond en toont het de melding uit data-bedankt, of het bedankblok
    (#bedankt) als dat op de pagina staat. Zonder JavaScript komt de
    bezoeker na het versturen terug op het formulier, met de bedankmelding
    of de fout en wat hij invulde.

    Het veld website is een honeypot: buiten beeld en buiten de tabvolgorde,
    dus alleen een bot vult het in. De bron is de pagina van het formulier.

    trouwdatum: een extra, optioneel veld voor de trouwdatum (/checklist).
    De slot staat bovenin het formulier, voor een eyebrow, kop en tekst.
    Attributen gaan naar het <form>, bijvoorbeeld class="cl-form".
--}}
@props([
    'trouwdatum' => false,
])
@php
    // Read from the session instead of $errors: the view is also rendered outside the web middleware (tests).
    $validationErrors = session('errors')?->getBag('default');
    $message = $validationErrors?->first() ?: (session('checklist.signed_up') ? __('checklist.signup.thanks') : null);
    $source = request()->is('/') ? 'homepage' : request()->path();
    $invalid = fn (string $field): bool => (bool) $validationErrors?->has($field);
@endphp
<form {{ $attributes->merge(['id' => 'checklist-form']) }} method="post" action="{{ route('checklist.signup') }}"
      data-bedankt="{{ __('checklist.signup.thanks') }}"
      data-message-expired="{{ __('checklist.signup.expired') }}"
      data-message-throttled="{{ __('checklist.signup.throttled') }}"
      data-message-failed="{{ __('checklist.signup.failed', ['email' => config('vertrouwdag.email')]) }}">
    {{ $slot }}
    @csrf
    <input type="hidden" name="bron" value="{{ $source }}">
    <div aria-hidden="true" style="position:absolute;left:-10000px;width:1px;height:1px;overflow:hidden"><label>Website <input type="text" name="website" tabindex="-1" autocomplete="off"></label></div>
    <div class="row" style="gap:12px;align-items:stretch">
        <label class="field"><span class="eb eb--sm" style="color:#404830">Voornaam</span><input class="input" type="text" name="voornaam" autocomplete="given-name" required value="{{ old('voornaam') }}" @if ($invalid('voornaam')) aria-invalid="true" @endif></label>
        <label class="field" style="flex-basis:220px"><span class="eb eb--sm" style="color:#404830">E-mailadres</span><input class="input" type="email" name="email" autocomplete="email" required value="{{ old('email') }}" @if ($invalid('email')) aria-invalid="true" @endif></label>
    </div>
    @if ($trouwdatum)
        <label class="field"><span class="eb eb--sm" style="color:#404830">Trouwdatum <span style="color:var(--grijs)">(optioneel)</span></span><input class="input" type="date" name="trouwdatum" value="{{ old('trouwdatum') }}" @if ($invalid('trouwdatum')) aria-invalid="true" @endif></label>
    @endif
    <label class="check"><input type="checkbox" name="avg" required @checked(old('avg')) @if ($invalid('avg')) aria-invalid="true" @endif><span>Ik ontvang de checklist en af en toe een mail van vertrouwdag. Afmelden kan altijd. Lees de <a href="{{ url('/privacyverklaring') }}">privacyverklaring</a>.</span></label>
    <div><x-knop type="submit">Stuur mij de checklist</x-knop></div>
    <p class="form-msg" role="status" @if ($message === null) hidden @endif>{{ $message }}</p>
</form>
