<?php

namespace Database\Seeders;

use Made\CmsPackage\Content\Actions\MovePage;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Models\Page;

/**
 * Zet /blog in het CMS (#19), zodat Blog in het menu staat. Het menu komt
 * uit de paginaboom. De pagina heeft geen stroken: de vaste routes van de
 * blog in routes/web.php gaan voor, tot de blogs uit het CMS komen (#20).
 * Dan is ze de ouder van de artikelen.
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

    protected function stroken(): array
    {
        return [];
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
