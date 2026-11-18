<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook hero-blog in het CMS: de opening van /blog, met chips die het
 * archief openen op een onderwerp. Hij rendert met de view
 * resources/views/strips/hero-blog.blade.php. De chips komen uit
 * App\Support\Blogcategorie.
 */
final class HeroBlog extends StripType
{
    public function key(): string
    {
        return 'hero-blog';
    }

    public function label(): string
    {
        return 'Opening van de blog';
    }

    public function description(): string
    {
        return 'Opening met kop, tekst en de onderwerpen als links naar het archief.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Blog')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->tag('h1')
                ->required()
                ->default('Rustig *plannen*'),
            LongText::make('tekst')
                ->default('Praktische tips, locaties in Zuid-Holland en een kijkje achter de schermen.'),
        ];
    }
}
