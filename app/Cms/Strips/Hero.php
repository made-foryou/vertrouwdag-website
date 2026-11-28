<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Button;
use Made\CmsPackage\Content\Strips\Fields\Choice;
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
 *
 * Since the strips were merged (2026-11-28) this is the one opening with an arch and a polaroid, called "Opening
 * met foto's" in the cms. The variant gives the composition of the homepage, of /diensten (the old hero-diensten:
 * no stamp, the sticker on top) or of /over-mij (the old hero-over-mij: the photos on the left, the stamp, one
 * button). Those strips were moved to this key with their variant by a migration; their values were already fields
 * of this type. Over mij shows only the field knop, as its one primary button, and no sticker; the hints say so.
 */
final class Hero extends StripType
{
    public function key(): string
    {
        return 'hero';
    }

    public function label(): string
    {
        return 'Opening met foto\'s';
    }

    public function description(): string
    {
        return 'Grote opening met kop, tekst, knoppen en twee foto\'s: een boog en een polaroid.';
    }

    public function fields(): array
    {
        return [
            Choice::make('variant', [
                'home' => 'Homepage: de foto\'s rechts, met het zegel en een sticker',
                'diensten' => 'Diensten: de foto\'s rechts, met een sticker',
                'over-mij' => 'Over mij: de foto\'s links, met het zegel en één knop',
            ])
                ->label('Uiterlijk')
                ->required()
                ->default('home'),
            Text::make('label')
                ->default('Weddingplanner in Zuid-Holland')
                ->hint('Korte regel boven de kop. Bij Homepage breekt het laatste woord niet af.'),
            Velden::kopMetAccent()
                ->tag('h1')
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
                ->hint('Leeg: de knop naar de kennismaking, zoals in de header. Niet bij Over mij.'),
            Button::make('knop')
                ->label('Tweede knop')
                ->default(['text' => 'Bekijk de diensten', 'link' => ['url' => '/diensten']])
                ->hint('Naast de eerste knop. Bij Over mij de enige knop.'),
            Velden::foto('foto_boog', 'Staand: de boog snijdt hem bij.')
                ->label('Foto in de boog'),
            Velden::foto('foto_polaroid', 'Vierkant: de polaroid snijdt hem bij.')
                ->label('Foto op de polaroid'),
            Text::make('bijschrift')
                ->label('Bijschrift bij de polaroid')
                ->default('ja, wij doen het'),
            LongText::make('sticker')
                ->default("met\nhart en\nhoofd")
                ->hint('Elke regel staat op de sticker op een eigen regel. Niet bij Over mij.'),
        ];
    }
}
