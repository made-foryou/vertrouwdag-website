{{--
    Een foto die Romy in het CMS kiest, met de foto uit het ontwerp als
    terugval (#21).

    foto: de waarde van een veld Afbeelding, {url, alt, width, height,
    focal}, of null. Null is: nog geen foto gekozen, of het bestand is uit
    de Media-bibliotheek. Dan staat de vaste foto er, met zijn eigen alt-tekst
    en maten, zodat een bestaande strook er hetzelfde uitziet als voor #21.

    terugval: het pad van de vaste foto voor Vite::asset(). alt, breedte en
    hoogte: die van de vaste foto. Een foto uit het CMS heeft de alt-tekst
    van zijn bestand en de maten van het bestand; een svg heeft geen maten
    en houdt die van de vaste foto. positie: de object-position van de
    vaste foto, als die afwijkt.

    Het focuspunt van een foto uit het CMS wordt de object-position. Elke
    lijst met foto's snijdt bij met object-fit:cover, dus het focuspunt
    bepaalt wat in beeld blijft.

    decoratief: de foto zegt niets wat de tekst ernaast niet al zegt, of
    staat in een visual met aria-hidden. Hij krijgt altijd een lege alt,
    ook als het bestand er een heeft (besluit Menno, 2026-11-10, zie #12).

    sizes: hoe breed de foto op de pagina staat, per schermbreedte (#39).
    Een foto uit het CMS heeft smallere formaten (srcset), en met sizes
    kiest de browser het kleinste dat scherp genoeg is: een telefoon laadt
    zo geen foto van 1920 px. Zonder formaten (een foto van voor de
    formaten, een svg, of de vaste foto) staan srcset en sizes er niet.

    veld: de naam van het veld, voor data-cms-field. Dat staat op de <img>
    zelf, zodat de bewerkschil een andere foto meteen toont; het package
    zet daarbij ook de srcset om en laat sizes staan. De overige
    attributen, zoals loading, fetchpriority, class en style, gaan door
    naar de <img>. Een foto met loading="lazy" staat onder de vouw en
    wordt ook buiten de hoofdthread gedecodeerd (decoding="async").

    data-foto: de foto faadt in zodra hij geladen is, op een plaatshouder
    van het element eromheen (fotos-laden.js, #39). Dat element heeft
    daarom de kleur --plaatshouder als achtergrond.
--}}
@props([
    'foto' => null,
    'terugval',
    'alt' => '',
    'breedte',
    'hoogte',
    'sizes',
    'positie' => null,
    'decoratief' => false,
    'veld',
])
@php
    $uitCms = is_array($foto) && is_string($foto['url'] ?? null);

    if ($uitCms) {
        $positie = round(($foto['focal']['x'] ?? 0.5) * 100, 2).'% '.round(($foto['focal']['y'] ?? 0.5) * 100, 2).'%';
    }

    $srcset = $uitCms && is_string($foto['srcset'] ?? null) ? $foto['srcset'] : null;
    $decodeerApart = $attributes->get('loading') === 'lazy' && ! $attributes->has('decoding');

    $stijl = implode(';', array_filter([
        rtrim(trim((string) $attributes->get('style')), ';'),
        $positie === null ? null : "object-position:{$positie}",
    ]));
@endphp
<img src="{{ $uitCms ? $foto['url'] : Vite::asset($terugval) }}" alt="{{ $decoratief ? '' : ($uitCms ? $foto['alt'] : $alt) }}" width="{{ $uitCms ? ($foto['width'] ?? $breedte) : $breedte }}" height="{{ $uitCms ? ($foto['height'] ?? $hoogte) : $hoogte }}" @if ($srcset !== null) srcset="{{ $srcset }}" sizes="{{ $sizes }}" @endif @if ($stijl !== '') style="{{ $stijl }}" @endif {{ $attributes->except('style') }} @if ($decodeerApart) decoding="async" @endif data-cms-field="{{ $veld }}" data-foto>
