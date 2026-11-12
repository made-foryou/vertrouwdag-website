<?php

namespace App\Cms\Strips;

use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook tijdlijn in het CMS: de fases van de werkwijze, elk met een
 * genummerd boogje en een kaart. Hij rendert met de view
 * resources/views/strips/tijdlijn.blade.php.
 *
 * Op /werkwijze staat hij twee keer: fase 1 tot en met 4, en na de
 * trouwdag (5) "Afronden" als fase 6. Het eerste nummer is daarom een
 * veld. Het is tekst, want het package kent nog geen getal; iets anders
 * dan een positief heel getal telt als 1. Het nummer en de variant
 * herladen het frame na het opslaan.
 *
 * De standaardwaarden zijn de eerste tijdlijn van /werkwijze.
 */
final class Tijdlijn extends StripType
{
    public function key(): string
    {
        return 'tijdlijn';
    }

    public function label(): string
    {
        return 'Tijdlijn met fases';
    }

    public function description(): string
    {
        return 'Genummerde fases met een periode, titel en tekst, om en om links en rechts.';
    }

    public function fields(): array
    {
        return [
            Choice::make('variant', [
                'band' => 'Op de salie band',
            ])
                ->default('band')
                ->hint('Leeg: op de lichte achtergrond.'),
            Text::make('eerste_nummer')
                ->label('Nummer van de eerste fase')
                ->default('1')
                ->hint('Een getal, zoals 6. Zo loopt de nummering door na een andere strook. Leeg: 1.'),
            Items::make('fases', [
                Text::make('periode')
                    ->required()
                    ->hint('Korte regel boven de titel, zoals: 6 weken ervoor.'),
                Text::make('titel')->required(),
                LongText::make('tekst')->required(),
            ])
                ->min(1)
                ->itemLabel('Fase')
                ->default([
                    [
                        'periode' => '12 tot 18 maanden ervoor',
                        'titel' => 'Kennismaken en voorstel',
                        'tekst' => 'We beginnen met een kennismaking, online en met een kop koffie erbij. Jullie vertellen over jullie plannen, de sfeer die jullie voor ogen hebben en wat jullie echt belangrijk vinden. Binnen een week stuur ik een helder voorstel met een vaste prijs, zodat jullie precies weten waar jullie aan toe zijn.',
                    ],
                    [
                        'periode' => '10 tot 12 maanden ervoor',
                        'titel' => 'Locatie, datum en budget',
                        'tekst' => 'Ik zoek een handvol locaties die echt bij jullie passen en plan de bezichtigingen. Samen zetten we het budget op papier, en ik maak een planning waarin alles op het juiste moment gebeurt. Zo staat de basis, en dat geeft rust.',
                    ],
                    [
                        'periode' => '4 tot 10 maanden ervoor',
                        'titel' => 'Leveranciers en styling',
                        'tekst' => 'Ik vraag offertes op, vergelijk ze en zet de beste opties voor jullie klaar. Jullie hoeven alleen nog te proeven, te passen en te kiezen: de leuke beslissingen. Daarna boek ik alles en houd ik elke afspraak bij.',
                    ],
                    [
                        'periode' => '6 weken ervoor',
                        'titel' => 'Het draaiboek',
                        'tekst' => 'Zes weken voor de bruiloft maak ik het draaiboek, altijd zelf en tot op de minuut. We lopen het samen door, zodat al jullie laatste wensen erin staan. Daarna stem ik met iedere leverancier af wie wanneer waar is.',
                    ],
                ]),
        ];
    }
}
