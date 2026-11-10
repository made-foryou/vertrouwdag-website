{{--
    Strook: fases van de werkwijze als tijdlijn, elk met een genummerd
    boogje en een kaart. De kaarten staan om en om links en rechts.

    Velden: variant, eerste_nummer en fases (lijst met periode, titel en
    tekst).
    - variant: leeg, of band voor de salie achtergrond.
    - eerste_nummer: het nummer van de eerste fase, standaard 1. Zo loopt
      de nummering door als de tijdlijn na een andere strook verdergaat,
      zoals "Afronden" (6) na de trouwdag (5) op /werkwijze.
--}}
@php
    $opBand = match ($strip->variant) {
        null, '' => false,
        'band' => true,
        default => throw new InvalidArgumentException("De strook tijdlijn kent geen variant [{$strip->variant}]; laat hem leeg of kies band."),
    };
    $eersteNummer = (int) ($strip->eerste_nummer ?? 1);
@endphp
<section @if ($opBand) class="band" @endif data-cms-strip="{{ $strip->id }}">
    <ol class="tl wrap sec" style="max-width:1040px" @if ($eersteNummer !== 1) start="{{ $eersteNummer }}" @endif data-cms-items="fases">
        @foreach ($strip->fases as $fase)
            <li @class(['tl__step', 'tl__step--r' => $loop->even]) data-reveal><x-fasenummer :nummer="$eersteNummer + $loop->index" /><div class="tl__card"><p class="eb eb--sm eb--grijs" data-cms-field="periode">{{ $fase['periode'] }}</p><h2 data-cms-field="titel">{{ $fase['titel'] }}</h2><p class="tl__txt" data-cms-field="tekst">{{ $fase['tekst'] }}</p></div></li>
        @endforeach
    </ol>
</section>
