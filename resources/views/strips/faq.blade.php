{{--
    Strook: de veelgestelde vragen in een kaart met een tabje erboven. De
    eerste vraag staat open, de rest klapt open bij een klik.

    Velden: per tabblad een naam en een lijst met vragen (label en vragen,
    label_2 en vragen_2, label_3 en vragen_3). Een vraag heeft een vraag,
    een antwoord en de keuze prijzen. Het antwoord is opgemaakte tekst
    (alinea's, links, nadruk), zoals RichText van het package hem levert, en
    wordt ongeëscapet geprint, zoals de tekst van artikel-tekst.

    Welke tabbladen er zijn, bepaalt App\Cms\Strips\Faq::tabbladen(): het
    eerste altijd, het tweede en derde alleen met vragen. Met één tabblad
    staat zijn naam als tabje boven de kaart. Met meer zijn het tabjes die
    elk hun eigen vragen tonen, met hetzelfde gedrag als de mappen op
    /diensten (data-tabfolders, resources/js/modules/dienstmappen.js), en
    in elk tabblad staat de eerste vraag open. Zonder JavaScript staan de
    tabbladen onder elkaar, elk met zijn naam erboven. Een tweede of derde
    tabblad zonder vragen staat niet in de view: een wijziging van zijn
    naam herlaadt het frame, net als zijn eerste vraag.

    De naam van een tabblad is een h2 en elke vraag een h3 (x-faq-item). Een
    tabje is zelf de h2, met role="tab": HTML staat geen kop in een button
    toe, en een kop om het tabje heen hoort niet in een tablist. Omdat een
    h2 uit zichzelf geen focus krijgt, heeft het open tabje tabindex 0.

    Staat de keuze prijzen aan, dan staat boven het antwoord de zin met de
    vanaf-prijzen uit de pakketten (prijzenzin uit App\Cms\Strips\Faq). Die
    zin staat buiten het veld antwoord, zodat meekijken hem niet
    overschrijft. Zonder prijs voor elk pakket is er geen zin.

    De strook geeft zoekmachines de vragen van alle tabbladen als FAQPage
    (JSON-LD, schema uit App\Cms\Strips\Faq), zodat de gestructureerde
    gegevens altijd gelijk zijn aan de pagina. Het tabje staat buiten de
    lijst: elk kind van data-cms-items is een vraag.
--}}
@use('App\Cms\Strips\Faq')
@php($tabbladen = Faq::tabbladen($strip->values))
@push('jsonld')
    <x-json-ld :data="$strip->schema" />
@endpush
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap wrap--midden" style="padding-top:56px;padding-bottom:var(--sec)">
        @if (count($tabbladen) > 1)
            <div class="faq-box faq-box--tabbladen" data-tabfolders data-reveal>
                <div class="faq-box__tabs" role="tablist" aria-label="Veelgestelde vragen">
                    @foreach ($tabbladen as $tabblad)
                        <h2 class="faq-box__tab eb" role="tab" id="faq-{{ $strip->id }}-{{ $loop->iteration }}" aria-controls="faq-{{ $strip->id }}-{{ $loop->iteration }}-vragen" aria-selected="{{ $loop->first ? 'true' : 'false' }}" tabindex="{{ $loop->first ? '0' : '-1' }}"><span data-cms-field="{{ $tabblad['naamveld'] }}">{{ $tabblad['naam'] }}</span></h2>
                    @endforeach
                </div>
                @foreach ($tabbladen as $tabblad)
                    <div @class(['faq-box__panel', 'is-active' => $loop->first]) role="tabpanel" id="faq-{{ $strip->id }}-{{ $loop->iteration }}-vragen" aria-labelledby="faq-{{ $strip->id }}-{{ $loop->iteration }}" tabindex="0">
                        <p class="faq-box__naam eb">{{ $tabblad['naam'] }}</p>
                        <x-faq-vragen :lijst="$tabblad['lijst']" :vragen="$tabblad['vragen']" :prijzenzin="$strip->prijzenzin" />
                    </div>
                @endforeach
            </div>
        @else
            @php($tabblad = $tabbladen[0])
            <div class="faq-box" data-reveal>
                <h2 class="faq-box__tab eb" data-cms-field="{{ $tabblad['naamveld'] }}">{{ $tabblad['naam'] }}</h2>
                <x-faq-vragen :lijst="$tabblad['lijst']" :vragen="$tabblad['vragen']" :prijzenzin="$strip->prijzenzin" />
            </div>
        @endif
    </div>
</section>
