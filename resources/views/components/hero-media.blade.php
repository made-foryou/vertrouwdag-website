{{--
    The photos of the opening with an arch and a polaroid (resources/views/strips/hero.blade.php): the arch, the
    polaroid with its caption, the fixed stamp and the sticker, in one collage.

    strip: the hero strip, for its photos, caption and sticker. opmaak: the settings of its variant, made in the view
    of the strip: the class of the media column, and per part its inline style, the photo of the design to fall back
    on, its alt text, height, object-position and sizes. A variant without a stamp or a sticker has null there. The
    stamp of Over mij comes before the polaroid in the markup, as it did when that was a strip of its own.
--}}
@props([
    'strip',
    'opmaak',
])
@php
    ['boog' => $boog, 'polaroid' => $polaroid, 'zegel' => $zegel, 'sticker' => $sticker] = $opmaak;
@endphp
<div @class(array_filter(['hero__media', $opmaak['mediaKlasse']]))>
    <div class="hero__collage" data-entrance>
        <div class="arch" style="{{ $boog['stijl'] }}"><x-foto :foto="$strip->foto_boog" :terugval="$boog['terugval']" :alt="$boog['alt']" :breedte="1100" :hoogte="$boog['hoogte']" :positie="$boog['positie']" :sizes="$boog['sizes']" veld="foto_boog" fetchpriority="high" /></div>
        @if ($zegel !== null && $zegel['voorPolaroid'])
            <div class="stamp tilt" style="{{ $zegel['stijl'] }}"><x-brand-image name="monogram-groen" :sizes="$zegel['sizes']" alt="" :style="$zegel['beeldStijl']" /></div>
        @endif
        <div class="polaroid tilt" style="{{ $polaroid['stijl'] }}">
            <x-foto :foto="$strip->foto_polaroid" :terugval="$polaroid['terugval']" :alt="$polaroid['alt']" :breedte="1100" :hoogte="$polaroid['hoogte']" :sizes="$polaroid['sizes']" veld="foto_polaroid" />
            <span @if ($polaroid['bijschriftStijl']) style="{{ $polaroid['bijschriftStijl'] }}" @endif data-cms-field="bijschrift">{{ $strip->bijschrift }}</span>
        </div>
        @if ($zegel !== null && ! $zegel['voorPolaroid'])
            <div class="stamp tilt" style="{{ $zegel['stijl'] }}"><x-brand-image name="monogram-groen" :sizes="$zegel['sizes']" alt="" :style="$zegel['beeldStijl']" /></div>
        @endif
        @if ($sticker !== null)
            <div class="sticker" style="{{ $sticker }}" data-cms-field="sticker"><x-regels :tekst="$strip->sticker" /></div>
        @endif
    </div>
</div>
