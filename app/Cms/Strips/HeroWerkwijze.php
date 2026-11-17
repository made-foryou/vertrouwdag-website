<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Button;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook hero-werkwijze in het CMS: de opening van /werkwijze met kop,
 * tekst en twee knoppen, en rechts twee polaroids. Hij rendert met de view
 * resources/views/strips/hero-werkwijze.blade.php.
 *
 * De eerste knop is de kennismaking uit config/vertrouwdag.php; de tweede
 * is het veld knop. De foto's op de polaroids zijn te kiezen; leeg is de
 * foto uit het ontwerp (#21).
 */
final class HeroWerkwijze extends StripType
{
    public function key(): string
    {
        return 'hero-werkwijze';
    }

    public function label(): string
    {
        return 'Opening van Werkwijze';
    }

    public function description(): string
    {
        return 'Opening met kop, tekst, knoppen en twee polaroids.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Werkwijze')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Van eerste koffie *tot de dag erna*'),
            LongText::make('tekst')
                ->default('Zo ziet een traject eruit bij Helemaal in handen, mijn full service. Bij de andere pakketten stap ik later in, met dezelfde rust en hetzelfde overzicht.'),
            Button::make('knop')
                ->label('Tweede knop')
                ->default(['text' => 'Bekijk de pakketten', 'link' => ['url' => '/diensten']])
                ->hint('Naast de knop naar de kennismaking.'),
            Velden::foto('foto_boven', 'Vierkant: de polaroid snijdt hem bij.')
                ->label('Foto op de bovenste polaroid'),
            Velden::foto('foto_onder', 'Vierkant: de polaroid snijdt hem bij.')
                ->label('Foto op de onderste polaroid'),
            Text::make('bijschrift_boven')
                ->label('Bijschrift bij de bovenste polaroid')
                ->default('stap voor stap'),
            Text::make('bijschrift_onder')
                ->label('Bijschrift bij de onderste polaroid')
                ->default('tot het ja-woord'),
        ];
    }
}
