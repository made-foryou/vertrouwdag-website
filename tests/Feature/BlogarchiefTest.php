<?php

use Database\Seeders\PaginasSeeder;
use Illuminate\Support\Str;
use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\Page;

beforeEach(function () {
    $this->withoutVite();
});

/**
 * Het archief onder /blog in het CMS, met de kop en de strook met alle artikelen, met hun standaardwaarden. Op /blog
 * staat een blog die binnenkort komt.
 */
function blogarchief(): Page
{
    $blog = Page::factory()->published()->at('/blog')->withStrips([
        ['id' => (string) Str::uuid(), 'type' => 'blog-laatste', 'visible' => true, 'values' => [
            'binnenkort' => [['titel' => 'Komt nog', 'intro' => 'Binnenkort.', 'categorie' => 'locaties', 'foto' => Media::factory()->create()->id]],
        ]],
    ])->create(['title' => 'Blog']);

    return Page::factory()->published()->under($blog)->at('/blog/archief')->withStrips([
        ['id' => (string) Str::uuid(), 'type' => 'hero-blog', 'visible' => true, 'values' => ['variant' => 'archief']],
        ['id' => (string) Str::uuid(), 'type' => 'blog-archief', 'visible' => true, 'values' => []],
    ])->create(['title' => 'Archief']);
}

test('the archive comes from the cms, with every section as a strip in the order of the design', function () {
    $this->seed(PaginasSeeder::class);

    $response = $this->get('/blog/archief');

    $response->assertOk()->assertViewIs('page');
    expect(striptypenOp('/blog/archief', documentVoor('/blog/archief')))->toBe(['hero-blog', 'blog-archief', 'cta']);
});

test('the archive shows every article, the newest first, with a link and its topic, without the blogs to come', function () {
    blogarchief();
    artikelOnderBlog('planning', ['titel' => 'Over planning', 'categorie' => 'planning']);
    artikelOnderBlog('budget', ['titel' => 'Over budget', 'categorie' => 'budget']);

    $document = documentVoor('/blog/archief');
    $kaarten = $document->querySelectorAll('main .posts > .pol');

    expect(tekstenVan($document->querySelectorAll('main .posts > .pol h3')))->toBe(['Over budget', 'Over planning'])
        ->and(attributenVan($kaarten, 'href'))->toBe([url('/blog/budget'), url('/blog/planning')])
        ->and(attributenVan($kaarten, 'data-cat'))->toBe(['budget', 'planning'])
        ->and(attributenVan($kaarten, 'data-post'))->each->toBe('');
});

test('the archive has a breadcrumb to the homepage and the blog', function () {
    blogarchief();

    $kruimelpad = documentVoor('/blog/archief')->querySelector('main nav.crumbs');

    expect(platteTekst($kruimelpad))->toBe('Home · Blog · Archief')
        ->and(attributenVan($kruimelpad?->querySelectorAll('a'), 'href'))->toBe([url('/'), url('/blog')]);
});

test('the archive filters on every topic with all blogs shown at first', function () {
    blogarchief();

    $chips = documentVoor('/blog/archief')->querySelectorAll('main .chips[role="group"] button.chip');

    expect(attributenVan($chips, 'data-filter'))->toBe(['alles', 'planning', 'locaties', 'budget', 'inspiratie', 'achter-de-schermen'])
        ->and(attributenVan($chips, 'aria-pressed'))->toBe(['true', 'false', 'false', 'false', 'false', 'false'])
        ->and(tekstenVan($chips))->toBe(['Alles', 'Planning', 'Locaties in Zuid-Holland', 'Budget', 'Inspiratie & styling', 'Achter de schermen']);
});

test('the archive only shows its empty message when there is no article', function (bool $metArtikel, bool $verborgen) {
    blogarchief();

    if ($metArtikel) {
        artikelOnderBlog('een-blog');
    }

    $melding = documentVoor('/blog/archief')->querySelector('main .posts__empty');

    expect($melding?->hasAttribute('hidden'))->toBe($verborgen)
        ->and($melding?->textContent)->toBe('Over dit onderwerp heb ik nog geen blog geschreven. Binnenkort meer!');
})->with([
    'met een artikel' => [true, true],
    'zonder artikelen' => [false, false],
]);

test('the archive links back to the blog', function () {
    blogarchief();

    $knop = documentVoor('/blog/archief')->querySelector('main .posts ~ .center a.btn');

    expect($knop?->getAttribute('href'))->toBe('/blog')
        ->and(trim($knop?->textContent))->toBe('Terug naar de blog');
});

test('the archive gives its cards a heading for screen readers, so no heading skips a level', function () {
    blogarchief();
    artikelOnderBlog('een-blog');

    $document = documentVoor('/blog/archief');

    $niveaus = [];
    foreach ($document->querySelectorAll('main h1, main h2, main h3, main h4') as $kop) {
        $niveaus[] = (int) substr($kop->tagName, 1);
    }
    $sprongen = array_filter(array_keys($niveaus), fn (int $i): bool => $i > 0 && $niveaus[$i] > $niveaus[$i - 1] + 1);

    expect($niveaus[0] ?? null)->toBe(1)
        ->and($sprongen)->toBe([])
        ->and(platteTekst($document->querySelector('main h2.sr-only')))->toBe('Alle artikelen');
});
