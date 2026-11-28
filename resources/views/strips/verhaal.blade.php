{{--
    Strook: een kop met daaronder lopende tekst, met of zonder afbeelding
    ernaast (in het CMS: "Tekst").

    Velden: kop (optioneel, Kop met nadruk), tekst (opgemaakte tekst, een
    of meer alinea's), afbeelding en positie. De tekst wordt ongeëscapet
    geprint, zoals de tekst van over-mij-intro: het package laat er alleen
    veilige opmaak in. De kop staat er alleen als hij gevuld is; vult een
    redacteur hem later, dan herlaadt het frame na het opslaan.

    Without a photo it is one narrow column, the markup of the old verhaal. With a photo it is the markup of the old
    tekst-met-afbeelding: the photo in an arch of 4 by 5 next to the text, on the left or right (positie), and above
    the text on a narrow screen. The photo has the alt text of its file. Adding or removing the photo changes the
    layout, so the frame reloads after saving.
--}}
@php
    $rechts = match ($strip->positie) {
        null, '', 'links' => false,
        'rechts' => true,
        default => throw new InvalidArgumentException("De strook tekst kent geen positie [{$strip->positie}]; kies links of rechts."),
    };
@endphp
<section data-cms-strip="{{ $strip->id }}">
    @if (filled($strip->afbeelding))
        <div @class(['wrap sec tekst-foto', 'tekst-foto--rechts' => $rechts])>
            <div class="tekst-foto__media" data-reveal="photo">
                <x-foto :foto="$strip->afbeelding" terugval="resources/images/bruid-rozen.jpg" :breedte="1100" :hoogte="1650" sizes="(max-width:500px) 80vw, (max-width:860px) 405px, (max-width:1260px) 35vw, 431px" veld="afbeelding" loading="lazy" />
            </div>
            <div class="tekst-foto__txt" data-reveal="text">
                <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" />
                <div class="prose" data-cms-field="tekst">{!! $strip->tekst !!}</div>
            </div>
        </div>
    @else
        <div class="wrap wrap--smal sec story">
            <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" data-reveal="text" />
            <div class="prose" data-reveal="text" data-cms-field="tekst">{!! $strip->tekst !!}</div>
        </div>
    @endif
</section>
