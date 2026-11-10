{{--
    De layout van elke pagina: de head, de header, het menu, de footer en
    de cookiebanner. De pagina zelf komt in $slot.

    title en description zijn de <title> en de meta description. De Open
    Graph-tags bepalen hoe een gedeelde link eruitziet: ogTitle is de
    korte titel ("Diensten en prijzen · vertrouwdag"), ogDescription valt
    terug op description, en ogImage is het pad van een foto voor
    Vite::asset(). ogType is website, of article voor een blog.

    De canonical en og:url wijzen naar hetzelfde pad op het canonieke
    adres uit config/vertrouwdag.php, zonder querystring, tenzij de pagina
    zelf een canonical meegeeft. Met noindex vraagt de pagina zoekmachines
    haar over te slaan en heeft ze geen canonical, zoals een foutpagina.
--}}
@props([
    'title',
    'description',
    'ogTitle',
    'ogDescription' => null,
    'ogImage' => 'resources/images/paar-landschap.jpg',
    'ogType' => 'website',
    'canonical' => null,
    'noindex' => false,
])
@php
    $canonical ??= rtrim(config('vertrouwdag.canonieke_url'), '/').'/'.ltrim(request()->path(), '/');
@endphp
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    @if ($noindex)
        <meta name="robots" content="noindex">
    @else
        <link rel="canonical" href="{{ $canonical }}">
    @endif
    <meta property="og:type" content="{{ $ogType }}">
    <meta property="og:title" content="{{ $ogTitle }}">
    <meta property="og:description" content="{{ $ogDescription ?? $description }}">
    <meta property="og:image" content="{{ Vite::asset($ogImage) }}">
    @unless ($noindex)
        <meta property="og:url" content="{{ $canonical }}">
    @endunless
    <meta name="theme-color" content="#F6F6F0">
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/monogram-groen.png') }}">
    <link rel="preload" href="{{ Vite::asset('resources/fonts/cormorant-latin-500-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <x-consent-mode />
    <script>document.documentElement.classList.add('js')</script>
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
