<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Button;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook hero in het CMS: de opening van de homepage met kop, tekst en
 * twee knoppen, en rechts het portret van Romy. Hij rendert met de view
 * resources/views/strips/hero.blade.php.
 *
 * Beide knoppen zijn velden. De eerste begint als de knop naar de
 * kennismaking uit config/vertrouwdag.php, en is dat ook weer als hij leeg
 * is; anders dan in de header en de andere stroken kan Romy hem hier dus
 * aanpassen. Een hero van vóór dit veld krijgt bij het renderen de
 * standaardwaarde. De foto's in de boog en op de polaroid zijn te kiezen;
 * leeg is de foto uit het ontwerp (#21). Het zegel en de plek van de
 * sticker liggen vast. Het label herlaadt het frame na het opslaan in plaats van
 * mee te kijken: het laatste woord staat in een eigen element, zodat
 * "Zuid-Holland" niet bij het streepje afbreekt.
 */
final class Hero extends StripType
{
    public function key(): string
    {
        return 'hero';
    }

    public function label(): string
    {
        return 'Opening van de homepage';
    }

    public function description(): string
    {
        return 'Grote opening met kop, tekst, knoppen en het portret van Romy.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Weddingplanner in Zuid-Holland')
                ->hint('Korte regel boven de kop. Het laatste woord breekt niet af.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Jullie dag in *vertrouwde* handen'),
            LongText::make('tekst')
                ->default('Jullie werken allebei hard. Ik neem het overzicht over, zodat jullie en jullie familie op de dag zelf alleen maar hoeven te genieten.'),
            Button::make('eerste_knop')
                ->label('Eerste knop')
                ->default([
                    'text' => config('vertrouwdag.cta.label'),
                    'link' => ['url' => config('vertrouwdag.cta.pad')],
                ])
                ->hint('Leeg: de knop naar de kennismaking, zoals in de header.'),
            Button::make('knop')
                ->label('Tweede knop')
                ->default(['text' => 'Bekijk de diensten', 'link' => ['url' => '/diensten']])
                ->hint('Naast de eerste knop.'),
            Velden::foto('foto_boog', 'Staand: de boog snijdt hem bij.')
                ->label('Foto in de boog'),
            Velden::foto('foto_polaroid', 'Vierkant: de polaroid snijdt hem bij.')
                ->label('Foto op de polaroid'),
            Text::make('bijschrift')
                ->label('Bijschrift bij de polaroid')
                ->default('ja, wij doen het'),
            LongText::make('sticker')
                ->default("met\nhart en\nhoofd")
                ->hint('Elke regel staat op de sticker op een eigen regel.'),
        ];
    }
}
