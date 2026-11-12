<?php

use Database\Seeders\PaginasSeeder;
use Dom\HTMLDocument;
use Made\CmsPackage\Access\Enums\Role;
use Made\CmsPackage\Access\Models\Editor;

beforeEach(function () {
    $this->withoutVite();
    $this->seed(PaginasSeeder::class);
});

test('the seeded services page comes from the cms with every strip in the order of the design', function () {
    $response = $this->get('/diensten');

    $response->assertOk()->assertViewIs('page');

    expect(striptypenOp('/diensten', documentVoor('/diensten')))->toBe([
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

test('changing a price in the packages changes it on the homepage, the services page, its description and the FAQ', function () {
    wijzigPakketten(['dag' => ['prijs' => '1000']]);

    $homepage = documentVoor('/');
    $diensten = documentVoor('/diensten');
    $faq = documentVoor('/veelgestelde-vragen');

    expect(tekstenVan($homepage->querySelectorAll('.folders a.folder [data-prijs-incl="dag"]')))->toBe(['€1.210'])
        ->and(tekstenVan($homepage->querySelectorAll('.folders a.folder [data-prijs="dag"]')))->toBe(['€1.000'])
        ->and(tekstenVan($diensten->querySelectorAll('[data-prijs-incl="dag"]')))->toBe(['€1.210', '€1.210', '€1.210'])
        ->and(tekstenVan($diensten->querySelectorAll('[data-prijs="dag"]')))->toBe(['€1.000', '€1.000', '€1.000'])
        ->and($diensten->querySelector('meta[name="description"]')?->getAttribute('content'))
        ->toBe('De Dag in handen (dagcoördinatie) vanaf €1.210, Hand in hand (gedeeltelijke planning) vanaf €2.238,50 en Helemaal in handen (full service) vanaf €4.235, inclusief btw. Vaste vanaf-prijzen, zodat jullie vooraf weten waar jullie aan toe zijn.')
        ->and(tekstenVan($faq->querySelectorAll('details.faq [data-prijs-incl="dag"]')))->toBe(['€1.210']);
});

test('a price in the draft shows in the edit mode, and only after publishing to visitors', function () {
    wijzigPakketten(['dag' => ['prijs' => '1000']], publiceren: false);
    $editor = Editor::factory()->withRoles(Role::Admin)->create();

    $bezoeker = documentVoor('/diensten');
    $homepage = documentVoor('/');

    $response = $this->actingAs($editor, 'made-cms')->get('/diensten?made-cms=edit');
    $response->assertOk();
    $bewerkmodus = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);

    expect(tekstenVan($bezoeker->querySelectorAll('#vergelijk [data-prijs-incl="dag"]')))->toBe(['€1.028,50'])
        ->and(tekstenVan($homepage->querySelectorAll('.folders [data-prijs-incl="dag"]')))->toBe(['€1.028,50'])
        ->and(tekstenVan($bewerkmodus->querySelectorAll('[data-prijs-incl="dag"]')))->toBe(['€1.210', '€1.210', '€1.210']);
});

test('a price that is no whole amount leaves out the price, but not the page', function () {
    wijzigPakketten(['partial' => ['prijs' => 'op aanvraag']]);

    $homepage = documentVoor('/');
    $diensten = documentVoor('/diensten');
    $faq = documentVoor('/veelgestelde-vragen');

    expect($homepage->querySelectorAll('.folders [data-prijs-incl]'))->toHaveCount(2)
        ->and($homepage->querySelector('.folders [data-prijs-incl="partial"]'))->toBeNull()
        ->and($diensten->querySelector('[data-prijs-incl="partial"]'))->toBeNull()
        ->and(tekstenVan($diensten->querySelectorAll('#vergelijk [data-prijs-incl]')))->toBe(['€1.028,50', '€4.235'])
        ->and($diensten->querySelector('meta[name="description"]')?->getAttribute('content'))
        ->toBe('Van dagcoördinatie tot full service: pakketten met een vaste vanaf-prijs, zodat jullie vooraf weten waar jullie aan toe zijn.')
        ->and($faq->querySelector('details.faq [data-prijs-incl]'))->toBeNull();
});

test('the anchors of the folders stay when an editor renames a service', function () {
    wijzigPakketten(['dag' => ['soort' => 'Alleen de dag', 'titel' => 'De Grote Dag']]);

    $ankers = ['dagcoordinatie', 'partial-planning', 'full-service'];
    $diensten = documentVoor('/diensten');
    $links = attributenVan(documentVoor('/')->querySelectorAll('.folders a.folder'), 'href');

    expect(attributenVan($diensten->querySelectorAll('[data-tabfolders] [role="tabpanel"]'), 'id'))->toBe($ankers)
        ->and(attributenVan($diensten->querySelectorAll('.tf-m [data-deck] > .folder'), 'data-for'))->toBe($ankers)
        ->and(platteTekst($diensten->querySelector('#dagcoordinatie h2')))->toBe('De Grote Dag')
        ->and(attributenVan($diensten->querySelectorAll('#vergelijk thead th[data-short]'), 'data-short'))->toBe(['De Grote Dag', 'Hand in hand', 'Helemaal in handen'])
        ->and($diensten->querySelector('meta[name="description"]')?->getAttribute('content'))->toStartWith('De Grote Dag (alleen de dag) vanaf €1.028,50, Hand in hand')
        ->and(array_map(fn (string $link): ?string => parse_url($link, PHP_URL_FRAGMENT), $links))->toBe($ankers);
});

test('the services page has its own texts in the running band and its own closing strip', function () {
    $document = documentVoor('/diensten');

    expect(tekstenVan(strookVanType('/diensten', $document, 'lopende-band')?->querySelectorAll('.marquee__track > span')))
        ->toBe(['De Dag in handen', 'Hand in hand', 'Helemaal in handen', 'Maatwerk mogelijk'])
        ->and(platteTekst(strookVanType('/diensten', $document, 'cta')?->querySelector('h2')))->toBe('Twijfelen jullie welk pakket?');
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

    expect(array_map(fn (string $link): string => parse_url($link, PHP_URL_PATH), $links))->each->toBe('/diensten')
        ->and($ankers)->toBe(['dagcoordinatie', 'partial-planning', 'full-service'])
        ->and(attributenVan($diensten->querySelectorAll('[role="tabpanel"]'), 'id'))->toBe($ankers)
        ->and(attributenVan($diensten->querySelectorAll('.tf-m [data-deck] > .folder'), 'data-for'))->toBe($ankers);
});

test('the hero links to the meeting and to the comparison', function () {
    $document = documentVoor('/diensten');

    expect(attributenVan(strookVanType('/diensten', $document, 'hero-diensten')?->querySelectorAll('a.btn'), 'href'))->toBe([url('/kennismaken'), '#vergelijk'])
        ->and($document->getElementById('vergelijk'))->toBe(strookVanType('/diensten', $document, 'vergelijking'));
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
