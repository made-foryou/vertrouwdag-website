<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Made\CmsPackage\Content\Actions\AddStrip;
use Made\CmsPackage\Content\Actions\CreatePage;
use Made\CmsPackage\Content\Actions\PublishPage;
use Made\CmsPackage\Content\Actions\UpdateAltText;
use Made\CmsPackage\Content\Actions\UpdateStrip;
use Made\CmsPackage\Content\Actions\UploadMedia;
use Made\CmsPackage\Content\Data\NewPage;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Data\UploadedMedia;
use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\Page;
use RuntimeException;

/**
 * Zet een pagina van de site in het CMS: een gepubliceerde pagina op haar
 * adres, met de stroken in de volgorde van het ontwerp (besluit 2 in #13).
 *
 * Elke strook begint met de standaardwaarden van zijn type. Voor een type
 * dat op één pagina staat, zijn dat de teksten van die pagina, dus noemt
 * de seeder alleen het type. Een type dat op meer pagina's staat (cta,
 * lopende-band, tijdlijn) krijgt hier de waarden die afwijken.
 *
 * Een pagina staat in het menu, tenzij inMenu() anders zegt. Het
 * hoofdmenu toont de pagina's in de volgorde van de boom (#19). De
 * kennismaking en de juridische pagina's staan er niet in (#18). Een
 * pagina met een ouder() staat onder die pagina, zoals een artikel onder
 * /blog (#20).
 *
 * Staat er al een pagina op het adres, dan doet de seeder niets: wat een
 * redacteur wijzigde, gaat nooit verloren (besluit Menno, 2026-11-11, zie
 * #16). Alleen een pagina zonder stroken en zonder concept krijgt haar
 * stroken alsnog, zoals /blog, die eerst leeg in het CMS stond (#19).
 * Opnieuw beginnen kan lokaal met migrate:fresh --seed.
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

    /**
     * Het adres van de pagina waar deze pagina onder staat, of null voor
     * een pagina bovenaan in de boom.
     */
    protected function ouder(): ?string
    {
        return null;
    }

    public function run(): void
    {
        $adres = PagePath::fromString($this->adres());
        $bestaand = Page::query()->atPath($adres)->first();

        if ($bestaand !== null && ($bestaand->strips !== [] || $bestaand->draft_strips !== null)) {
            $this->command?->warn("Er staat al een pagina op {$adres->value}. Die is niet opnieuw gezet.");

            return;
        }

        $page = $bestaand ?? $this->maakPagina($adres);

        foreach ($this->stroken() as $positie => $strook) {
            $strip = AddStrip::run($page, $strook[0], $positie);

            if (($strook[1] ?? []) !== []) {
                UpdateStrip::run($page, $strip->id, $strook[1], chooseLevels: false);
            }
        }

        PublishPage::run($page);
    }

    /**
     * Maakt de lege pagina: onder ouder(), of bovenaan in de paginaboom, na
     * de pagina's die er al staan, en in het menu als inMenu() dat zegt.
     *
     * @throws RuntimeException als de ouder nog niet in het CMS staat
     */
    protected function maakPagina(PagePath $adres): Page
    {
        $ouder = $this->ouder() === null ? null : Page::query()->atPath(PagePath::fromString($this->ouder()))->first()
            ?? throw new RuntimeException("Zet eerst de pagina op {$this->ouder()} in het CMS: {$adres->value} staat eronder.");

        return CreatePage::run(new NewPage(title: $this->titel(), slug: basename($adres->value), parentId: $ouder?->id, inMenu: $this->inMenu()));
    }

    /**
     * De id van een foto uit resources/images in de Media-bibliotheek, voor
     * een veld Afbeelding. Staat er al een foto met die naam, dan is het
     * die, zodat elke foto er één keer staat. Een foto zonder alt-tekst
     * krijgt deze; een alt-tekst die er al is, blijft.
     */
    protected function foto(string $bestand, string $alt): int
    {
        $media = Media::query()->images()->where('name', pathinfo($bestand, PATHINFO_FILENAME))->first()
            ?? UploadMedia::run(new UploadedMedia(path: resource_path("images/{$bestand}"), filename: $bestand));

        if (blank($media->alt)) {
            $media = UpdateAltText::run($media, $alt);
        }

        return $media->id;
    }
}
