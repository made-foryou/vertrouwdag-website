{{--
    Het afsluitende blok: links een foto, rechts op aubergine een kop,
    een korte tekst en de knop naar de kennismaking uit de config.

    foto en alt: standaard Romy aan tafel. fotoHoogte: de minimale hoogte
    van de foto in pixels. hart: een hartje boven de kop. groot: de
    grotere kop met ruimere tekst van de dienstenpagina.
    De slots kop en tekst geven hun attributen door aan hun element,
    bijvoorbeeld data-cms-field. tekst is optioneel.
--}}
@props([
    'foto' => null,
    'alt' => 'Romy aan tafel met haar telefoon, klaar voor een kennismakingsgesprek',
    'fotoHoogte' => null,
    'hart' => false,
    'groot' => false,
    'kop',
    'tekst' => null,
])
<section {{ $attributes->class(['cta-split']) }}>
    <div class="cta-split__photo" @if ($fotoHoogte) style="min-height:{{ $fotoHoogte }}px" @endif><img class="cta-romy" src="{{ $foto ?? Vite::asset('resources/images/romy-gesprek.jpg') }}" alt="{{ $alt }}" width="1400" height="933" loading="lazy"></div>
    <div class="cta-split__txt" data-reveal-inner @if ($groot) style="padding-top:80px;padding-bottom:80px;gap:22px" @endif>
        @if ($hart)
            <svg width="30" height="27" fill="#E0D0E0" aria-hidden="true"><use href="#heart"/></svg>
        @endif
        <h2 {{ $groot ? $kop->attributes->merge(['style' => 'font-size:clamp(40px,5vw,68px)']) : $kop->attributes }}>{{ $kop }}</h2>
        @if ($tekst)
            <p {{ $tekst->attributes }}>{{ $tekst }}</p>
        @endif
        <div><x-knop :href="route(config('vertrouwdag.cta.route'))" variant="lila">{{ config('vertrouwdag.cta.label') }}</x-knop></div>
    </div>
</section>
