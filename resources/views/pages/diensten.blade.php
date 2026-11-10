{{--
    De dienstenpagina, opgebouwd uit stroken (resources/views/strips). De
    inhoud staat hier vast in de vorm die het CMS straks levert: per
    strook een Strip met zijn velden, en een lijst als array van items.
    In #17 komt deze inhoud uit het CMS en verdwijnt dit blok.

    De prijzen komen uit config/vertrouwdag.php, ook in de omschrijving
    voor zoekmachines. Die noemt de bedragen inclusief btw, want de
    bezoekers zijn consumenten (besluit Menno, 2026-11-10, zie #6).
--}}
@use('App\Support\Prijs')
@use('App\Support\Strip')
@php
    $strips = [
        new Strip('hero-diensten', 'hero-diensten', [
            'label' => 'Diensten en prijzen',
            'kop' => 'Zoveel hulp *als jullie* nodig hebben',
            'tekst' => 'Van alleen de dag zelf tot het hele traject. Elk pakket heeft een vaste vanaf-prijs, zodat jullie vooraf weten waar jullie aan toe zijn.',
            'knop' => ['text' => 'Vergelijk de pakketten', 'url' => '#vergelijk'],
            'bijschrift' => 'alles geregeld',
            'sticker' => "vaste\nprijzen",
        ]),
        new Strip('lopende-band', 'lopende-band', [
            'teksten' => [
                ['id' => null, 'tekst' => 'De Dag in handen'],
                ['id' => null, 'tekst' => 'Hand in hand'],
                ['id' => null, 'tekst' => 'Helemaal in handen'],
                ['id' => null, 'tekst' => 'Maatwerk mogelijk'],
            ],
        ]),
        new Strip('pakketten', 'pakketten', [
            'label' => 'Drie pakketten',
            'kop' => 'Welke map *past bij jullie?*',
            'hint' => 'Tik op een tabje of swipe door de mappen',
            'hint_mobiel' => 'Swipe of tik op een mapje om het naar voren te halen',
            'diensten' => [
                [
                    'id' => null,
                    'dienst' => 'dag',
                    'soort' => 'Dagcoördinatie',
                    'titel' => 'De Dag in handen',
                    'omschrijving' => '<strong>Voor stellen</strong> die zelf graag plannen, maar op de dag niets meer willen regelen.',
                    'kenmerken' => "Overdracht zes weken voor de bruiloft\nDraaiboek en tijdsplanning\nAfstemming met alle leveranciers\nAanwezig van opbouw tot laatste dans",
                    'bijschrift' => 'op de dag zelf',
                ],
                [
                    'id' => null,
                    'dienst' => 'partial',
                    'soort' => 'Gedeeltelijke planning',
                    'titel' => 'Hand in hand',
                    'omschrijving' => '<strong>Voor stellen</strong> die de locatie al hebben en hulp willen bij de rest.',
                    'kenmerken' => "Alles uit De Dag in handen\nLeveranciers voorstellen en offertes vergelijken\nPlanning en budgetoverzicht\nDrie vaste overlegmomenten",
                    'bijschrift' => 'hand in hand',
                ],
                [
                    'id' => null,
                    'dienst' => 'full',
                    'soort' => 'Full service',
                    'titel' => 'Helemaal in handen',
                    'omschrijving' => '<strong>Voor stellen</strong> die bij het begin staan en alles in vertrouwde handen willen leggen.',
                    'kenmerken' => "Alles uit Hand in hand\nLocaties zoeken en samen bezichtigen\nConcept en styling uitwerken\nOnbeperkt contact, ook de dag erna",
                    'bijschrift' => 'van begin tot eind',
                ],
            ],
        ]),
        new Strip('vergelijking', 'vergelijking', [
            'label' => 'Naast elkaar',
            'kop' => 'Wat zit *waarin?*',
            'sticker' => "maatwerk\nmogelijk",
            'kolommen' => [
                ['id' => null, 'dienst' => 'dag', 'titel' => 'De Dag in handen'],
                ['id' => null, 'dienst' => 'partial', 'titel' => 'Hand in hand'],
                ['id' => null, 'dienst' => 'full', 'titel' => 'Helemaal in handen'],
            ],
            'onderdelen' => [
                ['id' => null, 'onderdeel' => 'Coördinatie op de dag zelf', 'dag' => 'ja', 'partial' => 'ja', 'full' => 'ja'],
                ['id' => null, 'onderdeel' => 'Draaiboek en tijdsplanning', 'dag' => 'ja', 'partial' => 'ja', 'full' => 'ja'],
                ['id' => null, 'onderdeel' => 'Leveranciers zoeken en boeken', 'dag' => 'nee', 'partial' => 'deels', 'full' => 'ja'],
                ['id' => null, 'onderdeel' => 'Budgetoverzicht', 'dag' => 'nee', 'partial' => 'ja', 'full' => 'ja'],
                ['id' => null, 'onderdeel' => 'Locatie zoeken', 'dag' => 'nee', 'partial' => 'nee', 'full' => 'ja'],
                ['id' => null, 'onderdeel' => 'Concept en styling', 'dag' => 'nee', 'partial' => 'nee', 'full' => 'ja'],
            ],
            'prijs_label' => 'Vanaf, incl. btw',
            'slotzin' => 'Past geen pakket precies? In het kennismakingsgesprek kijken we samen wat jullie nodig hebben.',
        ]),
        new Strip('cta', 'cta', [
            'kop' => 'Twijfelen jullie *welk pakket?*',
            'tekst' => 'Dat zoeken we samen uit, in een vrijblijvend gesprek van een half uur.',
            'variant' => 'groot',
        ]),
    ];

    $omschrijving = sprintf(
        'De Dag in handen (dagcoördinatie) vanaf %s, Hand in hand (gedeeltelijke planning) vanaf %s en Helemaal in handen (full service) vanaf %s, inclusief btw. Vaste vanaf-prijzen, zodat jullie vooraf weten waar jullie aan toe zijn.',
        Prijs::voorDienst('dag')->inclBtw(),
        Prijs::voorDienst('partial')->inclBtw(),
        Prijs::voorDienst('full')->inclBtw(),
    );
@endphp
<x-layouts::site
    title="Diensten en prijzen | vertrouwdag weddingplanner"
    :description="$omschrijving"
    og-title="Diensten en prijzen · vertrouwdag"
    og-description="Weddingplanner in Zuid-Holland. Warm en persoonlijk, en tegelijk strak georganiseerd."
    og-image="resources/images/ceremonie-buiten.jpg"
>
    <x-strips :strips="$strips" />
</x-layouts::site>
