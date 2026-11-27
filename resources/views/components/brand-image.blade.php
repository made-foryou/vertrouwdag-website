{{--
    The logo or the monogram of the site in a narrow and a wide size (#39).

    The original PNGs are 941 and 460 pixels wide, while the site shows them
    at 50 to 152 pixels, so every page loaded about 70 kB of brand images it
    did not need. Both sizes are lossless WebP: the narrow one is sharp on a
    phone, the wide one on a phone with a DPR of 3 and on a sharp desktop
    screen. The browser picks one by sizes, which says how wide the image is
    shown at its place. The PNGs stay for the structured data (BedrijfSchema).

    name: logo-groen, logo-cream or monogram-groen. The other attributes,
    such as alt, class and style, go to the <img>; width and height are those
    of the wide size, so the browser knows the proportions before it loads.
--}}
@props([
    'name',
    'sizes',
])
@php
    /** @var array<string, array{narrow: int, wide: int, wideHeight: int}> $sizesPerImage */
    $sizesPerImage = [
        'logo-groen' => ['narrow' => 200, 'wide' => 320, 'wideHeight' => 161],
        'logo-cream' => ['narrow' => 200, 'wide' => 320, 'wideHeight' => 161],
        'monogram-groen' => ['narrow' => 140, 'wide' => 240, 'wideHeight' => 247],
    ];

    $image = $sizesPerImage[$name] ?? throw new InvalidArgumentException("Unknown brand image [{$name}].");
    $narrowUrl = Vite::asset("resources/images/{$name}-{$image['narrow']}.webp");
    $wideUrl = Vite::asset("resources/images/{$name}-{$image['wide']}.webp");
@endphp
<img src="{{ $wideUrl }}" srcset="{{ $narrowUrl }} {{ $image['narrow'] }}w, {{ $wideUrl }} {{ $image['wide'] }}w" sizes="{{ $sizes }}" width="{{ $image['wide'] }}" height="{{ $image['wideHeight'] }}" {{ $attributes }}>
