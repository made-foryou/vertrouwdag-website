{{--
    Het afsluitende blok: links een foto, rechts op aubergine een kop,
    een korte tekst en de knop naar de kennismaking uit de config.

    foto: de foto uit het CMS ({url, alt, width, height, focal}) of null;
    zonder foto staat Romy aan tafel, de foto uit het ontwerp (<x-foto>).
    kop: de waarde van het veld kop, een Kop met nadruk ({text, tag}). Het element komt uit het CMS,
    de opmaak is altijd .h2, net als elke andere strookkop (#22).
    fotoHoogte: de minimale hoogte van de foto in pixels. hart: een hartje boven de kop. groot: de
    ruimere tekst van de dienstenpagina. Het slot tekst geeft zijn attributen door aan zijn element,
    bijvoorbeeld data-cms-field. tekst is optioneel.
--}}
@props([
    'foto' => null,
    'fotoHoogte' => null,
    'hart' => false,
    'groot' => false,
    'kop',
    'tekst' => null,
])
<section {{ $attributes->class(['cta-split']) }}>
    <div class="cta-split__photo" data-reveal="photo" @if ($fotoHoogte) style="min-height:{{ $fotoHoogte }}px" @endif><x-foto :foto="$foto" terugval="resources/images/romy-gesprek.jpg" alt="Romy aan tafel met haar telefoon, klaar voor een kennismakingsgesprek" :breedte="1400" :hoogte="933" sizes="(max-width:620px) 100vw, 50vw" veld="foto" class="cta-romy" loading="lazy" /></div>
    <div class="cta-split__txt" data-reveal="text" @if ($groot) style="padding-top:80px;padding-bottom:80px;gap:22px" @endif>
        @if ($hart)
            <svg width="30" height="27" fill="#E0D0E0" aria-hidden="true"><use href="#heart"/></svg>
        @endif
        <x-made-cms::heading :heading="$kop" field="kop" emphasis class="h2" />
        @if ($tekst)
            <p {{ $tekst->attributes }}>{{ $tekst }}</p>
        @endif
        <div><x-knop :href="url(config('vertrouwdag.cta.pad'))" variant="lila">{{ config('vertrouwdag.cta.label') }}</x-knop></div>
    </div>
</section>
