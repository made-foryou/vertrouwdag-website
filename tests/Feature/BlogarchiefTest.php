<?php

beforeEach(function () {
    $this->withoutVite();
});

test('every section of the archive is a strip, in the order of the design', function () {
    $document = documentVoor('/blog/archief');

    expect(attributenVan($document->querySelectorAll('main > *'), 'data-cms-strip'))->toBe([
        'hero-archief',
        'blog-archief',
        'cta',
    ]);
});

test('the archive shows every published blog with a link and its topic, without the blogs to come', function () {
    config(['blogs' => [
        blogInDeConfig(['titel' => 'Komt nog', 'binnenkort' => true, 'slug' => 'komt-nog']),
        blogInDeConfig(['titel' => 'Over planning', 'categorie' => 'planning', 'slug' => 'planning']),
        blogInDeConfig(['titel' => 'Zonder adres']),
        blogInDeConfig(['titel' => 'Over budget', 'categorie' => 'budget', 'slug' => 'budget']),
    ]]);

    $document = documentVoor('/blog/archief');
    $kaarten = $document->querySelectorAll('[data-cms-strip="blog-archief"] .posts > .pol');

    expect(tekstenVan($document->querySelectorAll('[data-cms-strip="blog-archief"] .posts > .pol h3')))->toBe(['Over planning', 'Over budget'])
        ->and(attributenVan($kaarten, 'href'))->toBe([route('blog.artikel', 'planning'), route('blog.artikel', 'budget')])
        ->and(attributenVan($kaarten, 'data-cat'))->toBe(['planning', 'budget'])
        ->and(attributenVan($kaarten, 'data-post'))->each->toBe('');
});

test('the archive has a breadcrumb to the homepage and the blog', function () {
    $kruimelpad = documentVoor('/blog/archief')->querySelector('[data-cms-strip="hero-archief"] nav.crumbs');

    expect(platteTekst($kruimelpad))->toBe('Home · Blog · Archief')
        ->and(attributenVan($kruimelpad?->querySelectorAll('a'), 'href'))->toBe([url('/'), route('blog')]);
});

test('the archive filters on every topic with all blogs shown at first', function () {
    $chips = documentVoor('/blog/archief')->querySelectorAll('[data-cms-strip="hero-archief"] .chips[role="group"] button.chip');

    expect(attributenVan($chips, 'data-filter'))->toBe(['alles', 'planning', 'locaties', 'budget', 'inspiratie', 'achter-de-schermen'])
        ->and(attributenVan($chips, 'aria-pressed'))->toBe(['true', 'false', 'false', 'false', 'false', 'false'])
        ->and(tekstenVan($chips))->toBe(['Alles', 'Planning', 'Locaties in Zuid-Holland', 'Budget', 'Inspiratie & styling', 'Achter de schermen']);
});

test('the archive only shows its empty message when there is no published blog', function (array $blogs, bool $verborgen) {
    config(['blogs' => $blogs]);

    $melding = documentVoor('/blog/archief')->querySelector('[data-cms-strip="blog-archief"] .posts__empty');

    expect($melding?->hasAttribute('hidden'))->toBe($verborgen)
        ->and($melding?->textContent)->toBe('Over dit onderwerp heb ik nog geen blog geschreven. Binnenkort meer!');
})->with([
    'met blogs' => [[blogInDeConfig(['slug' => 'een-blog'])], true],
    'zonder blogs' => [[blogInDeConfig(['binnenkort' => true])], false],
]);

test('the archive links back to the blog', function () {
    $knop = documentVoor('/blog/archief')->querySelector('[data-cms-strip="blog-archief"] a.btn');

    expect($knop?->getAttribute('href'))->toBe(route('blog'))
        ->and(trim($knop?->textContent))->toBe('Terug naar de blog');
});

test('the archive gives its cards a heading for screen readers, so no heading skips a level', function () {
    config(['blogs' => [blogInDeConfig(['slug' => 'een-blog'])]]);

    $document = documentVoor('/blog/archief');

    $niveaus = [];
    foreach ($document->querySelectorAll('main h1, main h2, main h3, main h4') as $kop) {
        $niveaus[] = (int) substr($kop->tagName, 1);
    }
    $sprongen = array_filter(array_keys($niveaus), fn (int $i): bool => $i > 0 && $niveaus[$i] > $niveaus[$i - 1] + 1);

    expect($niveaus[0] ?? null)->toBe(1)
        ->and($sprongen)->toBe([])
        ->and(platteTekst($document->querySelector('[data-cms-strip="blog-archief"] h2.sr-only')))->toBe('Alle artikelen');
});
