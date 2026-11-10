<?php

beforeEach(function () {
    $this->withoutVite();
});

test('the about page responds with the title from the design', function () {
    $document = documentVoor('/over-mij');

    expect($document->querySelector('title')?->textContent)->toBe('Over Romy | Persoonlijke weddingplanner in Zuid-Holland');
});

test('every section of the about page is a strip, in the order of the design', function () {
    $document = documentVoor('/over-mij');

    expect(attributenVan($document->querySelectorAll('main > *'), 'data-cms-strip'))->toBe([
        'hero-over-mij',
        'lopende-band',
        'verhaal',
        'kijkje',
        'citaat',
        'kwaliteiten',
        'cta',
    ]);
});

test('the about page shows the three qualities', function () {
    $document = documentVoor('/over-mij');

    expect(tekstenVan($document->querySelectorAll('[data-cms-items="kwaliteiten"] > .note h3')))->toBe(['Punctueel', 'Oog voor detail', 'Lokaal']);
});

test('the about hero has one button, to the meeting', function () {
    $document = documentVoor('/over-mij');

    expect(attributenVan($document->querySelectorAll('[data-cms-strip="hero-over-mij"] a.btn'), 'href'))->toBe([route('kennismaken')]);
});
