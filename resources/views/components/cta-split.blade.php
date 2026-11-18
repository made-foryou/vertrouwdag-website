{{--
    Het afsluitende blok: links een foto, rechts op aubergine een kop,
    een korte tekst en de knop naar de kennismaking uit de config.

    foto: de foto uit het CMS ({url, alt, width, height, focal}) of null;
    zonder foto staat Romy aan tafel, de foto uit het ontwerp (<x-foto>).
    fotoHoogte: de minimale hoogte van de foto in pixels. hart: een hartje boven de kop. groot: de
    ruimere tekst van de dienstenpagina. De kop is altijd een .h2, net als elke andere strookkop.
    De slots kop en tekst geven hun attributen door aan hun element,
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
    <div class="cta-split__photo" @if ($fotoHoogte) style="min-height:{{ $fotoHoogte }}px" @endif><x-foto :foto="$foto" terugval="resources/images/romy-gesprek.jpg" alt="Romy aan tafel met haar telefoon, klaar voor een kennismakingsgesprek" :breedte="1400" :hoogte="933" veld="foto" class="cta-romy" loading="lazy" /></div>
    <div class="cta-split__txt" data-reveal-inner @if ($groot) style="padding-top:80px;padding-bottom:80px;gap:22px" @endif>
        @if ($hart)
            <svg width="30" height="27" fill="#E0D0E0" aria-hidden="true"><use href="#heart"/></svg>
        @endif
        <h2 {{ $kop->attributes->class(['h2']) }}>{{ $kop }}</h2>
        @if ($tekst)
            <p {{ $tekst->attributes }}>{{ $tekst }}</p>
        @endif
        <div><x-knop :href="url(config('vertrouwdag.cta.pad'))" variant="lila">{{ config('vertrouwdag.cta.label') }}</x-knop></div>
    </div>
</section>
