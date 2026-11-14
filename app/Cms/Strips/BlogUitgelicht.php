<?php

namespace App\Cms\Strips;

use App\Support\Blog;
use App\Support\Blogs;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook blog-uitgelicht in het CMS: het nieuwste artikel, groot, als
 * link naar het artikel. Hij rendert met de view
 * resources/views/strips/blog-uitgelicht.blade.php. Het artikel komt uit
 * App\Support\Blogs; zonder artikel blijft de strook leeg.
 */
final class BlogUitgelicht extends StripType
{
    public function key(): string
    {
        return 'blog-uitgelicht';
    }

    public function label(): string
    {
        return 'Blog: nieuwste artikel';
    }

    public function description(): string
    {
        return 'Het nieuwste artikel onder /blog, groot. Het artikel komt vanzelf uit de pagina\'s onder Blog.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Nieuwste blog')
                ->hint('Op het labeltje boven de titel.'),
            Text::make('link_tekst')
                ->label('Tekst van de link')
                ->default('Lees verder'),
        ];
    }

    /**
     * @return array{blog: ?Blog}
     */
    public function with(array $values): array
    {
        return ['blog' => app(Blogs::class)->uitgelicht()];
    }
}
