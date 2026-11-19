<?php

namespace Database\Seeders;

use Made\CmsPackage\Content\Actions\MovePage;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Models\Page;

/**
 * Zet de homepage in het CMS (#16): de stroken in de volgorde van het
 * ontwerp (#5), vooraan in de paginaboom en niet in het menu (het logo
 * linkt al naar home). Hoe dat gaat, staat in PaginaSeeder.
 *
 * CreatePage maakt geen pagina op /, daarom bouwt deze seeder haar zelf.
 */
class HomepaginaSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return PagePath::HOME;
    }

    protected function titel(): string
    {
        return 'Home';
    }

    protected function metaTitle(): ?string
    {
        return 'Jullie dag in vertrouwde handen | vertrouwdag';
    }

    protected function metaDescription(): ?string
    {
        return 'Weddingplanner in Zuid-Holland. De Dag in handen, Hand in hand en Helemaal in handen: van dagcoördinatie tot full service, zodat jullie en jullie familie op de dag zelf alleen maar hoeven te genieten.';
    }

    protected function stroken(): array
    {
        return [
            ['hero'],
            ['lopende-band'],
            ['herkenning'],
            ['twee-kolommen'],
            ['diensten'],
            ['stappen'],
            ['over-mij-intro'],
            ['reviews'],
            ['leveranciers'],
            ['checklist-aanmelding'],
            ['cta'],
        ];
    }

    protected function maakPagina(PagePath $adres): Page
    {
        $page = Page::query()->create(['title' => $this->titel(), 'slug' => 'home', 'in_menu' => false]);
        $page->route()->create(['path' => $adres->value]);
        MovePage::run($page, parentId: null, position: 0);

        return $page;
    }
}
