{{--
    Een dienst als mapje met een foto die erboven uitsteekt.

    Met href is het de linkkaart van de homepage: omschrijving, prijs en
    een icoon. Zonder href is het de uitgebreide kaart van de mobiele
    stapel op de dienstenpagina: prijs, omschrijving, de kenmerken en een
    knop naar de kennismaking. De volgorde volgt het ontwerp.

    kleur (lila, cream of groen) bepaalt alles wat per kaart verschilt:
    de achtergrond, de eyebrow, een gedempte prijsregel, het icoon, de
    kleur van de hartjes en de knop. dienst is de sleutel van de prijs in
    config/vertrouwdag.php. gespiegeld zet het tabje en de foto rechts.

    De slots soort (eyebrow), titel en omschrijving geven hun attributen
    door aan hun element, bijvoorbeeld data-cms-field.
--}}
@props([
    'kleur',
    'dienst',
    'foto',
    'alt',
    'href' => null,
    'draaiing' => 0,
    'gespiegeld' => false,
    'kenmerken' => [],
    'soort',
    'titel',
    'omschrijving',
])
@php
    $stijl = match ($kleur) {
        'lila' => ['eyebrow' => null, 'gedempt' => false, 'hart' => '#401838', 'knop' => 'aubergine'],
        'cream' => ['eyebrow' => 'eb--grijs', 'gedempt' => true, 'hart' => '#C8AEC6', 'knop' => 'primair'],
        'groen' => ['eyebrow' => 'eb--lila', 'gedempt' => true, 'hart' => '#E0D0E0', 'knop' => 'lila'],
        default => throw new \InvalidArgumentException("De dienstkaart kent geen kleur [{$kleur}]; kies lila, cream of groen."),
    };
    $element = $href !== null ? 'a' : 'article';
@endphp
<{{ $element }} @if ($href !== null) href="{{ $href }}" @endif {{ $attributes->class(['folder', 'folder--r' => $gespiegeld, 'f-'.$kleur]) }}>
    <img class="folder__peek" src="{{ $foto }}" alt="{{ $alt }}" width="1100" height="825" loading="lazy" style="transform:rotate({{ $draaiing }}deg)">
    <div class="folder__body">
        <span class="folder__tab" aria-hidden="true"></span>
        <p {{ $soort->attributes->class(array_filter(['eb', 'eb--sm', $stijl['eyebrow']])) }}>{{ $soort }}</p>
        <h3 {{ $titel->attributes }}>{{ $titel }}</h3>
        @if ($href !== null)
            <p {{ $omschrijving->attributes->class(['folder__desc'])->merge(['style' => 'flex-grow:1']) }}>{{ $omschrijving }}</p>
            <x-prijsregel :dienst="$dienst" :gedempt="$stijl['gedempt']" />
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
            <x-prijsregel :dienst="$dienst" :gedempt="$stijl['gedempt']" />
            <p {{ $omschrijving->attributes->class(['folder__desc']) }}>{{ $omschrijving }}</p>
            @if (count($kenmerken) > 0)
                <ul class="hl">
                    @foreach ($kenmerken as $kenmerk)
                        <li><svg width="14" height="13" fill="{{ $stijl['hart'] }}" aria-hidden="true"><use href="#heart"/></svg>{{ $kenmerk }}</li>
                    @endforeach
                </ul>
            @endif
            <x-knop :href="route(config('vertrouwdag.cta.route'))" :variant="$stijl['knop']">{{ config('vertrouwdag.cta.label') }}</x-knop>
        @endif
    </div>
</{{ $element }}>
