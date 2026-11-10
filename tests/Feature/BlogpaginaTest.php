<?php

use Illuminate\View\ViewException;

beforeEach(function () {
    $this->withoutVite();
});

test('every section of the blog page is a strip, in the order of the design', function () {
    $document = documentVoor('/blog');

    expect(attributenVan($document->querySelectorAll('main > *'), 'data-cms-strip'))->toBe([
        'hero-blog',
        'blog-uitgelicht',
        'blog-laatste',
        'cta',
    ]);
});

test('the newest published blog is featured with a link to its article', function () {
    config(['blogs' => [
        blogInDeConfig(['titel' => 'Komt nog', 'binnenkort' => true]),
        blogInDeConfig(['titel' => 'Nieuwste *blog*', 'categorie' => 'budget', 'leestijd' => 4, 'slug' => 'nieuwste']),
        blogInDeConfig(['titel' => 'Oudere blog', 'slug' => 'ouder']),
    ]]);

    $uitgelicht = documentVoor('/blog')->querySelector('[data-cms-strip="blog-uitgelicht"] a.feature');

    expect($uitgelicht?->getAttribute('href'))->toBe(route('blog.artikel', 'nieuwste'))
        ->and($uitgelicht?->querySelector('h2 em')?->textContent)->toBe('blog')
        ->and(tekstenVan($uitgelicht?->querySelectorAll('.feature__txt .row .eb')))->toBe(['Nieuwste blog', 'Budget · 4 min'])
        ->and($uitgelicht?->querySelector('.lnk')?->textContent)->toBe('Lees verder');
});

test('the cards show the first three blogs after the featured one, also the ones to come', function () {
    config(['blogs' => [
        blogInDeConfig(['titel' => 'Komt eerst', 'binnenkort' => true]),
        blogInDeConfig(['titel' => 'Nieuwste', 'slug' => 'nieuwste']),
        blogInDeConfig(['titel' => 'Komt daarna', 'binnenkort' => true]),
        blogInDeConfig(['titel' => 'Ouder', 'slug' => 'ouder']),
        blogInDeConfig(['titel' => 'Oudste', 'slug' => 'oudste']),
    ]]);

    $document = documentVoor('/blog');
    $kaarten = $document->querySelectorAll('[data-cms-strip="blog-laatste"] .posts > .pol');

    expect(tekstenVan($document->querySelectorAll('[data-cms-strip="blog-laatste"] .posts > .pol h3')))->toBe(['Komt eerst', 'Komt daarna', 'Ouder'])
        ->and(attributenVan($kaarten, 'href'))->toBe([null, null, route('blog.artikel', 'ouder')]);
});

test('a blog to come is a card without a link on the blog page', function () {
    config(['blogs' => [
        blogInDeConfig(['titel' => 'Komt nog', 'categorie' => 'locaties', 'binnenkort' => true]),
    ]]);

    $document = documentVoor('/blog');
    $kaart = $document->querySelector('[data-cms-strip="blog-laatste"] .posts > .pol');

    expect($kaart?->tagName)->toBe('ARTICLE')
        ->and($kaart?->classList->contains('pol--soon'))->toBeTrue()
        ->and($kaart?->querySelector('.eb')?->textContent)->toBe('Locaties in Zuid-Holland · binnenkort')
        ->and($document->querySelector('[data-cms-strip="blog-uitgelicht"] .feature'))->toBeNull();
});

test('the chips on the blog page open the archive on a topic', function () {
    $chips = documentVoor('/blog')->querySelectorAll('[data-cms-strip="hero-blog"] nav.chips a.chip');
    $archief = route('blog.archief');

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
    $knop = documentVoor('/blog')->querySelector('[data-cms-strip="blog-laatste"] a.btn');

    expect($knop?->getAttribute('href'))->toBe(route('blog.archief'))
        ->and(trim($knop?->textContent))->toBe('Bekijk alle blogs in het archief');
});

test('the blog page names a blog with an unknown topic', function () {
    config(['blogs' => [blogInDeConfig(['titel' => 'Taart proeven', 'categorie' => 'eten'])]]);
    $this->withoutExceptionHandling();

    expect(fn () => $this->get('/blog'))->toThrow(ViewException::class, 'De blog [Taart proeven] heeft een onbekende categorie [eten]');
});
