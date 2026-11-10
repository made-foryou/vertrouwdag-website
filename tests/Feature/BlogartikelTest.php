<?php

use App\Support\Blogs;

beforeEach(function () {
    $this->withoutVite();
});

test('every section of the article is a strip, in the order of the design', function () {
    $document = documentVoor('/blog/wanneer-weddingplanner-inschakelen');

    expect(attributenVan($document->querySelectorAll('main > article > *'), 'data-cms-strip'))->toBe([
        'artikel-kop',
        'artikel-begin',
        'artikel-fotos',
        'artikel-moment',
        'artikel-citaat',
        'artikel-signalen',
        'auteur',
    ])
        ->and(attributenVan($document->querySelectorAll('main > :not(article)'), 'data-cms-strip'))->toBe(['kennismaking']);
});

test('the article takes its title, topic and reading time from its blog', function () {
    config(['blogs' => [
        blogInDeConfig(['titel' => 'Een vroeg *begin*', 'categorie' => 'budget', 'leestijd' => 9, 'slug' => 'wanneer-weddingplanner-inschakelen']),
    ]]);

    $document = documentVoor('/blog/wanneer-weddingplanner-inschakelen');
    $kop = $document->querySelector('[data-cms-strip="artikel-kop"] .art-head');

    expect(platteTekst($kop?->querySelector('h1')))->toBe('Een vroeg begin')
        ->and($kop?->querySelector('h1 em')?->textContent)->toBe('begin')
        ->and(platteTekst($kop?->querySelector('nav.crumbs')))->toBe('Home · Blog · Budget')
        ->and(attributenVan($kop?->querySelectorAll('nav.crumbs a'), 'href'))->toBe([route('home'), route('blog')])
        ->and(tekstenVan($kop?->querySelectorAll('.row > span')))->toBe(['Budget', '9 min lezen']);
});

test('the article describes itself to search engines as a blog posting', function () {
    $document = documentVoor('/blog/wanneer-weddingplanner-inschakelen');
    $scripts = $document->querySelectorAll('script[type="application/ld+json"]');

    expect($scripts)->toHaveCount(1);

    expect(json_decode($scripts->item(0)->textContent, true, flags: JSON_THROW_ON_ERROR))
        ->toMatchArray([
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => 'Wanneer schakel je een weddingplanner in? (En waarom eerder dan je denkt)',
            'author' => ['@type' => 'Person', 'name' => 'Romy van Dillen', 'url' => route('over-mij')],
            'publisher' => ['@type' => 'Organization', 'name' => 'vertrouwdag'],
            'mainEntityOfPage' => route('blog.artikel', 'wanneer-weddingplanner-inschakelen'),
        ])
        ->toHaveKey('image');
});

test('the article closes with the writer and an invitation to meet', function () {
    $document = documentVoor('/blog/wanneer-weddingplanner-inschakelen');

    expect($document->querySelector('[data-cms-strip="auteur"] a')?->getAttribute('href'))->toBe(route('over-mij'))
        ->and($document->querySelector('[data-cms-strip="kennismaking"] a.btn')?->getAttribute('href'))->toBe(route(config('vertrouwdag.cta.route')));
});

test('an article that is not online returns 404', function (array $blogs, string $adres) {
    config(['blogs' => $blogs]);

    $response = $this->get($adres);

    $response->assertNotFound();
})->with([
    'onbekend adres' => [[], '/blog/bestaat-niet'],
    'blog die binnenkort komt' => [[blogInDeConfig(['slug' => 'wanneer-weddingplanner-inschakelen', 'binnenkort' => true])], '/blog/wanneer-weddingplanner-inschakelen'],
]);

test('every published blog has an article', function () {
    $blogs = Blogs::gepubliceerd();

    expect($blogs)->not->toBeEmpty();

    foreach ($blogs as $blog) {
        $this->get($blog->url())->assertOk();
    }
});
