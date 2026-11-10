{{--
    Strook: het afsluitende blok "Zullen we kennismaken?" met foto, kop,
    tekst en de knop naar de kennismaking uit de config.

    Velden: kop (met accent), tekst.
--}}
<x-cta-split hart data-cms-strip="{{ $strip->id }}">
    <x-slot:kop data-cms-field="kop"><x-accent :tekst="$strip->kop" /></x-slot:kop>
    <x-slot:tekst data-cms-field="tekst">{{ $strip->tekst }}</x-slot:tekst>
</x-cta-split>
