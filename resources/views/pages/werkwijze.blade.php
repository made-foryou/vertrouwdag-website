{{--
    De werkwijze, opgebouwd uit stroken (resources/views/strips). De
    inhoud staat hier vast in de vorm die het CMS straks levert: per
    strook een Strip met zijn velden, en een lijst als array van items.
    In #17 komt deze inhoud uit het CMS en verdwijnt dit blok.

    De fases lopen over drie stroken: de tijdlijn met fase 1 tot en met
    4, de trouwdag als fase 5 en "Afronden" als fase 6. De nummers zijn
    velden, zodat de nummering doorloopt.
--}}
@use('App\Support\Strip')
@php
    $strips = [
        new Strip('hero-werkwijze', 'hero-werkwijze', [
            'label' => 'Werkwijze',
            'kop' => 'Van eerste koffie *tot de dag erna*',
            'tekst' => 'Zo ziet een traject eruit bij Helemaal in handen, mijn full service. Bij de andere pakketten stap ik later in, met dezelfde rust en hetzelfde overzicht.',
            'knop' => ['text' => 'Bekijk de pakketten', 'url' => route('diensten')],
            'bijschrift_boven' => 'stap voor stap',
            'bijschrift_onder' => 'tot het ja-woord',
        ]),
        new Strip('tijdlijn', 'tijdlijn', [
            'variant' => 'band',
            'fases' => [
                [
                    'id' => null,
                    'periode' => '12 tot 18 maanden ervoor',
                    'titel' => 'Kennismaken en voorstel',
                    'tekst' => 'We beginnen met een kennismaking, online en met een kop koffie erbij. Jullie vertellen over jullie plannen, de sfeer die jullie voor ogen hebben en wat jullie echt belangrijk vinden. Binnen een week stuur ik een helder voorstel met een vaste prijs, zodat jullie precies weten waar jullie aan toe zijn.',
                ],
                [
                    'id' => null,
                    'periode' => '10 tot 12 maanden ervoor',
                    'titel' => 'Locatie, datum en budget',
                    'tekst' => 'Ik zoek een handvol locaties die echt bij jullie passen en plan de bezichtigingen. Samen zetten we het budget op papier, en ik maak een planning waarin alles op het juiste moment gebeurt. Zo staat de basis, en dat geeft rust.',
                ],
                [
                    'id' => null,
                    'periode' => '4 tot 10 maanden ervoor',
                    'titel' => 'Leveranciers en styling',
                    'tekst' => 'Ik vraag offertes op, vergelijk ze en zet de beste opties voor jullie klaar. Jullie hoeven alleen nog te proeven, te passen en te kiezen: de leuke beslissingen. Daarna boek ik alles en houd ik elke afspraak bij.',
                ],
                [
                    'id' => null,
                    'periode' => '6 weken ervoor',
                    'titel' => 'Het draaiboek',
                    'tekst' => 'Zes weken voor de bruiloft maak ik het draaiboek, altijd zelf en tot op de minuut. We lopen het samen door, zodat al jullie laatste wensen erin staan. Daarna stem ik met iedere leverancier af wie wanneer waar is.',
                ],
            ],
        ]),
        new Strip('trouwdag', 'trouwdag', [
            'nummer' => 5,
            'label' => 'Jullie trouwdag',
            'kop' => 'Wat jullie familie *die dag doet*',
            'briefjes' => [
                ['id' => null, 'wie' => 'Jullie ouders', 'tekst' => 'Genieten. Verder niets.'],
                ['id' => null, 'wie' => 'De getuigen', 'tekst' => 'Een speech en een proost.'],
                ['id' => null, 'wie' => 'Jullie', 'tekst' => 'Ja zeggen, en de rest van de dag dansen.'],
                ['id' => null, 'wie' => 'Ik', 'tekst' => 'Alles wat jullie niet hoeven te merken.'],
            ],
            'slotzin' => 'Ik ben er van de opbouw tot de laatste dans. Leveranciers, vragen en onverwachte dingen komen bij mij terecht.',
        ]),
        new Strip('tijdlijn-afronden', 'tijdlijn', [
            'eerste_nummer' => 6,
            'fases' => [
                [
                    'id' => null,
                    'periode' => 'De dag erna',
                    'titel' => 'Afronden',
                    'tekst' => 'Ook de dag erna is geregeld. Vooraf spreek ik af wie wat opruimt en terugbrengt: de leveranciers zelf, de locatiemanager of één vaste persoon uit jullie kring. Zo weet iedereen precies wat er van hem verwacht wordt. Het enige wat jullie nog hoeven te doen: nagenieten.',
                ],
            ],
        ]),
        new Strip('cta', 'cta', [
            'kop' => 'Het begint met *een gesprek*',
            'tekst' => 'Een half uur, online en vrijblijvend.',
            'variant' => 'eenvoudig',
        ]),
    ];
@endphp
<x-layouts::site
    title="Werkwijze: zo plan ik jullie bruiloft | vertrouwdag"
    description="Van het eerste kennismakingsgesprek tot de dag na de bruiloft: zo werk ik als weddingplanner in Zuid-Holland, stap voor stap."
>
    <x-strips :strips="$strips" />
</x-layouts::site>
