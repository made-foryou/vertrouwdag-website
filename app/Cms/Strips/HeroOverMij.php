<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Button;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook hero-over-mij in het CMS: de opening van /over-mij met Romy in
 * een boog, het zegel en een polaroid, en de kop, tekst en één knop. Hij
 * rendert met de view resources/views/strips/hero-over-mij.blade.php.
 *
 * De foto's en het zegel liggen vast tot #21.
 */
final class HeroOverMij extends StripType
{
    public function key(): string
    {
        return 'hero-over-mij';
    }

    public function label(): string
    {
        return 'Opening van Over mij';
    }

    public function description(): string
    {
        return 'Opening met Romy in een boog, kop, tekst en één knop.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Over mij')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Hoi, ik ben *Romy*'),
            LongText::make('tekst')
                ->default('De planner achter vertrouwdag. Ik woon in Zuid-Holland, houd van een goed gedekte tafel en word blij van een planning die klopt.'),
            Button::make('knop')
                ->default(['text' => 'Maak kennis met mij', 'link' => ['url' => '/kennismaken']]),
            Text::make('bijschrift')
                ->label('Bijschrift bij de polaroid')
                ->default('hier word ik blij van'),
        ];
    }
}
