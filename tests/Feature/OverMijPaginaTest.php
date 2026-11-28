<?php

use Database\Seeders\PaginasSeeder;

beforeEach(function () {
    $this->withoutVite();
    $this->seed(PaginasSeeder::class);
});

test('the about page responds with the title from the design', function () {
    $document = documentVoor('/over-mij');

    expect($document->querySelector('title')?->textContent)->toBe('Over Romy | Persoonlijke weddingplanner in Zuid-Holland');
});

test('the seeded about page comes from the cms with every strip in the order of the design', function () {
    $this->get('/over-mij')->assertOk()->assertViewIs('page');

    expect(striptypenOp('/over-mij', documentVoor('/over-mij')))->toBe([
        'hero',
        'lopende-band',
        'verhaal',
        'kijkje',
        'citaat',
        'herkenning',
        'cta',
    ]);
});

test('the about page shows the three qualities', function () {
    $document = documentVoor('/over-mij');

    expect(tekstenVan($document->querySelectorAll('[data-cms-items="citaten"] > .note h3')))->toBe(['Punctueel', 'Oog voor detail', 'Lokaal']);
});

test('the about page has its own texts in the running band', function () {
    $document = documentVoor('/over-mij');

    expect(tekstenVan(strookVanType('/over-mij', $document, 'lopende-band')?->querySelectorAll('.marquee__track > span')))
        ->toBe(['Punctueel', 'Oog voor detail', 'Vanuit het hart', 'Thuis in Zuid-Holland']);
});

test('the about hero has one button, to the meeting', function () {
    $document = documentVoor('/over-mij');

    expect(attributenVan(strookVanType('/over-mij', $document, 'hero')?->querySelectorAll('a.btn'), 'href'))->toBe(['/kennismaken']);
});
