{{--
    Strook: het afsluitende blok met foto, kop, tekst en de knop naar de
    kennismaking uit de config.

    Velden: kop (met accent), tekst en variant. Zonder variant staat er
    een hartje boven de kop, zoals op de homepage. Met variant groot is
    de kop groter, de foto minstens 480px hoog en is er geen hartje,
    zoals op /diensten.
--}}
@php
    $groot = match ($strip->variant) {
        null, '' => false,
        'groot' => true,
        default => throw new InvalidArgumentException("De strook cta kent geen variant [{$strip->variant}]; laat hem leeg of kies groot."),
    };
@endphp
<x-cta-split :hart="! $groot" :groot="$groot" :foto-hoogte="$groot ? 480 : null" data-cms-strip="{{ $strip->id }}">
    <x-slot:kop data-cms-field="kop"><x-accent :tekst="$strip->kop" /></x-slot:kop>
    <x-slot:tekst data-cms-field="tekst">{{ $strip->tekst }}</x-slot:tekst>
</x-cta-split>
