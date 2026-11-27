{{--
    De layout van elke pagina: de head, de header, het menu, de footer en
    de cookiebanner. De pagina zelf komt in $slot.

    Een pagina uit het CMS geeft zichzelf mee als page. De head komt dan
    uit het CMS (<x-made-cms::meta>, #23): titel, omschrijving, canonical,
    noindex en de Open Graph-tags met de deelafbeelding. Typte Romy geen
    omschrijving, dan vult de site er een in waar ze die kent: de prijzen
    op /diensten en de intro van een artikel (App\Support\FallbackDescription).

    Een pagina buiten het CMS (een foutpagina, de stijlgids) geeft de
    waarden los mee. title en description zijn de <title> en de meta
    description. ogTitle is de titel van een gedeelde link, ogDescription
    valt terug op description, en ogImage is het pad van een foto voor
    Vite::asset(). Met noindex vraagt de pagina zoekmachines haar over te
    slaan. Een canonical en og:url staan er alleen als de pagina een
    canonical meegeeft.
--}}
@use('App\Support\FallbackDescription')
@props([
    'page' => null,
    'title' => null,
    'description' => null,
    'ogTitle' => null,
    'ogDescription' => null,
    'ogImage' => 'resources/images/paar-landschap.jpg',
    'ogType' => 'website',
    'canonical' => null,
    'noindex' => false,
])
@php
    $fallbackDescription = $page !== null && blank($page->meta_description)
        ? app(FallbackDescription::class)->forPage($page)
        : null;
@endphp
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    @if ($page !== null)
        <x-made-cms::meta :page="$page" />
        @if ($fallbackDescription !== null)
            <meta name="description" content="{{ $fallbackDescription }}">
            <meta property="og:description" content="{{ $fallbackDescription }}">
        @endif
    @else
        <title>{{ $title }}</title>
        <meta name="description" content="{{ $description }}">
        @if ($noindex)
            <meta name="robots" content="noindex">
        @endif
        @if ($canonical !== null && ! $noindex)
            <link rel="canonical" href="{{ $canonical }}">
        @endif
        <meta property="og:type" content="{{ $ogType }}">
        <meta property="og:title" content="{{ $ogTitle }}">
        <meta property="og:description" content="{{ $ogDescription ?? $description }}">
        <meta property="og:image" content="{{ str_starts_with($ogImage, 'resources/') ? Vite::asset($ogImage) : $ogImage }}">
        @if ($canonical !== null && ! $noindex)
            <meta property="og:url" content="{{ $canonical }}">
        @endif
    @endif
    <meta name="theme-color" content="#F6F6F0">
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/monogram-groen.png') }}">
    {{--
        The heading and its italic accent (#39): an accent that came in late could wrap a heading that just fits
        differently. Unbounded is not preloaded: its fallback keeps the buttons and the menu in place, and a preload
        only took bandwidth from the stylesheet (measured: 80 ms on the LCP, no shift prevented).
    --}}
    <link rel="preload" href="{{ Vite::asset('resources/fonts/cormorant-latin-500-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <link rel="preload" href="{{ Vite::asset('resources/fonts/cormorant-latin-400-italic.woff2') }}" as="font" type="font/woff2" crossorigin>
    <x-consent-mode />
    <x-motion />
    <x-speculation-rules />
    @vite(['resources/css/style.css', 'resources/js/app.js'])
    @stack('jsonld')
</head>
<body>
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <symbol id="heart" viewBox="0 0 24 22"><path d="M12 21C12 21 1 14.5 1 7.2 1 3.6 3.8 1 7.1 1c2.2 0 4 1.2 4.9 3 .9-1.8 2.7-3 4.9-3C20.2 1 23 3.6 23 7.2 23 14.5 12 21 12 21z"/></symbol>
    </svg>
    <a class="skip" href="#inhoud">Naar de inhoud</a>
    <x-cookiebanner />

    <x-site-header />

    <main id="inhoud">
        {{ $slot }}
    </main>

    <x-site-footer />

    <x-whatsapp-link class="wa" aria-label="Stuur Romy een WhatsApp-bericht"><svg width="14" height="13" fill="#E0D0E0" aria-hidden="true"><use href="#heart"/></svg>WhatsApp</x-whatsapp-link>
</body>
</html>
