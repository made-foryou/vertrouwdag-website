<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook hero-archief in het CMS: de opening van het blogarchief, met
 * een kruimelpad en chips die de blogs eronder op onderwerp filteren. Hij
 * rendert met de view resources/views/strips/hero-archief.blade.php. De
 * chips komen uit App\Support\Blogcategorie.
 */
final class HeroArchief extends StripType
{
    public function key(): string
    {
        return 'hero-archief';
    }

    public function label(): string
    {
        return 'Opening van het blogarchief';
    }

    public function description(): string
    {
        return 'Opening met kruimelpad, kop, tekst en een filter op onderwerp.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Blogarchief')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->tag('h1')
                ->required()
                ->default('Alle *blogs*'),
            LongText::make('tekst')
                ->default('Alles wat ik schreef over rustig plannen, mooie plekken in Zuid-Holland en het werk achter de schermen.'),
        ];
    }
}
