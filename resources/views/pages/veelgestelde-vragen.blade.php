{{--
    De veelgestelde vragen, opgebouwd uit stroken (resources/views/strips).
    De inhoud staat hier vast in de vorm die het CMS straks levert: per
    strook een Strip met zijn velden, en een lijst als array van items.
    In #18 komt deze inhoud uit het CMS en verdwijnt dit blok.

    Een antwoord is opgemaakte tekst, zoals RichText die levert. De
    prijzen erin komen uit config/vertrouwdag.php via <x-prijs>, en de
    links uit de routes. De strook faq maakt van dezelfde vragen de
    JSON-LD (FAQPage).

    De omschrijving wijkt af van het ontwerp, waar een verhaspelde zin
    stond ("de vragen die stellen mij het vaakst stellen"). Romy bevestigt
    de nieuwe tekst in #27.
--}}
@use('App\Support\Strip')
@use('Illuminate\Support\Facades\Blade')
@php
    $prijs = fn (string $dienst): string => trim(Blade::render('<x-prijs :dienst="$dienst" />', ['dienst' => $dienst]));
    $btw = config('vertrouwdag.btw_percentage');

    $strips = [
        new Strip('hero-faq', 'hero-faq', [
            'label' => 'Veelgestelde vragen',
            'kop' => 'Goed *om te weten*',
            'tekst' => 'Staat jullie vraag er niet tussen? Stel hem gerust in het kennismakingsgesprek, of',
            'app_tekst' => 'stuur me een appje',
            'briefjes' => [
                ['id' => null, 'tekst' => '"Wat als het regent?"'],
                ['id' => null, 'tekst' => '"Wanneer moeten we beginnen?"'],
                ['id' => null, 'tekst' => '"Wat kost het?"'],
            ],
        ]),
        new Strip('faq', 'faq', [
            'label' => 'Alle vragen',
            'vragen' => [
                [
                    'id' => null,
                    'vraag' => 'Wanneer schakelen we een weddingplanner in?',
                    'antwoord' => '<p>Voor Helemaal in handen (full service) het liefst 12 tot 18 maanden van tevoren, zodat we de locatie en datum samen kunnen kiezen. Voor De Dag in handen (dagcoördinatie) is 3 tot 6 maanden ervoor vaak genoeg. Hoe eerder, hoe meer rust.</p>',
                ],
                [
                    'id' => null,
                    'vraag' => 'Wat kost een weddingplanner?',
                    'antwoord' => '<p>De Dag in handen (dagcoördinatie) begint bij '.$prijs('dag').', Hand in hand (gedeeltelijke planning) bij '.$prijs('partial').' en Helemaal in handen (full service) bij '.$prijs('full').', steeds inclusief '.$btw.'% btw. Na de kennismaking krijgen jullie een voorstel met een vaste prijs. <a href="'.e(route('diensten')).'">Bekijk wat er in elk pakket zit</a>.</p>',
                ],
                [
                    'id' => null,
                    'vraag' => 'Wat is het verschil tussen de pakketten?',
                    'antwoord' => '<p>Bij De Dag in handen plannen jullie zelf en neem ik het zes weken voor de bruiloft over. Bij Hand in hand help ik ook met leveranciers en budget. Bij Helemaal in handen doen we alles samen, vanaf het allereerste begin.</p>',
                ],
                [
                    'id' => null,
                    'vraag' => 'Werk je ook buiten Zuid-Holland?',
                    'antwoord' => '<p>Mijn thuisbasis is Zuid-Holland, van Rotterdam tot de Hoeksche Waard. Trouwen jullie net daarbuiten, dan kijk ik graag of het past. Reiskosten spreken we vooraf af.</p>',
                ],
                [
                    'id' => null,
                    'vraag' => 'Wat als het regent?',
                    'antwoord' => '<p>Er is altijd een plan B, en dat staat al in het draaiboek voordat het nodig is. Op de dag zelf beslis ik samen met de locatie, zodat jullie er niet mee bezig hoeven te zijn.</p>',
                ],
                [
                    'id' => null,
                    'vraag' => 'Hoe gaat het kennismakingsgesprek?',
                    'antwoord' => '<p>Online, ongeveer 30 minuten, en vrijblijvend. Jullie vertellen over jullie plannen, ik stel vragen en vertel hoe ik werk. Liever fysiek afspreken kan ook. <a href="'.e(route('kennismaken')).'">Plan direct een moment</a>.</p>',
                ],
                [
                    'id' => null,
                    'vraag' => 'Werk je met vaste leveranciers?',
                    'antwoord' => '<p>Ik ken veel goede leveranciers in de regio en stel ze voor als ze bij jullie passen. Jullie zitten nergens aan vast.</p>',
                ],
                [
                    'id' => null,
                    'vraag' => 'Moet onze familie nog iets doen op de dag zelf?',
                    'antwoord' => '<p>Nee. Leveranciers, vragen en onverwachte dingen komen bij mij terecht. Jullie ouders, getuigen en vrienden zijn gewoon gast.</p>',
                ],
                [
                    'id' => null,
                    'vraag' => 'Hoeveel contact hebben we tijdens de voorbereiding?',
                    'antwoord' => '<p>We spreken vaste overlegmomenten af, en tussendoor kunnen jullie mij mailen of appen. Jullie weten altijd wat de volgende stap is.</p>',
                ],
                [
                    'id' => null,
                    'vraag' => 'Kunnen we een pakket aanpassen?',
                    'antwoord' => '<p>Ja, maatwerk is mogelijk. In het kennismakingsgesprek kijken we wat jullie nodig hebben, en dat zet ik in het voorstel.</p>',
                ],
                [
                    'id' => null,
                    'vraag' => 'Hoe groot mag onze bruiloft zijn?',
                    'antwoord' => '<p>Ik ben gespecialiseerd in persoonlijke, intieme bruiloften, maar ook een groter feest regel ik met dezelfde aandacht voor detail.</p>',
                ],
            ],
        ]),
        new Strip('cta', 'cta', [
            'kop' => 'Nog een *vraag?*',
            'tekst' => 'Die beantwoord ik graag in een vrijblijvend gesprek.',
            'variant' => 'middel',
        ]),
    ];
@endphp
<x-layouts::site
    title="Veelgestelde vragen over een weddingplanner | vertrouwdag"
    description="Wat kost een weddingplanner, wanneer schakel je er een in en wat als het regent? Antwoorden op de vragen die bruidsparen mij het vaakst stellen."
    og-title="Veelgestelde vragen over een weddingplanner · vertrouwdag"
    og-image="resources/images/hand-in-hand.jpg"
>
    <x-strips :strips="$strips" />
</x-layouts::site>
