<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use App\Support\Blog;
use App\Support\Blogcategorie;
use App\Support\Blogs;
use Made\CmsPackage\Content\Strips\Fields\Button;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\Image;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook blog-laatste in het CMS: de artikelen na het uitgelichte als
 * polaroids, met daarna de blogs die binnenkort komen, en een knop naar
 * het archief. Hij rendert met de view
 * resources/views/strips/blog-laatste.blade.php.
 *
 * De artikelen komen uit App\Support\Blogs. Een blog die binnenkort komt
 * is geen pagina, want een gepubliceerde pagina is te openen. Hij staat
 * daarom in de lijst binnenkort van deze strook (besluit Menno,
 * 2026-11-14, zie #20). Staat het artikel online, dan haalt Romy het
 * kaartje weg.
 *
 * De standaardwaarden zijn de kaartjes uit het ontwerp, zonder foto: een
 * foto heeft geen standaardwaarde.
 */
final class BlogLaatste extends StripType
{
    public function key(): string
    {
        return 'blog-laatste';
    }

    public function label(): string
    {
        return 'Blog: laatste artikelen';
    }

    public function description(): string
    {
        return 'De drie artikelen na het nieuwste, de blogs die binnenkort komen en een knop naar het archief.';
    }

    public function fields(): array
    {
        return [
            Items::make('binnenkort', [
                Velden::kopMetAccent('titel')
                    ->label('Titel')
                    ->required(),
                LongText::make('intro')
                    ->required(),
                Choice::make('categorie', Blogcategorie::class)
                    ->label('Onderwerp')
                    ->required(),
                Image::make('foto')
                    ->required(),
            ])
                ->itemLabel('Blog die binnenkort komt')
                ->max(3)
                ->default([
                    ['titel' => 'Landelijk-romantisch trouwen in Zuid-Holland: waar begin je?', 'intro' => 'Van boerderij tot buitenplaats, dicht bij huis.', 'categorie' => Blogcategorie::Locaties->value],
                    ['titel' => 'Waarom jullie familie op jullie trouwdag niets hoeft te regelen', 'intro' => 'Zo mogen jullie ouders en getuigen vooral gast zijn.', 'categorie' => Blogcategorie::AchterDeSchermen->value],
                    ['titel' => '[Volgend artikel]', 'intro' => 'Hier komt jullie volgende blog.', 'categorie' => Blogcategorie::Budget->value],
                ])
                ->hint('Kaartjes zonder link na de artikelen. Staat het artikel online, haal het kaartje dan weg.'),
            Button::make('knop')
                ->default(['text' => 'Bekijk alle blogs in het archief', 'link' => ['url' => Blogs::ARCHIEF]]),
        ];
    }

    /**
     * De kaarten: eerst de artikelen na het uitgelichte, dan wat binnenkort
     * komt. Een kaartje zonder titel of onderwerp (in een concept) blijft
     * weg.
     *
     * @return array{blogs: list<Blog>}
     */
    public function with(array $values): array
    {
        $binnenkort = [];

        foreach ($values['binnenkort'] ?? [] as $item) {
            $blog = is_array($item) ? Blog::uitVelden($item, binnenkort: true) : null;

            if ($blog !== null) {
                $binnenkort[] = $blog;
            }
        }

        return ['blogs' => [...app(Blogs::class)->laatste(), ...$binnenkort]];
    }
}
