<?php

namespace Database\Seeders;

use App\Support\Dienst;
use Closure;
use Illuminate\Database\Seeder;
use Made\CmsPackage\Content\Actions\AddStrip;
use Made\CmsPackage\Content\Actions\CreatePage;
use Made\CmsPackage\Content\Actions\PublishPage;
use Made\CmsPackage\Content\Actions\UpdateAltText;
use Made\CmsPackage\Content\Actions\UpdateStrip;
use Made\CmsPackage\Content\Actions\UploadMedia;
use Made\CmsPackage\Content\Data\NewPage;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Data\Strip;
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
 * lopende-band, tijdlijn) krijgt hier de waarden die afwijken. Een veld
 * Afbeelding heeft geen standaardwaarde, dus krijgt elke strook hier de
 * foto's uit het ontwerp (ontwerpfotos(), #21).
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
            $waarden = [...$this->ontwerpfotos($strip), ...($strook[1] ?? [])];

            if ($waarden !== []) {
                UpdateStrip::run($page, $strip->id, $waarden, chooseLevels: false);
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
     * De foto's uit het ontwerp voor de fotovelden van een nieuwe strook
     * (#21), als ids in de Media-bibliotheek. Het zijn de foto's die de view
     * toont als het veld leeg is (<x-foto>). Zo ziet een verse database er
     * hetzelfde uit, en ziet Romy in de bewerkschil welke foto er staat.
     *
     * In een lijst krijgt elk item zijn foto via zijn id: een kolom de foto
     * van zijn plek, een dienst de foto van de dienst. Een lijst die een
     * seeder zelf meegeeft in stroken(), vervangt deze lijst.
     *
     * @return array<string, mixed>
     */
    private function ontwerpfotos(Strip $strip): array
    {
        return match ($strip->type) {
            'hero' => ['foto_boog' => $this->ontwerpfoto('romy-portret.jpg'), 'foto_polaroid' => $this->ontwerpfoto('ringen-boeket.jpg')],
            'hero-diensten' => ['foto_boog' => $this->ontwerpfoto('ceremonie-buiten.jpg'), 'foto_polaroid' => $this->ontwerpfoto('bruid-rozen.jpg')],
            'hero-over-mij' => ['foto_boog' => $this->ontwerpfoto('romy.jpg'), 'foto_polaroid' => $this->ontwerpfoto('kus-zwartwit.jpg')],
            'over-mij-intro' => ['foto_boog' => $this->ontwerpfoto('romy.jpg'), 'foto_polaroid' => $this->ontwerpfoto('ringen-pioenen.jpg')],
            'hero-werkwijze' => ['foto_boven' => $this->ontwerpfoto('paar-landschap.jpg'), 'foto_onder' => $this->ontwerpfoto('ringen-goud.jpg')],
            'kijkje' => ['foto_links' => $this->ontwerpfoto('romy-klein.jpg'), 'foto_rechts' => $this->ontwerpfoto('romy-samen.jpg')],
            'hero-kennismaken', 'auteur' => ['foto' => $this->ontwerpfoto('romy.jpg')],
            'cta' => ['foto' => $this->ontwerpfoto('romy-gesprek.jpg')],
            'hero-checklist' => ['foto' => $this->ontwerpfoto('ringen-pioenen.jpg')],
            'checklist-aanmelding' => ['foto' => $this->ontwerpfoto('hand-in-hand.jpg')],
            'twee-kolommen' => ['kolommen' => $this->fotoPerItem($strip->values['kolommen'], fn (array $kolom, int $plek): int => $this->ontwerpfoto(['paar-landschap.jpg', 'feesttafel.jpg'][$plek % 2]))],
            'diensten', 'pakketten' => ['diensten' => $this->fotoPerItem($strip->values['diensten'], fn (array $dienst): int => $this->ontwerpfoto(basename(Dienst::from($dienst['dienst'])->foto())))],
            default => [],
        };
    }

    /**
     * Per item van een lijst zijn id en zijn foto, zodat UpdateStrip alleen
     * de foto van het item verandert.
     *
     * @param  list<array<string, mixed>>  $items
     * @param  Closure(array<string, mixed>, int): int  $foto  de id van de foto van een item, op zijn plek vanaf 0
     * @return list<array{id: string, foto: int}>
     */
    private function fotoPerItem(array $items, Closure $foto): array
    {
        return array_map(fn (array $item, int $plek): array => ['id' => $item['id'], 'foto' => $foto($item, $plek)], $items, array_keys($items));
    }

    /**
     * Een foto uit het ontwerp in de Media-bibliotheek, met de alt-tekst
     * uit #12. Een foto die op de site decoratie is (een dienst, de
     * checklist), krijgt daar een lege alt, maar heeft er in de bibliotheek
     * wel een, voor een plek waar hij wel iets zegt.
     */
    private function ontwerpfoto(string $bestand): int
    {
        return $this->foto($bestand, match ($bestand) {
            'romy.jpg', 'romy-portret.jpg' => 'Romy, weddingplanner van vertrouwdag in Zuid-Holland',
            'romy-gesprek.jpg' => 'Romy aan tafel met haar telefoon, klaar voor een kennismakingsgesprek',
            'romy-klein.jpg' => 'Romy als klein meisje verkleed als bruidje, met sluier, bloemenkrans en boeket',
            'romy-samen.jpg' => 'Romy samen met haar partner aan zee',
            'ringen-boeket.jpg' => 'Handen met trouwringen en een boeket',
            'ringen-goud.jpg' => 'Twee gouden trouwringen',
            'ringen-pioenen.jpg' => 'Trouwringen tussen roze pioenrozen',
            'ceremonie-buiten.jpg' => 'Ceremonieplek buiten met witte stoelen en bloemen',
            'bruid-rozen.jpg' => 'Bruid met boeket van perzikkleurige rozen',
            'kus-zwartwit.jpg' => 'Zwart-witfoto van een bruidspaar',
            'paar-landschap.jpg' => 'Bruidspaar wandelt door een weids landschap',
            'feesttafel.jpg' => 'Lange gedekte feesttafel met bloemen',
            'hand-in-hand.jpg' => 'Bruidspaar hand in hand op het gras, met hun schaduwen',
            'schoenen-boeket.jpg' => 'Bruidsschoenen op een vensterbank naast een boeket',
            'hand-in-hand-paar.jpg' => 'Bruid en bruidegom hand in hand',
            'sleep-jurk.jpg' => 'Bruidegom tilt de sleep van de bruidsjurk op',
        });
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
