{{--
    Een dienst als mapje met een foto die erboven uitsteekt.

    Met href is het de linkkaart van de homepage: omschrijving, prijs en
    een icoon. Zonder href is het de uitgebreide kaart van de mobiele
    stapel op de dienstenpagina: prijs, omschrijving, de kenmerken en een
    knop naar de kennismaking. De volgorde volgt het ontwerp.

    kleur (lila, cream of groen) bepaalt alles wat per kaart verschilt:
    de achtergrond, de eyebrow, een gedempte prijsregel, het icoon, de
    kleur van de hartjes en de knop (zie App\Support\Dienstkleur). dienst
    (dag, partial of full) markeert de bedragen, prijs is de vanaf-prijs
    (App\Support\Prijs); zonder prijs heeft de kaart geen prijsregel.
    gespiegeld zet het tabje en de foto rechts.

    Zonder href is de omschrijving opgemaakte tekst uit het CMS, met een
    eigen alinea. Daarom staat ze dan in een div en niet in een p.

    foto is de foto uit het CMS ({url, alt, width, height, focal}) of
    null; zonder foto staat de foto van de dienst uit het ontwerp
    (App\Support\Dienst::foto()). De foto is sfeer en heeft daarom altijd
    een lege alt: de titel, omschrijving en prijs zeggen al wat de dienst
    is. Zo blijft ook de naam van de linkkaart kort (besluit Menno,
    2026-11-10, zie #12). De foto staat in een eigen vlak (.folder__peek)
    met de plaatshouder als achtergrond, zodat hij erop kan infaden (#39).

    De slots soort (eyebrow), titel en omschrijving geven hun attributen
    door aan hun element, bijvoorbeeld data-cms-field.
--}}
@props([
    'kleur',
    'dienst',
    'prijs' => null,
    'foto' => null,
    'href' => null,
    'draaiing' => 0,
    'gespiegeld' => false,
    'kenmerken' => [],
    'soort',
    'titel',
    'omschrijving',
])
@php
    $stijl = \App\Support\Dienstkleur::tryFrom($kleur) ?? throw new \InvalidArgumentException("De dienstkaart kent geen kleur [{$kleur}]; kies lila, cream of groen.");
    $welkeDienst = \App\Support\Dienst::tryFrom($dienst) ?? throw new \InvalidArgumentException("De dienstkaart kent geen dienst [{$dienst}]; kies dag, partial of full.");
    $element = $href !== null ? 'a' : 'article';
@endphp
<{{ $element }} @if ($href !== null) href="{{ $href }}" @endif {{ $attributes->class(['folder', 'folder--r' => $gespiegeld, 'f-'.$kleur]) }}>
    <span class="folder__peek" style="transform:rotate({{ $draaiing }}deg)"><x-foto :foto="$foto" :terugval="$welkeDienst->foto()" :breedte="1100" :hoogte="825" decoratief sizes="(max-width:720px) 224px, (max-width:1110px) 30vw, 245px" veld="foto" loading="lazy" /></span>
    <div class="folder__body">
        <span class="folder__tab" aria-hidden="true"></span>
        <p {{ $soort->attributes->class(array_filter(['eb', 'eb--sm', $stijl->eyebrowKlasse()])) }}>{{ $soort }}</p>
        <h3 {{ $titel->attributes->class(['h3']) }}>{{ $titel }}</h3>
        @if ($href !== null)
            <p {{ $omschrijving->attributes->class(['folder__desc'])->merge(['style' => 'flex-grow:1']) }}>{{ $omschrijving }}</p>
            <x-prijsregel :prijs="$prijs" :dienst="$dienst" :gedempt="$stijl->isGedempt()" />
            @switch ($kleur)
                @case ('lila')
                    <svg class="folder__icon" width="34" height="31" aria-hidden="true" fill="none" stroke="#401838" stroke-width="1.2" style="transform:rotate(12deg)"><use href="#heart"/></svg>
                    @break
                @case ('cream')
                    <svg class="folder__icon" width="30" height="30" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#404830" stroke-width="1.2" stroke-linecap="round" style="transform:rotate(-10deg)"><path d="M8 4v12a4 4 0 0 0 8 0V6a2.5 2.5 0 0 0-5 0v10a1 1 0 0 0 2 0V7"/></svg>
                    @break
                @case ('groen')
                    <svg class="folder__icon" width="34" height="31" aria-hidden="true" fill="#E0D0E0" style="transform:rotate(-8deg)"><use href="#heart"/></svg>
                    @break
            @endswitch
        @else
            <x-prijsregel :prijs="$prijs" :dienst="$dienst" :gedempt="$stijl->isGedempt()" />
            <div {{ $omschrijving->attributes->class(['folder__desc']) }}>{{ $omschrijving }}</div>
            @if (count($kenmerken) > 0)
                <ul class="hl">
                    @foreach ($kenmerken as $kenmerk)
                        <li><svg width="14" height="13" fill="{{ $stijl->hartKleur() }}" aria-hidden="true"><use href="#heart"/></svg>{{ $kenmerk }}</li>
                    @endforeach
                </ul>
            @endif
            <x-knop :href="url(config('vertrouwdag.cta.pad'))" :variant="$stijl->knopVariant()">{{ config('vertrouwdag.cta.label') }}</x-knop>
        @endif
    </div>
</{{ $element }}>
