{{--
    Strook: een kop met tekst, en ernaast een staande afbeelding in een
    boog.

    Velden: kop (optioneel, Kop met nadruk), tekst (opgemaakte tekst),
    afbeelding en positie.
    - kop: staat er alleen als hij gevuld is; vult een redacteur hem
      later, dan herlaadt het frame na het opslaan.
    - tekst: ongeëscapet geprint, zoals de tekst van verhaal.
    - afbeelding: {url, alt, width, height, focal} uit de Media-
      bibliotheek, met de alt-tekst van het bestand. De boog snijdt hem
      bij op 4 bij 5; het focuspunt blijft in beeld. Zonder afbeelding (in
      een concept: publiceren kan pas met een afbeelding) staat er een
      vaste foto, zodat de strook zijn vorm houdt.
    - positie: links of rechts, de kant van de afbeelding naast de tekst.
      Op een smal scherm staat de afbeelding altijd boven de tekst.
--}}
@php
    $rechts = match ($strip->positie) {
        null, '', 'links' => false,
        'rechts' => true,
        default => throw new InvalidArgumentException("De strook tekst-met-afbeelding kent geen positie [{$strip->positie}]; kies links of rechts."),
    };
@endphp
<section @class(['wrap sec tekst-foto', 'tekst-foto--rechts' => $rechts]) data-cms-strip="{{ $strip->id }}">
    <div class="tekst-foto__media" data-reveal>
        <x-foto :foto="$strip->afbeelding" terugval="resources/images/bruid-rozen.jpg" :breedte="1100" :hoogte="1650" veld="afbeelding" loading="lazy" />
    </div>
    <div class="tekst-foto__txt" data-reveal>
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" />
        <div class="prose" data-cms-field="tekst">{!! $strip->tekst !!}</div>
    </div>
</section>
