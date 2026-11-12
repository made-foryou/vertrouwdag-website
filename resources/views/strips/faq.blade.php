{{--
    Strook: de veelgestelde vragen in een kaart met een tabje erboven. De
    eerste vraag staat open, de rest klapt open bij een klik.

    Velden: label (het tabje) en vragen (lijst met vraag, antwoord en de
    keuze prijzen). Het antwoord is opgemaakte tekst (alinea's, links,
    nadruk), zoals RichText van het package hem levert, en wordt
    ongeëscapet geprint, zoals de tekst van artikel-tekst.

    Staat de keuze prijzen aan, dan staat boven het antwoord de zin met de
    vanaf-prijzen uit de pakketten (prijzenzin uit App\Cms\Strips\Faq). Die
    zin staat buiten het veld antwoord, zodat meekijken hem niet
    overschrijft. Zonder prijs voor elk pakket is er geen zin.

    De strook geeft zoekmachines dezelfde vragen als FAQPage (JSON-LD,
    schema uit App\Cms\Strips\Faq), zodat de gestructureerde gegevens altijd
    gelijk zijn aan de pagina. Het tabje staat buiten de lijst: elk kind
    van data-cms-items is een vraag.
--}}
@use('App\Cms\Strips\Faq')
@push('jsonld')
    <x-json-ld :data="$strip->schema" />
@endpush
<section class="wrap" style="max-width:920px;padding-top:56px;padding-bottom:var(--sec)" data-cms-strip="{{ $strip->id }}">
    <div class="faq-box" data-reveal>
        <span class="faq-box__tab eb" data-cms-field="label">{{ $strip->label }}</span>
        <div style="display:contents" data-cms-items="vragen">
            @foreach ($strip->vragen ?? [] as $vraag)
                <x-faq-item :open="$loop->first" :boven="($vraag['prijzen'] ?? null) === Faq::MET_PRIJZEN ? $strip->prijzenzin : null">
                    <x-slot:vraag data-cms-field="vraag">{{ $vraag['vraag'] }}</x-slot:vraag>
                    <x-slot:antwoord data-cms-field="antwoord">{!! $vraag['antwoord'] !!}</x-slot:antwoord>
                </x-faq-item>
            @endforeach
        </div>
    </div>
</section>
