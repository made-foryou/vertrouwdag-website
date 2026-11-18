{{--
    De tekst van een tekstvak (LongText), met een <br> voor elke enter,
    zodat de regels van de redacteur ook op de site regels zijn. De tekst
    wordt geëscapet. Zet het component in het element dat data-cms-field
    draagt: ziet het frame van de bewerkschil daar een <br>, dan patcht
    het de regels live mee. De eerste enter in een tekst herlaadt het frame.

    De uitvoer is een echo in een PHP-blok en geen Blade-echo, want Blade
    laat de regelovergang erna staan en die komt dan in de tekst.
--}}
@props([
    'tekst',
])
@php
    echo nl2br(e($tekst ?? ''));
@endphp
