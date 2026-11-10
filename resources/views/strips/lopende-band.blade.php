{{--
    Strook: de lopende band met korte teksten.

    Velden: teksten (lijst met tekst). De band krijgt geen data-cms-items:
    tussen de teksten staan hartjes en lopende-band.js vult hem aan, dus
    zijn kinderen zijn geen items. Een wijziging herlaadt de frame.
--}}
<x-lopende-band data-cms-strip="{{ $strip->id }}" :teksten="array_column($strip->teksten, 'tekst')" />
