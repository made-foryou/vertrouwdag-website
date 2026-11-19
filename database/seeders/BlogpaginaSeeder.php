<?php

namespace Database\Seeders;

use Made\CmsPackage\Content\Actions\MovePage;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Models\Page;

/**
 * Zet /blog in het CMS, met de stroken uit het ontwerp (#20). De pagina is
 * de ouder van de artikelen en van het archief. De uitgelichte blog en de
 * kaarten komen uit die artikelen (App\Support\Blogs); de blogs die
 * binnenkort komen, staan in de strook blog-laatste, met hun foto in de
 * Media-bibliotheek.
 *
 * Eerst stond /blog leeg in het CMS, alleen voor het menu (#19). Zo'n lege
 * pagina krijgt de stroken alsnog (PaginaSeeder).
 *
 * In het menu staat Blog tussen Over mij en Veelgestelde vragen. Een
 * nieuwe pagina komt achteraan in de boom. Op een database waar de andere
 * pagina's al staan, zet de seeder haar daarom direct na Over mij.
 */
class BlogpaginaSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return '/blog';
    }

    protected function titel(): string
    {
        return 'Blog';
    }

    protected function metaTitle(): ?string
    {
        return 'Blog: rustig je bruiloft plannen | vertrouwdag';
    }

    protected function metaDescription(): ?string
    {
        return 'Praktische tips voor het plannen van jullie bruiloft, trouwlocaties in Zuid-Holland en een kijkje achter de schermen bij weddingplanner Romy.';
    }

    protected function stroken(): array
    {
        return [
            ['hero-blog'],
            ['blog-uitgelicht'],
            ['blog-laatste', [
                'binnenkort' => [
                    [
                        'titel' => 'Landelijk-romantisch trouwen in Zuid-Holland: waar begin je?',
                        'intro' => 'Van boerderij tot buitenplaats, dicht bij huis.',
                        'categorie' => 'locaties',
                        'foto' => $this->foto('ceremonie-buiten.jpg', 'Ceremonieplek buiten met witte stoelen'),
                    ],
                    [
                        'titel' => 'Waarom jullie familie op jullie trouwdag niets hoeft te regelen',
                        'intro' => 'Zo mogen jullie ouders en getuigen vooral gast zijn.',
                        'categorie' => 'achter-de-schermen',
                        'foto' => $this->foto('feesttafel.jpg', 'Gedekte feesttafel met bloemen'),
                    ],
                    [
                        'titel' => '[Volgend artikel]',
                        'intro' => 'Hier komt jullie volgende blog.',
                        'categorie' => 'budget',
                        'foto' => $this->foto('ringen-goud.jpg', 'Twee gouden trouwringen'),
                    ],
                ],
            ]],
            ['cta', [
                'kop' => ['text' => 'Liever direct *persoonlijk advies?*'],
                'tekst' => null,
                'variant' => 'compact',
            ]],
        ];
    }

    protected function maakPagina(PagePath $adres): Page
    {
        $page = parent::maakPagina($adres);
        $overMij = Page::query()->roots()->atPath(PagePath::fromString('/over-mij'))->first();

        if ($overMij !== null) {
            $plekVanOverMij = (int) Page::query()->roots()->whereKeyNot($page->id)->inMenuOrder()->pluck('id')->search($overMij->id);

            MovePage::run($page, parentId: null, position: $plekVanOverMij + 1);
        }

        return $page;
    }
}
