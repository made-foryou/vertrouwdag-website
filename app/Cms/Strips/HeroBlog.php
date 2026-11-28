<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook hero-blog in het CMS: de opening van /blog, met chips die het
 * archief openen op een onderwerp. Hij rendert met de view
 * resources/views/strips/hero-blog.blade.php. De chips komen uit
 * App\Support\Blogcategorie.
 *
 * Since the strips were merged (2026-11-28) the variant Archief is the old hero-archief: the opening of the blog
 * archive, with a breadcrumb and the topics as a filter on the blogs below it. Those strips were moved to this key
 * with that variant by a migration.
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
        return 'Opening met kop, tekst en de onderwerpen: als links naar het archief, of als filter in het archief.';
    }

    public function fields(): array
    {
        return [
            Choice::make('variant', [
                'blog' => 'Blog: de onderwerpen als links naar het archief',
                'archief' => 'Archief: met een kruimelpad en de onderwerpen als filter',
            ])
                ->label('Uiterlijk')
                ->required()
                ->default('blog'),
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
