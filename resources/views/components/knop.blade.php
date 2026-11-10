{{--
    Een knop uit het ontwerp. Met href een link, anders een <button>
    (standaard type="button"; geef type="submit" mee voor een formulier).

    variant: primair (groen), secundair (omlijnd), lila of aubergine.
    klein: de kleinere knop uit de header.
--}}
@props([
    'href' => null,
    'variant' => 'primair',
    'klein' => false,
])
@php
    $klassen = array_filter([
        'btn',
        match ($variant) {
            'primair' => null,
            'secundair' => 'btn--o',
            'lila' => 'btn--l',
            'aubergine' => 'btn--a',
            default => throw new \InvalidArgumentException("De knop kent geen variant [{$variant}]; kies primair, secundair, lila of aubergine."),
        },
        $klein ? 'btn--sm' : null,
    ]);
@endphp
@if ($href !== null)
    <a href="{{ $href }}" {{ $attributes->class($klassen) }}>{{ $slot }}</a>
@else
    <button {{ $attributes->class($klassen)->merge(['type' => 'button']) }}>{{ $slot }}</button>
@endif
