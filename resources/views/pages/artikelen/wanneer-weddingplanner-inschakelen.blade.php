{{--
    Het artikel "Wanneer schakel je een weddingplanner in?", opgebouwd uit
    stroken (resources/views/strips). BlogartikelController geeft de blog
    uit App\Support\Blogs mee als $blog; de kop haalt daar de titel, het
    onderwerp en de leestijd vandaan. In #20 wordt elk artikel een pagina
    in het CMS en verdwijnt dit blok.

    Het artikel is nog niet af, zoals in het ontwerp. Romy schrijft het
    verder in #27.
--}}
@use('App\Support\Strip')
@php
    $artikel = [
        new Strip('artikel-kop', 'artikel-kop', [
            'foto' => Vite::asset('resources/images/paar-landschap.jpg'),
            'alt' => 'Bruidspaar wandelt door een weids landschap',
            'sticker' => "hoe eerder,\nhoe rustiger",
            'blog' => $blog,
        ]),
        new Strip('artikel-begin', 'artikel-tekst', [
            'inleiding' => 'De meeste stellen zoeken pas hulp als het al druk is. Dat is begrijpelijk, maar juist in het begin worden de keuzes gemaakt die later rust geven.',
            'tekst' => '<p>Jullie zijn net verloofd, de champagne is op, en dan komt de vraag: waar beginnen we? Vaak is het antwoord: met een locatie zoeken, in de avonduren, naast een volle werkweek.</p><h2>Het begin <em>bepaalt de rest</em></h2><p>In de eerste maanden kiezen jullie de datum, de locatie en het budget. Dat zijn de keuzes waar alles aan vastzit. Een planner die dan al meedenkt, voorkomt dat jullie later moeten schuiven, of meer betalen dan nodig.</p>',
        ]),
        new Strip('artikel-fotos', 'artikel-fotos', [
            'fotos' => [
                ['id' => null, 'foto' => Vite::asset('resources/images/ceremonie-buiten.jpg'), 'alt' => 'Ceremonieplek buiten met witte stoelen', 'bijschrift' => 'eerst de locatie'],
                ['id' => null, 'foto' => Vite::asset('resources/images/feesttafel.jpg'), 'alt' => 'Gedekte feesttafel', 'bijschrift' => 'dan de details'],
            ],
        ]),
        new Strip('artikel-moment', 'artikel-tekst', [
            'tekst' => '<p>Voor Helemaal in handen (full service) is 12 tot 18 maanden van tevoren een fijn moment. Willen jullie alleen hulp op de dag zelf, dan is 3 tot 6 maanden ervoor vaak genoeg.</p>',
        ]),
        new Strip('artikel-citaat', 'artikel-citaat', [
            'citaat' => 'Hoe eerder jullie hulp vragen, hoe meer van de voorbereiding ook echt leuk blijft.',
        ]),
        new Strip('artikel-signalen', 'artikel-tekst', [
            'tekst' => '<h2>Drie signalen <em>dat het tijd is</em></h2><p>Jullie merken dat de bruiloft elke avond het gesprek is. Jullie familie biedt aan om dingen te regelen, en jullie weten niet goed hoe je dat vriendelijk afslaat. Of jullie hebben al drie offertes liggen en geen idee hoe je ze vergelijkt.</p><p class="todo">[Hier verder schrijven, Romy.]</p>',
        ]),
        new Strip('auteur', 'auteur', [
            'label' => 'Geschreven door Romy',
            'tekst' => 'Weddingplanner in Zuid-Holland en de planner achter vertrouwdag.',
            'link' => ['text' => 'Meer over mij', 'url' => route('over-mij')],
        ]),
    ];

    $afsluiting = [
        new Strip('kennismaking', 'kennismaking', [
            'label' => 'Zullen we kennismaken?',
            'kop' => 'Benieuwd wat het *voor jullie* kan betekenen?',
            'tekst' => 'Een half uur, online en vrijblijvend.',
        ]),
    ];
@endphp
<x-layouts::site
    title="Wanneer schakel je een weddingplanner in? | vertrouwdag"
    description="Wanneer schakel je een weddingplanner in? Voor full service het liefst 12 tot 18 maanden van tevoren. Waarom een vroeg begin juist rust oplevert."
>
    <article>
        <x-strips :strips="$artikel" />
    </article>

    <x-strips :strips="$afsluiting" />
</x-layouts::site>
