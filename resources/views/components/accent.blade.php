{{--
    Een kop met een accent: de woorden tussen sterretjes worden schuin en
    gekleurd (<em>), zoals "Jullie dag in *vertrouwde* handen". De rest
    van de tekst wordt geëscapet. Zet het component in het kopelement,
    zodat dat element data-cms-field kan dragen.

    In het CMS is zo'n kop App\Cms\Velden::kopMetAccent() (besluit #15).
    Het package kent de sterretjes nog niet (made-cms-package#144). Tot dan
    herlaadt het frame bij een kop met accent na het opslaan, want het
    patcht alleen een element met platte tekst.

    De uitvoer is een echo in een PHP-blok en geen Blade-echo, want Blade
    laat de regelovergang erna staan en die komt dan in de kop.
--}}
@props([
    'tekst',
])
@php
    echo preg_replace('/\*([^*]+)\*/', '<em>$1</em>', e($tekst ?? ''));
@endphp
