{{--
    Strook: lopende tekst van een artikel, in een smalle kolom.

    Velden: inleiding (optioneel, groter en als eerste) en tekst
    (opgemaakte tekst: alinea's en tussenkoppen, een accent als <em>). De
    tekst wordt ongeëscapet geprint, zoals de tekst van verhaal. Hij staat
    in een element met display:contents, zodat de alinea's en koppen de
    ruimte van .art-prose houden.
--}}
<div class="prose art-prose" data-cms-strip="{{ $strip->id }}">
    @if (filled($strip->inleiding))
        <p class="art-lead" data-cms-field="inleiding">{{ $strip->inleiding }}</p>
    @endif
    <div style="display:contents" data-cms-field="tekst">{!! $strip->tekst !!}</div>
</div>
