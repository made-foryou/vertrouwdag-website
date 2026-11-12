<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Made\CmsPackage\Content\Actions\AddStrip;
use Made\CmsPackage\Content\Actions\CreatePage;
use Made\CmsPackage\Content\Actions\PublishPage;
use Made\CmsPackage\Content\Actions\UpdateStrip;
use Made\CmsPackage\Content\Data\NewPage;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Models\Page;
use Made\CmsPackage\Content\Models\Route;

/**
 * Zet een pagina van de site in het CMS: een gepubliceerde pagina op haar
 * adres, met de stroken in de volgorde van het ontwerp (besluit 2 in #13).
 *
 * Elke strook begint met de standaardwaarden van zijn type. Voor een type
 * dat op één pagina staat, zijn dat de teksten van die pagina, dus noemt
 * de seeder alleen het type. Een type dat op meer pagina's staat (cta,
 * lopende-band, tijdlijn) krijgt hier de waarden die afwijken.
 *
 * Een pagina staat in het menu, tenzij inMenu() anders zegt. Dat geldt
 * voor de pagina's die het menu in config/vertrouwdag.php niet noemt,
 * zoals de kennismaking en de juridische pagina's (#18).
 *
 * Staat er al een pagina op het adres, dan doet de seeder niets: wat een
 * redacteur wijzigde, gaat nooit verloren (besluit Menno, 2026-11-11, zie
 * #16). Opnieuw beginnen kan lokaal met migrate:fresh --seed.
 */
abstract class PaginaSeeder extends Seeder
{
    /**
     * Het adres van de pagina, zoals /diensten.
     */
    abstract protected function adres(): string;

    /**
     * De titel van de pagina in het CMS.
     */
    abstract protected function titel(): string;

    /**
     * De stroken van boven naar beneden: het type, en de waarden die
     * afwijken van zijn standaardwaarden.
     *
     * @return list<array{0: string, 1?: array<string, mixed>}>
     */
    abstract protected function stroken(): array;

    /**
     * Of de pagina in het menu staat.
     */
    protected function inMenu(): bool
    {
        return true;
    }

    public function run(): void
    {
        $adres = PagePath::fromString($this->adres());

        if (Route::query()->atPath($adres)->exists()) {
            $this->command?->warn("Er staat al een pagina op {$adres->value}. Die is niet opnieuw gezet.");

            return;
        }

        $page = $this->maakPagina($adres);

        foreach ($this->stroken() as $positie => $strook) {
            $strip = AddStrip::run($page, $strook[0], $positie);

            if (($strook[1] ?? []) !== []) {
                UpdateStrip::run($page, $strip->id, $strook[1], chooseLevels: false);
            }
        }

        PublishPage::run($page);
    }

    /**
     * Maakt de lege pagina: bovenaan in de paginaboom, na de pagina's die
     * er al staan, en in het menu als inMenu() dat zegt.
     */
    protected function maakPagina(PagePath $adres): Page
    {
        return CreatePage::run(new NewPage(title: $this->titel(), slug: basename($adres->value), parentId: null, inMenu: $this->inMenu()));
    }
}
