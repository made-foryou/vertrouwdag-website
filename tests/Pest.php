<?php

use App\Support\Strip;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\NodeList;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\Str;
use Made\CmsPackage\Content\Actions\PublishPage;
use Made\CmsPackage\Content\Actions\UpdateStrip;
use Made\CmsPackage\Content\Actions\UploadMedia;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Data\UploadedMedia;
use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\Page;
use Tests\TestCase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| The closure you provide to your test functions is always bound to a specific PHPUnit test
| case class. By default, that class is "PHPUnit\Framework\TestCase". Of course, you may
| need to change it using the "pest()" function to bind different classes or traits.
|
*/

// Elk onbekend adres zoekt een pagina in de database van het CMS (Route::madeCms). Lazily migreert alleen voor een test
// die de database echt gebruikt.
//
// De seeders van de blog zetten foto's in de Media-bibliotheek (#20). Die schrijft naar een nep-schijf, nooit naar
// storage/app/public. Een foto echt uploaden verkleint hem en maakt een thumbnail, ongeveer 0,1 s per foto, en
// PaginasSeeder doet dat vier keer in elke test die hem draait. Daarom zet UploadMedia hier alleen een bestand in de
// bibliotheek, met de naam die het package het geeft. De actie is final, dus geen mock: UploadMedia::run() haalt hem
// uit de container. Een test die de echte upload nodig heeft, roept echteUploads() aan.
pest()->extend(TestCase::class)
    ->use(LazilyRefreshDatabase::class)
    ->beforeEach(function () {
        // Fake the disk the package stores media on, not a fixed one: a local .env may point it at a real bucket.
        Storage::fake(config()->string('made-cms-package.media.disk'));
        app()->instance(UploadMedia::class, new class
        {
            public function handle(UploadedMedia $upload): Media
            {
                return Media::factory()->create(['name' => Str::slug(pathinfo($upload->filename, PATHINFO_FILENAME))]);
            }
        });
    })
    ->in('Feature');

/*
|--------------------------------------------------------------------------
| Expectations
|--------------------------------------------------------------------------
|
| When you're writing tests, you often need to check that values meet certain conditions. The
| "expect()" function gives you access to a set of "expectations" methods that you can use
| to assert different things. Of course, you may extend the Expectation API at any time.
|
*/

expect()->extend('toBeOne', function () {
    return $this->toBe(1);
});

/*
|--------------------------------------------------------------------------
| Functions
|--------------------------------------------------------------------------
|
| While Pest is very powerful out-of-the-box, you may have some testing code specific to your
| project that you don't want to repeat in every file. Here you can also expose helpers as
| global functions to help you to reduce the number of lines of code in your test files.
|
*/

/**
 * Rendert een Blade-template en geeft het resultaat als document terug,
 * zodat een test met CSS-selectors in een component kan zoeken.
 *
 * @param  array<string, mixed>  $data
 */
function renderComponent(string $template, array $data = []): HTMLDocument
{
    $html = (string) test()->blade($template, $data);

    return HTMLDocument::createFromString('<!doctype html><html><body>'.$html.'</body></html>', LIBXML_NOERROR);
}

/**
 * Rendert een strip-view met een strook, zoals <x-strips> dat doet.
 *
 * @param  array<string, mixed>  $values
 */
function renderStrip(string $type, array $values): HTMLDocument
{
    return renderComponent("@include('strips.{$type}', ['strip' => \$strip])", ['strip' => new Strip('s1', $type, $values)]);
}

/**
 * Een kop zoals een veld Kop hem rendert: de tekst met het niveau.
 *
 * @return array{text: string, tag: string}
 */
function kop(string $tekst, string $niveau = 'h2'): array
{
    return ['text' => $tekst, 'tag' => $niveau];
}

/**
 * Haalt een pagina op en geeft het document terug, zodat de test met
 * CSS-selectors in de pagina kan zoeken.
 */
function documentVoor(string $url): HTMLDocument
{
    $response = test()->get($url);

    $response->assertOk();

    return HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);
}

/**
 * De types van de stroken in <main>, van boven naar beneden. Een strook uit
 * het CMS heeft een uuid als id; de gepubliceerde pagina op het adres zegt
 * van welk type hij is. Een strook die niet van die pagina is, wordt null.
 * Van een artikel staan de stroken in <article> (selector main > article > *).
 *
 * @return list<?string>
 */
function striptypenOp(string $adres, HTMLDocument $document, string $selector = 'main > *'): array
{
    $typen = array_column(Page::query()->published()->atPath(PagePath::fromString($adres))->sole()->strips, 'type', 'id');

    return array_map(fn (?string $id): ?string => $typen[$id] ?? null, attributenVan($document->querySelectorAll($selector), 'data-cms-strip'));
}

/**
 * De eerste strook van het type in <main>, op de gepubliceerde pagina op
 * het adres.
 */
function strookVanType(string $adres, HTMLDocument $document, string $type): ?Element
{
    $index = array_search($type, striptypenOp($adres, $document), true);

    return $index === false ? null : $document->querySelectorAll('main > *')->item($index);
}

/**
 * Wijzigt de eerste strook van het type op de pagina op het adres, zoals een
 * redacteur dat in de bewerkschil doet. De wijziging krijgt de waarden van
 * de strook in het concept en geeft de velden terug die veranderen. Zonder
 * publiceren staat de wijziging alleen in het concept.
 *
 * @param  Closure(array<string, mixed>): array<string, mixed>  $wijziging
 */
function wijzigStrook(string $adres, string $type, Closure $wijziging, bool $publiceren = true): void
{
    $pagina = Page::query()->atPath(PagePath::fromString($adres))->sole();
    $strook = collect($pagina->draft_strips ?? $pagina->strips)->firstWhere('type', $type);

    UpdateStrip::run($pagina, $strook['id'], $wijziging($strook['values']), chooseLevels: false);

    if ($publiceren) {
        PublishPage::run($pagina);
    }
}

/**
 * Wijzigt diensten in de strook pakketten op /diensten, zoals Romy dat in
 * de bewerkschil doet: per dienst (dag, partial of full) de velden die
 * veranderen. Zonder publiceren staat de wijziging alleen in het concept.
 *
 * @param  array<string, array<string, mixed>>  $wijzigingen
 */
function wijzigPakketten(array $wijzigingen, bool $publiceren = true): void
{
    wijzigStrook('/diensten', 'pakketten', fn (array $waarden): array => [
        'diensten' => array_map(fn (array $item): array => [...$item, ...($wijzigingen[$item['dienst']] ?? [])], $waarden['diensten']),
    ], $publiceren);
}

/**
 * @return list<string>
 */
function attributenVan(NodeList $elements, string $attribute): array
{
    $values = [];

    foreach ($elements as $element) {
        $values[] = $element->getAttribute($attribute);
    }

    return $values;
}

/**
 * @return list<string>
 */
function tekstenVan(NodeList $elements): array
{
    $values = [];

    foreach ($elements as $element) {
        $values[] = trim($element->textContent);
    }

    return $values;
}

/**
 * De tekst van een element met de witruimte samengevoegd tot één spatie.
 */
function platteTekst(?Element $element): ?string
{
    return $element === null ? null : trim(preg_replace('/\s+/', ' ', $element->textContent));
}

/**
 * Een strook artikel-kop zoals het CMS hem opslaat, met een foto uit de
 * Media-bibliotheek, tenzij de test andere velden geeft.
 *
 * @param  array<string, mixed>  $velden
 * @return array{id: string, type: string, visible: bool, values: array<string, mixed>}
 */
function artikelkop(array $velden = [], bool $zichtbaar = true): array
{
    return [
        'id' => (string) Str::uuid(),
        'type' => 'artikel-kop',
        'visible' => $zichtbaar,
        'values' => [
            'titel' => 'Een blog',
            'intro' => 'Een korte intro.',
            'categorie' => 'planning',
            'leestijd' => '5',
            'foto' => Media::factory()->withAlt('Twee gouden trouwringen')->create()->id,
            ...$velden,
        ],
    ];
}

/**
 * De pagina /blog in het CMS, gepubliceerd en zonder stroken, als ouder
 * van de artikelen. Staat ze er al, dan is het die.
 */
function blogpagina(): Page
{
    return Page::query()->atPath(PagePath::fromString('/blog'))->first()
        ?? Page::factory()->published()->at('/blog')->create(['title' => 'Blog']);
}

/**
 * Een gepubliceerd artikel onder /blog, zoals Romy het maakt: een pagina
 * met een artikelkop. De velden zijn die van de artikelkop.
 *
 * @param  array<string, mixed>  $velden
 */
function artikelOnderBlog(string $slug, array $velden = []): Page
{
    return Page::factory()->published()->under(blogpagina())->at("/blog/{$slug}")->withStrips([artikelkop($velden)])->create(['title' => $slug]);
}

/**
 * Laat UploadMedia een foto weer echt in de Media-bibliotheek zetten, met het verkleinen en de thumbnail, in plaats van
 * het snelle bestand uit Pest.php.
 */
function echteUploads(): void
{
    app()->forgetInstance(UploadMedia::class);
}

/**
 * Laat Vite::asset() weer de url van een foto uit de gebouwde manifest geven, ook als de Vite-devserver lokaal draait,
 * zodat een test ziet welke foto uit het ontwerp er staat.
 */
function metGebouwdeFotos(): void
{
    test()->withVite();
    Vite::useHotFile(storage_path('framework/testing/geen-vite-devserver'));
}

/**
 * Een foto zoals een veld Afbeelding hem rendert, met deze velden in plaats van de standaard.
 *
 * @param  array<string, mixed>  $velden
 * @return array{url: string, alt: string, width: int|null, height: int|null, focal: array{x: float, y: float}}
 */
function fotoUitCms(array $velden = []): array
{
    return ['url' => '/storage/media/ringen.webp', 'alt' => 'Twee gouden trouwringen', 'width' => 1920, 'height' => 1280, 'focal' => ['x' => 0.25, 'y' => 0.8], ...$velden];
}
