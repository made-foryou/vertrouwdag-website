{{--
    Strook: het afsluitende blok met foto, kop, tekst en de knop naar de
    kennismaking uit de config.

    Velden: kop (met accent), tekst en variant.
    - Leeg: een hartje boven de kop, zoals op de homepage.
    - groot: een grotere kop, een foto van minstens 480px en geen hartje,
      zoals op /diensten.
    - eenvoudig: de gewone kop, een foto van minstens 480px en geen
      hartje, zoals op /werkwijze en /over-mij.
--}}
@php
    $opmaak = match ($strip->variant) {
        null, '' => ['hart' => true, 'groot' => false, 'fotoHoogte' => null],
        'groot' => ['hart' => false, 'groot' => true, 'fotoHoogte' => 480],
        'eenvoudig' => ['hart' => false, 'groot' => false, 'fotoHoogte' => 480],
        default => throw new InvalidArgumentException("De strook cta kent geen variant [{$strip->variant}]; laat hem leeg of kies groot of eenvoudig."),
    };
@endphp
<x-cta-split :hart="$opmaak['hart']" :groot="$opmaak['groot']" :foto-hoogte="$opmaak['fotoHoogte']" data-cms-strip="{{ $strip->id }}">
    <x-slot:kop data-cms-field="kop"><x-accent :tekst="$strip->kop" /></x-slot:kop>
    <x-slot:tekst data-cms-field="tekst">{{ $strip->tekst }}</x-slot:tekst>
</x-cta-split>
