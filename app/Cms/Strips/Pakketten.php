<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use App\Support\Dienst;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\RichText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook pakketten in het CMS: de drie diensten als mappen met tabjes,
 * met wat erin zit en hun vanaf-prijs. Hij rendert met de view
 * resources/views/strips/pakketten.blade.php.
 *
 * Dit is de enige plek voor de prijzen (besluit Menno, 2026-11-12, zie
 * #17). De homepage, de vergelijking, de omschrijving voor zoekmachines
 * en de veelgestelde vragen lezen ze hier via App\Support\Aanbod. De
 * prijs is tekst, want het package kent nog geen getal; de site leest er
 * een heel bedrag uit (App\Support\Prijs::uitTekst) en rekent zelf het
 * bedrag inclusief btw uit.
 *
 * Het veld dienst legt de kleur en het anker van de map vast
 * (App\Support\Dienst), en de foto zolang er geen gekozen is (#21). Het
 * anker volgt de naam niet, dus links naar #dagcoordinatie,
 * #partial-planning en #full-service blijven werken als een redacteur een
 * dienst hernoemt. De foto van een pakket staat niet op de homepage: de
 * strook diensten heeft een eigen foto per dienst. De prijs, de kenmerken en een keuze
 * herladen het frame na het opslaan: ze staan niet als tekst in de view.
 */
final class Pakketten extends StripType
{
    public function key(): string
    {
        return 'pakketten';
    }

    public function label(): string
    {
        return 'Pakketten met prijzen';
    }

    public function description(): string
    {
        return 'De drie diensten als mappen met tabjes, met wat erin zit en de prijs. De prijzen gelden voor de hele site.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Drie pakketten')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Welke map *past bij jullie?*'),
            Text::make('hint')
                ->label('Hint boven de mappen')
                ->default('Tik op een tabje of swipe door de mappen'),
            Text::make('hint_mobiel')
                ->label('Hint onder de mapjes op mobiel')
                ->default('Swipe of tik op een mapje om het naar voren te halen'),
            Items::make('diensten', [
                Choice::make('dienst', [
                    Dienst::Dag->value => 'Dagcoördinatie',
                    Dienst::Partial->value => 'Gedeeltelijke planning',
                    Dienst::Full->value => 'Full service',
                ])
                    ->required()
                    ->default(Dienst::Dag->value)
                    ->hint('Bepaalt de kleur en het anker van de map, en de foto zolang er geen gekozen is. De homepage vindt de prijs via deze keuze.'),
                Text::make('soort')
                    ->required()
                    ->hint('Korte regel boven de titel.'),
                Text::make('titel')->required(),
                RichText::make('omschrijving')->required(),
                LongText::make('kenmerken')
                    ->label('Wat erin zit')
                    ->hint('Eén punt per regel.'),
                Velden::foto('foto', 'Vierkant op de polaroid, liggend op het mapje op mobiel. Sfeer, dus zonder alt-tekst op de site.'),
                Text::make('bijschrift')
                    ->label('Bijschrift bij de polaroid'),
                Text::make('prijs')
                    ->label('Prijs excl. btw')
                    ->required()
                    ->hint('In hele euro\'s, zonder centen, zoals 1850. De site rekent het bedrag inclusief btw zelf uit. Deze prijs staat ook op de homepage en in de veelgestelde vragen.'),
            ])
                ->min(1)
                ->max(3)
                ->itemLabel('Pakket')
                ->default([
                    [
                        'dienst' => Dienst::Dag->value,
                        'soort' => 'Dagcoördinatie',
                        'titel' => 'De Dag in handen',
                        'omschrijving' => '<p><strong>Voor stellen</strong> die zelf graag plannen, maar op de dag niets meer willen regelen.</p>',
                        'kenmerken' => "Overdracht zes weken voor de bruiloft\nDraaiboek en tijdsplanning\nAfstemming met alle leveranciers\nAanwezig van opbouw tot laatste dans",
                        'bijschrift' => 'op de dag zelf',
                        'prijs' => '850',
                    ],
                    [
                        'dienst' => Dienst::Partial->value,
                        'soort' => 'Gedeeltelijke planning',
                        'titel' => 'Hand in hand',
                        'omschrijving' => '<p><strong>Voor stellen</strong> die de locatie al hebben en hulp willen bij de rest.</p>',
                        'kenmerken' => "Alles uit De Dag in handen\nLeveranciers voorstellen en offertes vergelijken\nPlanning en budgetoverzicht\nDrie vaste overlegmomenten",
                        'bijschrift' => 'hand in hand',
                        'prijs' => '1850',
                    ],
                    [
                        'dienst' => Dienst::Full->value,
                        'soort' => 'Full service',
                        'titel' => 'Helemaal in handen',
                        'omschrijving' => '<p><strong>Voor stellen</strong> die bij het begin staan en alles in vertrouwde handen willen leggen.</p>',
                        'kenmerken' => "Alles uit Hand in hand\nLocaties zoeken en samen bezichtigen\nConcept en styling uitwerken\nOnbeperkt contact, ook de dag erna",
                        'bijschrift' => 'van begin tot eind',
                        'prijs' => '3500',
                    ],
                ])
                ->hint('Elke dienst één keer. De eerste map staat open.'),
        ];
    }
}
