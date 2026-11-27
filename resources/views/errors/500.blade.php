{{--
    De 500: er ging iets mis op de server. Dit is bewust een eigen, kale
    pagina zonder de layout van de site. Gaat er iets mis in de layout
    zelf, dan zou een 500 in die layout opnieuw stuklopen. Daarom staan
    hier alleen het logo, de foutmelding met het mailadres uit de config
    en een link naar de homepage. Zoekmachines slaan de pagina over
    (noindex).
--}}
<!doctype html>
<html lang="nl">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Er ging iets mis | vertrouwdag</title>
    <meta name="robots" content="noindex">
    <meta name="theme-color" content="#F6F6F0">
    <link rel="icon" type="image/png" href="{{ Vite::asset('resources/images/monogram-groen.png') }}">
    @vite('resources/css/style.css')
</head>
<body>
    <svg width="0" height="0" style="position:absolute" aria-hidden="true">
        <symbol id="heart" viewBox="0 0 24 22"><path d="M12 21C12 21 1 14.5 1 7.2 1 3.6 3.8 1 7.1 1c2.2 0 4 1.2 4.9 3 .9-1.8 2.7-3 4.9-3C20.2 1 23 3.6 23 7.2 23 14.5 12 21 12 21z"/></symbol>
    </svg>
    <header class="site-header">
        <div class="site-header__in">
            <a class="site-header__logo" href="{{ url('/') }}" aria-label="vertrouwdag, naar de homepage"><x-brand-image name="logo-groen" sizes="(max-width:1040px) 92px, 112px" alt="vertrouwdag" /></a>
        </div>
    </header>

    <main id="inhoud">
        <x-foutmelding label="Storing" kop="Er ging iets *mis*">
            Dat ligt aan mij, niet aan jullie. Probeer het over een paar minuten opnieuw. Blijft het misgaan? Mail me op <x-mail-link>{{ config('vertrouwdag.email') }}</x-mail-link>.
            <x-slot:knoppen>
                <x-knop :href="url('/')">Naar de homepage</x-knop>
            </x-slot:knoppen>
        </x-foutmelding>
    </main>
</body>
</html>
