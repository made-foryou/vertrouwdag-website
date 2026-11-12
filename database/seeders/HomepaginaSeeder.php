<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Made\CmsPackage\Content\Actions\AddStrip;
use Made\CmsPackage\Content\Actions\MovePage;
use Made\CmsPackage\Content\Actions\PublishPage;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Models\Page;
use Made\CmsPackage\Content\Models\Route;

/**
 * Zet de homepage in het CMS: een gepubliceerde pagina op / met de
 * stroken in de volgorde van het ontwerp (#5), vooraan in de paginaboom
 * en niet in het menu (het logo linkt al naar home).
 *
 * Elke strook krijgt de standaardwaarden van zijn type, en die zijn de
 * inhoud van de homepage (app/Cms/Strips). Zo staat de tekst op één plek.
 *
 * Staat er al een pagina op /, dan doet de seeder niets: wat een
 * redacteur wijzigde, gaat nooit verloren (besluit Menno, 2026-11-11,
 * zie #16). Opnieuw beginnen kan lokaal met migrate:fresh --seed.
 * CreatePage maakt geen pagina op /, daarom bouwt de seeder haar zelf.
 */
class HomepaginaSeeder extends Seeder
{
    /**
     * De striptypes van de homepage, van boven naar beneden.
     *
     * @var list<string>
     */
    private const array STROKEN = [
        'hero',
        'lopende-band',
        'herkenning',
        'twee-kolommen',
        'diensten',
        'stappen',
        'over-mij-intro',
        'reviews',
        'leveranciers',
        'checklist-aanmelding',
        'cta',
    ];

    public function run(): void
    {
        $adres = PagePath::fromString(PagePath::HOME);

        if (Route::query()->atPath($adres)->exists()) {
            $this->command?->warn('Er staat al een pagina op /. De homepage is niet opnieuw gezet.');

            return;
        }

        $page = Page::query()->create(['title' => 'Home', 'slug' => 'home', 'in_menu' => false]);
        $page->route()->create(['path' => $adres->value]);
        MovePage::run($page, parentId: null, position: 0);

        foreach (self::STROKEN as $positie => $type) {
            AddStrip::run($page, $type, $positie);
        }

        PublishPage::run($page);
    }
}
