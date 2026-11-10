{{--
    Een kop met een accent: de woorden tussen sterretjes worden schuin en
    gekleurd (<em>), zoals "Jullie dag in *vertrouwde* handen". De rest
    van de tekst wordt geëscapet. Zet het component in het kopelement,
    zodat dat element data-cms-field kan dragen.

    Hoe het accent straks in het CMS werkt, besluit #15. Kiest dat een
    andere vorm, dan verandert alleen dit component.

    De uitvoer is een echo in een PHP-blok en geen Blade-echo, want Blade
    laat de regelovergang erna staan en die komt dan in de kop.
--}}
@props([
    'tekst',
])
@php
    echo preg_replace('/\*([^*]+)\*/', '<em>$1</em>', e($tekst ?? ''));
@endphp
