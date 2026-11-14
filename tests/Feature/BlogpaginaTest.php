<?php

use Database\Seeders\BlogpaginaSeeder;
use Database\Seeders\HomepaginaSeeder;
use Database\Seeders\OverMijPaginaSeeder;
use Database\Seeders\PaginasSeeder;
use Database\Seeders\VeelgesteldeVragenpaginaSeeder;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Enums\PageStatus;
use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\Page;

beforeEach(function () {
    $this->withoutVite();
});

/**
 * /blog in het CMS met de stroken van het overzicht: het nieuwste artikel en de kaarten, met de blogs die binnenkort
 * komen. Het knopje en de teksten zijn de standaardwaarden.
 *
 * @param  list<array<string, mixed>>  $binnenkort
 */
function blogoverzicht(array $binnenkort = []): Page
{
    return Page::factory()->published()->at('/blog')->withStrips([
        ['id' => (string) Str::uuid(), 'type' => 'blog-uitgelicht', 'visible' => true, 'values' => []],
        ['id' => (string) Str::uuid(), 'type' => 'blog-laatste', 'visible' => true, 'values' => ['binnenkort' => $binnenkort]],
    ])->create(['title' => 'Blog']);
}

/**
 * Een blog die binnenkort komt, als item van de strook blog-laatste.
 *
 * @return array<string, mixed>
 */
function blogDieBinnenkortKomt(string $titel, string $categorie = 'planning'): array
{
    return ['id' => (string) Str::uuid(), 'titel' => $titel, 'intro' => 'Komt nog.', 'categorie' => $categorie, 'foto' => Media::factory()->create()->id];
}

test('the blog page comes from the cms, with every section as a strip in the order of the design', function () {
    $this->seed(PaginasSeeder::class);

    $response = $this->get('/blog');

    $response->assertOk()->assertViewIs('page');
    expect(striptypenOp('/blog', documentVoor('/blog')))->toBe(['hero-blog', 'blog-uitgelicht', 'blog-laatste', 'cta']);
});

test('the newest article is featured with a link to its article', function () {
    blogoverzicht();
    artikelOnderBlog('ouder', ['titel' => 'Oudere blog']);
    artikelOnderBlog('nieuwste', ['titel' => 'Nieuwste *blog*', 'categorie' => 'budget', 'leestijd' => '4']);

    $uitgelicht = documentVoor('/blog')->querySelector('main a.feature');

    expect($uitgelicht?->getAttribute('href'))->toBe(url('/blog/nieuwste'))
        ->and($uitgelicht?->querySelector('h2 em')?->textContent)->toBe('blog')
        ->and(tekstenVan($uitgelicht?->querySelectorAll('.feature__txt .row .eb')))->toBe(['Nieuwste blog', 'Budget · 4 min'])
        ->and($uitgelicht?->querySelector('.lnk')?->textContent)->toBe('Lees verder');
});

test('the newest article is the page that was created last, not the one published last', function () {
    blogoverzicht();
    artikelOnderBlog('later', ['titel' => 'Later aangemaakt']);
    artikelOnderBlog('eerder', ['titel' => 'Eerder aangemaakt'])
        ->forceFill(['created_at' => now()->subDay(), 'published_at' => now()->addMinute()])
        ->save();

    $uitgelicht = documentVoor('/blog')->querySelector('main a.feature');

    expect($uitgelicht?->getAttribute('href'))->toBe(url('/blog/later'));
});

test('the cards show the three articles after the featured one, then the blogs to come', function () {
    blogoverzicht([blogDieBinnenkortKomt('Komt nog')]);

    foreach (['Oudste', 'Ouder', 'Oud', 'Nieuwer', 'Nieuwste'] as $titel) {
        artikelOnderBlog(Str::slug($titel), ['titel' => $titel]);
    }

    $document = documentVoor('/blog');
    $kaarten = $document->querySelectorAll('main .posts > .pol');

    expect(tekstenVan($document->querySelectorAll('main .posts > .pol h3')))->toBe(['Nieuwer', 'Oud', 'Ouder', 'Komt nog'])
        ->and(attributenVan($kaarten, 'href'))->toBe([url('/blog/nieuwer'), url('/blog/oud'), url('/blog/ouder'), null]);
});

test('a blog to come is a card without a link on the blog page', function () {
    blogoverzicht([blogDieBinnenkortKomt('Komt nog', 'locaties')]);

    $document = documentVoor('/blog');
    $kaart = $document->querySelector('main .posts > .pol');

    expect($kaart?->tagName)->toBe('ARTICLE')
        ->and($kaart?->classList->contains('pol--soon'))->toBeTrue()
        ->and($kaart?->querySelector('.eb')?->textContent)->toBe('Locaties in Zuid-Holland · binnenkort')
        ->and($document->querySelector('main .feature'))->toBeNull();
});

test('only a published page right under the blog with a visible article heading and a known topic is an article', function (Closure $maakPagina) {
    blogoverzicht();
    artikelOnderBlog('echt', ['titel' => 'Echt artikel']);
    $maakPagina();

    $document = documentVoor('/blog');

    expect($document->querySelector('main a.feature')?->getAttribute('href'))->toBe(url('/blog/echt'))
        ->and($document->querySelectorAll('main .posts > .pol'))->toHaveCount(0);
})->with([
    'concept' => fn () => fn () => Page::factory()->under(blogpagina())->at('/blog/concept')->withDraft([artikelkop()])->create(),
    'zonder artikelkop' => fn () => fn () => Page::factory()->published()->under(blogpagina())->at('/blog/archief')->withStrips([])->create(),
    'verborgen artikelkop' => fn () => fn () => Page::factory()->published()->under(blogpagina())->at('/blog/verborgen')->withStrips([artikelkop(zichtbaar: false)])->create(),
    'onbekend onderwerp' => fn () => fn () => Page::factory()->published()->under(blogpagina())->at('/blog/taart')->withStrips([artikelkop(['categorie' => 'eten'])])->create(),
    'niet onder de blog' => fn () => fn () => Page::factory()->published()->at('/nieuws')->withStrips([artikelkop()])->create(),
]);

test('the chips on the blog page open the archive on a topic', function () {
    $this->seed(PaginasSeeder::class);

    $chips = documentVoor('/blog')->querySelectorAll('main nav.chips a.chip');
    $archief = url('/blog/archief');

    expect(attributenVan($chips, 'href'))->toBe([
        $archief,
        "{$archief}#planning",
        "{$archief}#locaties",
        "{$archief}#budget",
        "{$archief}#inspiratie",
        "{$archief}#achter-de-schermen",
    ])
        ->and(tekstenVan($chips))->toBe(['Alles', 'Planning', 'Locaties in Zuid-Holland', 'Budget', 'Inspiratie & styling', 'Achter de schermen']);
});

test('the blog page links to the archive below the cards', function () {
    blogoverzicht();

    $knop = documentVoor('/blog')->querySelector('main .posts ~ .center a.btn');

    expect($knop?->getAttribute('href'))->toBe('/blog/archief')
        ->and(trim($knop?->textContent))->toBe('Bekijk alle blogs in het archief');
});

test('the seeder puts the blog page in the cms, published and in the menu, with the blogs to come and their photos', function () {
    echteUploads();

    $this->seed(BlogpaginaSeeder::class);

    $blog = Page::query()->atPath(PagePath::fromString('/blog'))->sole();
    $binnenkort = collect($blog->strips)->firstWhere('type', 'blog-laatste')['values']['binnenkort'];

    expect($blog->title)->toBe('Blog')
        ->and($blog->status)->toBe(PageStatus::Published)
        ->and($blog->in_menu)->toBeTrue()
        ->and(array_column($blog->strips, 'type'))->toBe(['hero-blog', 'blog-uitgelicht', 'blog-laatste', 'cta'])
        ->and(array_column($binnenkort, 'titel'))->toBe(['Landelijk-romantisch trouwen in Zuid-Holland: waar begin je?', 'Waarom jullie familie op jullie trouwdag niets hoeft te regelen', '[Volgend artikel]'])
        ->and(Media::query()->whereKey(array_column($binnenkort, 'foto'))->pluck('alt', 'name')->all())->toBe([
            'ceremonie-buiten' => 'Ceremonieplek buiten met witte stoelen',
            'feesttafel' => 'Gedekte feesttafel met bloemen',
            'ringen-goud' => 'Twee gouden trouwringen',
        ]);
    Storage::disk('public')->assertExists(Media::query()->pluck('path')->all());
});

test('the seeders put a photo that two pages use in the library once', function () {
    $this->seed(PaginasSeeder::class);

    expect(Media::query()->orderBy('name')->pluck('name')->all())->toBe(['ceremonie-buiten', 'feesttafel', 'paar-landschap', 'ringen-goud']);
});

test('the seeder gives the empty blog page from before the strips of the design', function () {
    $leeg = Page::factory()->published()->at('/blog')->create(['title' => 'Blog']);

    $this->seed(BlogpaginaSeeder::class);

    expect(array_column($leeg->refresh()->strips, 'type'))->toBe(['hero-blog', 'blog-uitgelicht', 'blog-laatste', 'cta'])
        ->and(Page::query()->count())->toBe(1);
});

test('the seeder leaves a blog page with strips as it is', function () {
    $blog = blogoverzicht();

    $this->seed(BlogpaginaSeeder::class);

    expect(array_column($blog->refresh()->strips, 'type'))->toBe(['blog-uitgelicht', 'blog-laatste'])
        ->and(Media::query()->count())->toBe(0);
});

test('the seeder puts the blog page right after over mij on a database that has the other pages', function () {
    $this->seed([HomepaginaSeeder::class, OverMijPaginaSeeder::class, VeelgesteldeVragenpaginaSeeder::class]);

    $this->seed(BlogpaginaSeeder::class);

    expect(Page::query()->roots()->inMenuOrder()->pluck('title')->all())->toBe(['Home', 'Over mij', 'Blog', 'Veelgestelde vragen']);
});

test('the seeder puts the blog page last on a database without over mij', function () {
    $this->seed([HomepaginaSeeder::class, VeelgesteldeVragenpaginaSeeder::class]);

    $this->seed(BlogpaginaSeeder::class);

    expect(Page::query()->roots()->inMenuOrder()->pluck('title')->all())->toBe(['Home', 'Veelgestelde vragen', 'Blog']);
});
