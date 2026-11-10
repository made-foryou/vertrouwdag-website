<?php

beforeEach(function () {
    $this->withoutVite();
});

test('every section of the services page is a strip, in the order of the design', function () {
    $document = documentVoor('/diensten');

    expect(attributenVan($document->querySelectorAll('main > *'), 'data-cms-strip'))->toBe([
        'hero-diensten',
        'lopende-band',
        'pakketten',
        'vergelijking',
        'cta',
    ]);
});

test('the services page shows the three prices including and excluding vat', function (string $plek) {
    $document = documentVoor('/diensten');

    expect(tekstenVan($document->querySelectorAll("{$plek} [data-prijs-incl]")))->toBe(['€1.028,50', '€2.238,50', '€4.235'])
        ->and(tekstenVan($document->querySelectorAll("{$plek} [data-prijs]")))->toBe(['€850', '€1.850', '€3.500']);
})->with([
    'in the folders' => ['.tf__panel'],
    'in the mobile stack' => ['.tf-m .folder'],
    'in the comparison' => ['#vergelijk'],
]);

test('changing a price in the config changes it on the homepage, the services page and its description', function () {
    config(['vertrouwdag.prijzen.dag' => 1000]);

    $homepage = documentVoor('/');
    $diensten = documentVoor('/diensten');

    expect($homepage->querySelector('.folders a.folder [data-prijs-incl="dag"]')?->textContent)->toBe('€1.210')
        ->and(tekstenVan($diensten->querySelectorAll('[data-prijs-incl="dag"]')))->each->toBe('€1.210')
        ->and(tekstenVan($diensten->querySelectorAll('[data-prijs="dag"]')))->each->toBe('€1.000')
        ->and($diensten->querySelector('meta[name="description"]')?->getAttribute('content'))
        ->toStartWith('De Dag in handen (dagcoördinatie) vanaf €1.210, Hand in hand (gedeeltelijke planning) vanaf €2.238,50 en Helemaal in handen (full service) vanaf €4.235, inclusief btw.');
});

test('the folders follow the tabs pattern with the first folder open', function () {
    $document = documentVoor('/diensten');

    $tabjes = $document->querySelectorAll('[data-tabfolders] [role="tablist"] > [role="tab"]');
    $panelen = $document->querySelectorAll('[data-tabfolders] [role="tabpanel"]');

    expect(attributenVan($tabjes, 'aria-controls'))->toBe(['dagcoordinatie', 'partial-planning', 'full-service'])
        ->and(attributenVan($panelen, 'id'))->toBe(['dagcoordinatie', 'partial-planning', 'full-service'])
        ->and(attributenVan($panelen, 'aria-labelledby'))->toBe(attributenVan($tabjes, 'id'))
        ->and(attributenVan($tabjes, 'aria-selected'))->toBe(['true', 'false', 'false'])
        ->and(attributenVan($tabjes, 'tabindex'))->toBe([null, '-1', '-1'])
        ->and($document->querySelectorAll('[role="tabpanel"].is-active'))->toHaveCount(1)
        ->and($panelen->item(0)->classList->contains('is-active'))->toBeTrue();
});

test('every service link on the homepage opens a folder and a card on the services page', function () {
    $links = attributenVan(documentVoor('/')->querySelectorAll('.folders a.folder'), 'href');
    $ankers = array_map(fn (string $link): string => parse_url($link, PHP_URL_FRAGMENT), $links);

    $diensten = documentVoor('/diensten');

    expect($ankers)->toBe(['dagcoordinatie', 'partial-planning', 'full-service'])
        ->and(attributenVan($diensten->querySelectorAll('[role="tabpanel"]'), 'id'))->toBe($ankers)
        ->and(attributenVan($diensten->querySelectorAll('.tf-m [data-deck] > .folder'), 'data-for'))->toBe($ankers);
});

test('the hero links to the meeting and to the comparison', function () {
    $document = documentVoor('/diensten');

    expect(attributenVan($document->querySelectorAll('[data-cms-strip="hero-diensten"] a.btn'), 'href'))->toBe([route('kennismaken'), '#vergelijk'])
        ->and($document->getElementById('vergelijk')?->getAttribute('data-cms-strip'))->toBe('vergelijking');
});

test('the comparison shows what each package contains', function () {
    $tabel = documentVoor('/diensten')->querySelector('#vergelijk table.cmp');
    $budget = $tabel?->querySelectorAll('tbody tr')->item(3);

    expect(attributenVan($tabel?->querySelectorAll('thead th[data-short]'), 'data-short'))->toBe(['De Dag in handen', 'Hand in hand', 'Helemaal in handen'])
        ->and(tekstenVan($tabel?->querySelectorAll('tbody th')))->toBe([
            'Coördinatie op de dag zelf',
            'Draaiboek en tijdsplanning',
            'Leveranciers zoeken en boeken',
            'Budgetoverzicht',
            'Locatie zoeken',
            'Concept en styling',
            'Vanaf, incl. btw',
        ])
        ->and(attributenVan($tabel?->querySelectorAll('tbody tr:nth-child(3) td'), 'class'))->toBe(['nee', 'deels', 'ja'])
        ->and(tekstenVan($budget?->querySelectorAll('td')))->toBe(['Nee', 'Ja', 'Ja'])
        ->and(attributenVan($budget?->querySelectorAll('td'), 'data-l'))->toBe(['De Dag in handen', 'Hand in hand', 'Helemaal in handen']);
});
