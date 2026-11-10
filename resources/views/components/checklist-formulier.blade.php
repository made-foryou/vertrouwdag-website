{{--
    Het aanmeldformulier voor de gratis checklist, op de homepage en op
    /checklist. De browser controleert de verplichte velden en het
    e-mailadres. Daarna toont resources/js/modules/checklist-formulier.js
    de melding uit data-bedankt, of het bedankblok (#bedankt) als dat op
    de pagina staat. Het formulier verstuurt nog niets: dat komt in #25.

    trouwdatum: een extra, optioneel veld voor de trouwdatum (/checklist).
    De slot staat bovenin het formulier, voor een eyebrow, kop en tekst.
    Attributen gaan naar het <form>, bijvoorbeeld class="cl-form".
--}}
@props([
    'trouwdatum' => false,
])
<form {{ $attributes->merge(['id' => 'checklist-form']) }} method="post" data-bedankt="Dankjewel! (Testversie: dit formulier verstuurt nog niets.)">
    {{ $slot }}
    <div class="row" style="gap:12px;align-items:stretch">
        <label class="field"><span class="eb eb--sm" style="color:#404830">Voornaam</span><input class="input" type="text" name="voornaam" autocomplete="given-name" required></label>
        <label class="field" style="flex-basis:220px"><span class="eb eb--sm" style="color:#404830">E-mailadres</span><input class="input" type="email" name="email" autocomplete="email" required></label>
    </div>
    @if ($trouwdatum)
        <label class="field"><span class="eb eb--sm" style="color:#404830">Trouwdatum <span style="color:var(--grijs)">(optioneel)</span></span><input class="input" type="date" name="trouwdatum"></label>
    @endif
    <label class="check"><input type="checkbox" name="avg" required><span>Ik ontvang de checklist en af en toe een mail van vertrouwdag. Afmelden kan altijd. Lees de <a href="{{ route('privacyverklaring') }}">privacyverklaring</a>.</span></label>
    <div><x-knop type="submit">Stuur mij de checklist</x-knop></div>
    <p class="form-msg" role="status" hidden></p>
</form>
