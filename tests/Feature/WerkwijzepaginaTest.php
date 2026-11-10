<?php

beforeEach(function () {
    $this->withoutVite();
});

test('the process page responds with the title from the design', function () {
    $document = documentVoor('/werkwijze');

    expect($document->querySelector('title')?->textContent)->toBe('Werkwijze: zo plan ik jullie bruiloft | vertrouwdag');
});

test('every section of the process page is a strip, in the order of the design', function () {
    $document = documentVoor('/werkwijze');

    expect(attributenVan($document->querySelectorAll('main > *'), 'data-cms-strip'))->toBe([
        'hero-werkwijze',
        'tijdlijn',
        'trouwdag',
        'tijdlijn-afronden',
        'cta',
    ]);
});

test('the process page numbers the phases one to six with the wedding day as the fifth', function () {
    $document = documentVoor('/werkwijze');

    expect(tekstenVan($document->querySelectorAll('.tl__badge span')))->toBe(['1', '2', '3', '4', '5', '6'])
        ->and(tekstenVan($document->querySelectorAll('.tl__card h2')))->toBe([
            'Kennismaken en voorstel',
            'Locatie, datum en budget',
            'Leveranciers en styling',
            'Het draaiboek',
            'Afronden',
        ])
        ->and(platteTekst($document->querySelector('[data-cms-strip="trouwdag"] h2')))->toBe('Wat jullie familie die dag doet');
});

test('the process hero links to the meeting and to the packages', function () {
    $document = documentVoor('/werkwijze');

    expect(attributenVan($document->querySelectorAll('[data-cms-strip="hero-werkwijze"] a.btn'), 'href'))->toBe([route('kennismaken'), route('diensten')]);
});
