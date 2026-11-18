{{--
    Strook: lopende tekst van een artikel, in een smalle kolom.

    Velden: kop (optioneel, een tussenkop met accent), inleiding
    (optioneel, groter) en tekst (opgemaakte tekst: alinea's, een accent
    als <em>). Opgemaakte tekst kent geen tussenkoppen, dus begint een
    tussenkop een nieuwe strook (#20). Kop en inleiding staan er alleen
    als ze gevuld zijn; vult een redacteur er een, dan herlaadt het frame
    na het opslaan.

    De tekst wordt ongeëscapet geprint, zoals de tekst van verhaal. Hij
    staat in een element met display:contents, zodat de alinea's de ruimte
    van .art-prose houden.
--}}
<div class="prose art-prose" data-cms-strip="{{ $strip->id }}">
    @if (filled($strip->kop))
        <h2 class="h3" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h2>
    @endif
    @if (filled($strip->inleiding))
        <p class="art-lead" data-cms-field="inleiding">{{ $strip->inleiding }}</p>
    @endif
    <div style="display:contents" data-cms-field="tekst">{!! $strip->tekst !!}</div>
</div>
