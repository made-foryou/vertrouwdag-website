{{--
    Strook: het afsluitende blok met foto, kop, tekst en de knop naar de
    kennismaking uit de config.

    Velden: label, kop (Kop met nadruk), tekst (optioneel), foto en variant.
    Zonder foto staat Romy aan tafel, de foto uit het ontwerp (x-cta-split).
    - Leeg: een hartje boven de kop, zoals op de homepage.
    - groot: ruimere tekst, een foto van minstens 480px en geen hartje,
      zoals op /diensten.
    - eenvoudig: een foto van minstens 480px en geen hartje, zoals op
      /werkwijze en /over-mij.
    - middel: een foto van minstens 440px en geen hartje, zoals op
      /veelgestelde-vragen.
    - compact: een foto van minstens 420px en geen hartje, zoals op /blog
      en het archief.
    De kop is in elke variant even groot (.h2).

    The variant kaart is the markup of the old kennismaking: the label, heading and text on an aubergine card with the
    button next to them, without the photo, as the close of an article. Only this variant shows the label.
--}}
@php
    $opmaak = match ($strip->variant) {
        null, '' => ['hart' => true, 'groot' => false, 'fotoHoogte' => null],
        'groot' => ['hart' => false, 'groot' => true, 'fotoHoogte' => 480],
        'eenvoudig' => ['hart' => false, 'groot' => false, 'fotoHoogte' => 480],
        'middel' => ['hart' => false, 'groot' => false, 'fotoHoogte' => 440],
        'compact' => ['hart' => false, 'groot' => false, 'fotoHoogte' => 420],
        'kaart' => null,
        default => throw new InvalidArgumentException("De strook cta kent geen variant [{$strip->variant}]; laat hem leeg of kies groot, eenvoudig, middel, compact of kaart."),
    };
@endphp
@if ($opmaak === null)
    <section data-cms-strip="{{ $strip->id }}">
        <div class="wrap sec">
            <div class="founding" data-reveal>
                <div class="stack" style="gap:12px;flex:1 1 420px">
                    <p class="eb eb--lila" data-cms-field="label">{{ $strip->label }}</p>
                    <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" />
                    <p style="font-size:19px" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
                </div>
                <x-knop :href="url(config('vertrouwdag.cta.pad'))" variant="lila">{{ config('vertrouwdag.cta.label') }}</x-knop>
            </div>
        </div>
    </section>
@else
    <x-cta-split :kop="$strip->kop" :foto="$strip->foto" :hart="$opmaak['hart']" :groot="$opmaak['groot']" :foto-hoogte="$opmaak['fotoHoogte']" data-cms-strip="{{ $strip->id }}">
        @if (filled($strip->tekst))
            <x-slot:tekst data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></x-slot:tekst>
        @endif
    </x-cta-split>
@endif
