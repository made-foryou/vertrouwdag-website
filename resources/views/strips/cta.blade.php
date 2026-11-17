{{--
    Strook: het afsluitende blok met foto, kop, tekst en de knop naar de
    kennismaking uit de config.

    Velden: kop (met accent), tekst (optioneel), foto en variant. Zonder
    foto staat Romy aan tafel, de foto uit het ontwerp (x-cta-split).
    - Leeg: een hartje boven de kop, zoals op de homepage.
    - groot: een grotere kop, een foto van minstens 480px en geen hartje,
      zoals op /diensten.
    - eenvoudig: de gewone kop, een foto van minstens 480px en geen
      hartje, zoals op /werkwijze en /over-mij.
    - middel: de gewone kop, een foto van minstens 440px en geen hartje,
      zoals op /veelgestelde-vragen.
    - compact: de gewone kop, een foto van minstens 420px en geen hartje,
      zoals op /blog en het archief.
--}}
@php
    $opmaak = match ($strip->variant) {
        null, '' => ['hart' => true, 'groot' => false, 'fotoHoogte' => null],
        'groot' => ['hart' => false, 'groot' => true, 'fotoHoogte' => 480],
        'eenvoudig' => ['hart' => false, 'groot' => false, 'fotoHoogte' => 480],
        'middel' => ['hart' => false, 'groot' => false, 'fotoHoogte' => 440],
        'compact' => ['hart' => false, 'groot' => false, 'fotoHoogte' => 420],
        default => throw new InvalidArgumentException("De strook cta kent geen variant [{$strip->variant}]; laat hem leeg of kies groot, eenvoudig, middel of compact."),
    };
@endphp
<x-cta-split :foto="$strip->foto" :hart="$opmaak['hart']" :groot="$opmaak['groot']" :foto-hoogte="$opmaak['fotoHoogte']" data-cms-strip="{{ $strip->id }}">
    <x-slot:kop data-cms-field="kop"><x-accent :tekst="$strip->kop" /></x-slot:kop>
    @if (filled($strip->tekst))
        <x-slot:tekst data-cms-field="tekst">{{ $strip->tekst }}</x-slot:tekst>
    @endif
</x-cta-split>
