<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Button;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook hero-diensten in het CMS: de opening van /diensten met kop,
 * tekst en twee knoppen, en rechts de ceremonieplek, een polaroid en de
 * sticker. Hij rendert met de view
 * resources/views/strips/hero-diensten.blade.php.
 *
 * De eerste knop is de kennismaking uit config/vertrouwdag.php; de tweede
 * is het veld knop. De foto's in de boog en op de polaroid zijn te kiezen;
 * leeg is de foto uit het ontwerp (#21).
 */
final class HeroDiensten extends StripType
{
    public function key(): string
    {
        return 'hero-diensten';
    }

    public function label(): string
    {
        return 'Opening van Diensten';
    }

    public function description(): string
    {
        return 'Opening met kop, tekst, knoppen, de ceremonieplek en een sticker.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Diensten en prijzen')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->tag('h1')
                ->required()
                ->default('Zoveel hulp *als jullie* nodig hebben'),
            LongText::make('tekst')
                ->default('Van alleen de dag zelf tot het hele traject. Elk pakket heeft een vaste vanaf-prijs, zodat jullie vooraf weten waar jullie aan toe zijn.'),
            Button::make('knop')
                ->label('Tweede knop')
                ->default(['text' => 'Vergelijk de pakketten', 'link' => ['url' => '#vergelijk']])
                ->hint('Naast de knop naar de kennismaking. #vergelijk springt naar de vergelijking.'),
            Velden::foto('foto_boog', 'Staand: de boog snijdt hem bij.')
                ->label('Foto in de boog'),
            Velden::foto('foto_polaroid', 'Vierkant: de polaroid snijdt hem bij.')
                ->label('Foto op de polaroid'),
            Text::make('bijschrift')
                ->label('Bijschrift bij de polaroid')
                ->default('alles geregeld'),
            LongText::make('sticker')
                ->default("vaste\nprijzen")
                ->hint('Elke regel staat op de sticker op een eigen regel.'),
        ];
    }
}
