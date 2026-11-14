<?php

namespace App\Cms\Strips;

use App\Support\Blog;
use App\Support\Blogs;
use Made\CmsPackage\Content\Strips\Fields\Button;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook blog-archief in het CMS: alle gepubliceerde artikelen als
 * polaroids, zonder wat binnenkort komt, met een knop terug naar /blog.
 * Hij rendert met de view resources/views/strips/blog-archief.blade.php.
 * De artikelen komen uit App\Support\Blogs.
 */
final class BlogArchief extends StripType
{
    public function key(): string
    {
        return 'blog-archief';
    }

    public function label(): string
    {
        return 'Blogarchief: alle artikelen';
    }

    public function description(): string
    {
        return 'Alle artikelen onder /blog, het nieuwste eerst, met een melding als een onderwerp nog leeg is.';
    }

    public function fields(): array
    {
        return [
            Text::make('kop')
                ->default('Alle artikelen')
                ->hint('Alleen voor schermlezers, boven de kaarten.'),
            Text::make('leeg')
                ->label('Melding zonder artikelen')
                ->required()
                ->default('Over dit onderwerp heb ik nog geen blog geschreven. Binnenkort meer!'),
            Button::make('knop')
                ->default(['text' => 'Terug naar de blog', 'link' => ['url' => Blogs::ADRES]]),
        ];
    }

    /**
     * @return array{blogs: list<Blog>}
     */
    public function with(array $values): array
    {
        return ['blogs' => app(Blogs::class)->artikelen()];
    }
}
