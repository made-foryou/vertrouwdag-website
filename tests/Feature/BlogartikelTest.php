<?php

use Database\Seeders\PaginasSeeder;
use Made\CmsPackage\Content\Actions\AddStrip;
use Made\CmsPackage\Content\Actions\CreatePage;
use Made\CmsPackage\Content\Actions\PublishPage;
use Made\CmsPackage\Content\Actions\UpdateStrip;
use Made\CmsPackage\Content\Data\NewPage;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\Page;

beforeEach(function () {
    $this->withoutVite();
});

test('the article keeps its address and comes from the cms, with every section as a strip in an article', function () {
    $this->seed(PaginasSeeder::class);

    $response = $this->get('/blog/wanneer-weddingplanner-inschakelen');

    $response->assertOk()->assertViewIs('page');
    expect(striptypenOp('/blog/wanneer-weddingplanner-inschakelen', documentVoor('/blog/wanneer-weddingplanner-inschakelen'), 'main > article > *'))->toBe([
        'artikel-kop',
        'artikel-tekst',
        'artikel-tekst',
        'artikel-fotos',
        'artikel-tekst',
        'artikel-citaat',
        'artikel-tekst',
        'auteur',
        'kennismaking',
    ]);
});

test('the article heading shows the title, topic and reading time of its fields', function () {
    artikelOnderBlog('vroeg', ['titel' => 'Een vroeg *begin*', 'categorie' => 'budget', 'leestijd' => '9']);

    $kop = documentVoor('/blog/vroeg')->querySelector('main .art-head');

    expect(platteTekst($kop?->querySelector('h1')))->toBe('Een vroeg begin')
        ->and($kop?->querySelector('h1 em')?->textContent)->toBe('begin')
        ->and(platteTekst($kop?->querySelector('nav.crumbs')))->toBe('Home · Blog · Budget')
        ->and(attributenVan($kop?->querySelectorAll('nav.crumbs a'), 'href'))->toBe([url('/'), url('/blog')])
        ->and(tekstenVan($kop?->querySelectorAll('.row > span')))->toBe(['Budget', '9 min lezen']);
});

test('the article shows the photo of its heading with the alt text of the file', function () {
    $foto = Media::factory()->withAlt('Bruidspaar bij een meer')->create();
    artikelOnderBlog('meer', ['foto' => $foto->id]);

    $img = documentVoor('/blog/meer')->querySelector('main .art-hero__img img');

    expect($img?->getAttribute('src'))->toEndWith("/storage/{$foto->path}")
        ->and($img?->getAttribute('alt'))->toBe('Bruidspaar bij een meer');
});

test('the article describes itself to search engines as a blog posting', function () {
    $foto = Media::factory()->create();
    artikelOnderBlog('vroeg', ['titel' => 'Een vroeg *begin*', 'foto' => $foto->id]);

    $scripts = documentVoor('/blog/vroeg')->querySelectorAll('script[type="application/ld+json"]');

    expect($scripts)->toHaveCount(1);

    $schema = json_decode($scripts->item(0)->textContent, true, flags: JSON_THROW_ON_ERROR);

    expect($schema)->toMatchArray([
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => 'Een vroeg begin',
        'author' => ['@type' => 'Person', 'name' => 'Romy van Dillen', 'url' => url('/over-mij')],
        'publisher' => ['@type' => 'Organization', 'name' => 'vertrouwdag'],
        'mainEntityOfPage' => url('/blog/vroeg'),
    ])
        ->and($schema['image'])->toEndWith("/storage/{$foto->path}");
});

test('the article closes with the writer and an invitation to meet', function () {
    $this->seed(PaginasSeeder::class);

    $document = documentVoor('/blog/wanneer-weddingplanner-inschakelen');

    expect($document->querySelector('main .author a')?->getAttribute('href'))->toBe('/over-mij')
        ->and($document->querySelector('main .founding a.btn')?->getAttribute('href'))->toBe(url(config('vertrouwdag.cta.pad')));
});

test('an article that is not online returns 404', function (Closure $maakPagina) {
    $maakPagina();

    $response = $this->get('/blog/niet-online');

    $response->assertNotFound();
})->with([
    'onbekend adres' => fn () => fn () => blogpagina(),
    'concept' => fn () => fn () => Page::factory()->under(blogpagina())->at('/blog/niet-online')->withDraft([artikelkop()])->create(),
]);

test('a blog that an editor adds under the blog and publishes is on the blog and in the archive', function () {
    $this->seed(PaginasSeeder::class);
    $foto = Media::factory()->withAlt('Ringen op een boeket')->create();
    $blog = Page::query()->atPath(PagePath::fromString('/blog'))->sole();

    $artikel = CreatePage::run(new NewPage(title: 'Trouwen in de winter', slug: 'trouwen-in-de-winter', parentId: $blog->id));
    $kop = AddStrip::run($artikel, 'artikel-kop', 0);
    UpdateStrip::run($artikel, $kop->id, [
        'titel' => ['text' => 'Trouwen in de *winter*'],
        'intro' => 'Kort licht, warme kleuren.',
        'categorie' => 'inspiratie',
        'foto' => $foto->id,
    ], chooseLevels: false);
    PublishPage::run($artikel);

    $uitgelicht = documentVoor('/blog')->querySelector('main a.feature');
    $archief = documentVoor('/blog/archief')->querySelectorAll('main .posts > .pol');

    expect($uitgelicht?->getAttribute('href'))->toBe(url('/blog/trouwen-in-de-winter'))
        ->and(platteTekst($uitgelicht?->querySelector('h2')))->toBe('Trouwen in de winter')
        ->and(attributenVan($archief, 'href'))->toBe([url('/blog/trouwen-in-de-winter'), url('/blog/wanneer-weddingplanner-inschakelen')]);
    $this->get('/blog/trouwen-in-de-winter')->assertOk();
});
