@props([
    'title',
    'description',
    'canonical' => null,
])
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }}</title>
    <meta name="description" content="{{ $description }}">
    <link rel="canonical" href="{{ $canonical ?? url()->current() }}">
    <meta name="theme-color" content="#F6F6F0">
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/monogram-groen.png') }}">
    <link rel="preload" href="{{ Vite::asset('resources/fonts/cormorant-latin-500-normal.woff2') }}" as="font" type="font/woff2" crossorigin>
    <script>document.documentElement.classList.add('js')</script>
    @vite(['resources/css/style.css', 'resources/js/app.js'])
    @stack('jsonld')
</head>
<body>
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <symbol id="heart" viewBox="0 0 24 22"><path d="M12 21C12 21 1 14.5 1 7.2 1 3.6 3.8 1 7.1 1c2.2 0 4 1.2 4.9 3 .9-1.8 2.7-3 4.9-3C20.2 1 23 3.6 23 7.2 23 14.5 12 21 12 21z"/></symbol>
    </svg>
    <a class="skip" href="#inhoud">Naar de inhoud</a>

    <main id="inhoud">
        {{ $slot }}
    </main>
</body>
</html>
