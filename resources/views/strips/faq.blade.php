{{--
    Strook: de veelgestelde vragen in een kaart met een tabje erboven. De
    eerste vraag staat open, de rest klapt open bij een klik.

    Velden: label (het tabje) en vragen (lijst met vraag en antwoord). Het
    antwoord is opgemaakte tekst (alinea's, links, nadruk), zoals RichText
    van het package hem levert, en wordt ongeëscapet geprint, zoals de
    tekst van artikel-tekst.

    De strook geeft zoekmachines dezelfde vragen als FAQPage (JSON-LD),
    zodat de gestructureerde gegevens altijd gelijk zijn aan de pagina.
--}}
@use('App\Support\FaqSchema')
@push('jsonld')
    <x-json-ld :data="FaqSchema::vanVragen($strip->vragen ?? [])" />
@endpush
<section class="wrap" style="max-width:920px;padding-top:56px;padding-bottom:var(--sec)" data-cms-strip="{{ $strip->id }}">
    <div class="faq-box" data-reveal data-cms-items="vragen">
        <span class="faq-box__tab eb" data-cms-field="label">{{ $strip->label }}</span>
        @foreach ($strip->vragen ?? [] as $vraag)
            <x-faq-item :open="$loop->first">
                <x-slot:vraag data-cms-field="vraag">{{ $vraag['vraag'] }}</x-slot:vraag>
                <x-slot:antwoord data-cms-field="antwoord">{!! $vraag['antwoord'] !!}</x-slot:antwoord>
            </x-faq-item>
        @endforeach
    </div>
</section>
