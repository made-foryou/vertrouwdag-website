<?php

use Database\Seeders\PaginasSeeder;

beforeEach(function () {
    $this->withoutVite();
    $this->seed(PaginasSeeder::class);
});

test('the process page responds with the title from the design', function () {
    $document = documentVoor('/werkwijze');

    expect($document->querySelector('title')?->textContent)->toBe('Werkwijze: zo plan ik jullie bruiloft | vertrouwdag');
});

test('the seeded process page comes from the cms with every strip in the order of the design', function () {
    $this->get('/werkwijze')->assertOk()->assertViewIs('page');

    expect(striptypenOp('/werkwijze', documentVoor('/werkwijze')))->toBe([
        'hero-werkwijze',
        'tijdlijn',
        'herkenning',
        'tijdlijn',
        'cta',
    ]);
});

test('the process page numbers the phases one to six with the wedding day as the fifth', function () {
    $document = documentVoor('/werkwijze');

    expect(tekstenVan($document->querySelectorAll('.tl__badge span')))->toBe(['1', '2', '3', '4', '5', '6'])
        ->and($document->querySelector('.tl__card h2'))->toBeNull()
        ->and(tekstenVan($document->querySelectorAll('.tl__card > h3.h3')))->toBe([
            'Kennismaken en voorstel',
            'Locatie, datum en budget',
            'Leveranciers en styling',
            'Het draaiboek',
            'Afronden',
        ])
        ->and(platteTekst(strookVanType('/werkwijze', $document, 'herkenning')?->querySelector('h2')))->toBe('Wat jullie familie die dag doet');
});

test('only the first timeline lies on the band', function () {
    $document = documentVoor('/werkwijze');

    expect(attributenVan($document->querySelectorAll('main > section:has(ol.tl)'), 'class'))->toBe(['band', null]);
});

test('the process hero links to the meeting and to the packages', function () {
    $document = documentVoor('/werkwijze');

    expect(attributenVan(strookVanType('/werkwijze', $document, 'hero-werkwijze')?->querySelectorAll('a.btn'), 'href'))->toBe([url('/kennismaken'), '/diensten']);
});
