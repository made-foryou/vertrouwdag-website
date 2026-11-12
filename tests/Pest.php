<?php

use Dom\Element;
use Dom\HTMLDocument;
use Dom\NodeList;
use Illuminate\Foundation\Testing\LazilyRefreshDatabase;
use Made\CmsPackage\Content\Data\PagePath;
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
pest()->extend(TestCase::class)
    ->use(LazilyRefreshDatabase::class)
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
 *
 * @return list<?string>
 */
function striptypenOp(string $adres, HTMLDocument $document): array
{
    $typen = array_column(Page::query()->published()->atPath(PagePath::fromString($adres))->sole()->strips, 'type', 'id');

    return array_map(fn (?string $id): ?string => $typen[$id] ?? null, attributenVan($document->querySelectorAll('main > *'), 'data-cms-strip'));
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
 * Een blog zoals hij in config/blogs.php staat: een gepubliceerde blog
 * zonder adres, tenzij de test andere velden geeft.
 *
 * @param  array<string, mixed>  $velden
 * @return array{titel: string, intro: string, categorie: string, leestijd: ?int, afbeelding: string, alt: string, slug: ?string, binnenkort: bool}
 */
function blogInDeConfig(array $velden = []): array
{
    return [
        'titel' => 'Een blog',
        'intro' => 'Een korte intro.',
        'categorie' => 'planning',
        'leestijd' => 5,
        'afbeelding' => 'resources/images/ringen-goud.jpg',
        'alt' => 'Twee gouden trouwringen',
        'slug' => null,
        'binnenkort' => false,
        ...$velden,
    ];
}
