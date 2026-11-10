<?php

beforeEach(function () {
    $this->withoutVite();
});

test('the homepage shows the three services with their price from the config', function () {
    config([
        'vertrouwdag.prijzen.dag' => 1000,
        'vertrouwdag.prijzen.partial' => 2000,
        'vertrouwdag.prijzen.full' => 4000,
    ]);

    $document = documentVoor('/');

    $diensten = $document->querySelectorAll('.folders a.folder');

    expect(tekstenVan($document->querySelectorAll('.folders a.folder h3')))->toBe(['De Dag in handen', 'Hand in hand', 'Helemaal in handen'])
        ->and(tekstenVan($document->querySelectorAll('.folders [data-prijs-incl]')))->toBe(['€1.210', '€2.420', '€4.840'])
        ->and(tekstenVan($document->querySelectorAll('.folders [data-prijs]')))->toBe(['€1.000', '€2.000', '€4.000'])
        ->and(attributenVan($diensten, 'href'))->toBe([
            route('diensten').'#dagcoordinatie',
            route('diensten').'#partial-planning',
            route('diensten').'#full-service',
        ]);
});

test('every section of the homepage is a strip, in the order of the design', function () {
    $document = documentVoor('/');

    $secties = $document->querySelectorAll('main > *');

    expect(attributenVan($secties, 'data-cms-strip'))->toBe([
        'hero',
        'lopende-band',
        'herkenning',
        'twee-kolommen',
        'diensten',
        'stappen',
        'over-mij-intro',
        'reviews',
        'leveranciers',
        'checklist-aanmelding',
        'cta',
    ]);
});

test('the homepage shows two placeholder reviews until the real ones arrive', function () {
    $document = documentVoor('/');

    $reviews = $document->querySelectorAll('#reviews [data-cms-items="reviews"] > figure.review');

    expect($reviews)->toHaveCount(2)
        ->and(tekstenVan($document->querySelectorAll('#reviews blockquote')))->each->toStartWith('"[Tekst review');
});

test('the homepage describes the business as structured data from the config', function () {
    config([
        'vertrouwdag.email' => 'test@voorbeeld.nl',
        'vertrouwdag.whatsapp.nummer' => '31600000000',
        'vertrouwdag.instagram' => 'https://www.instagram.com/test/',
    ]);

    $document = documentVoor('/');
    $scripts = $document->querySelectorAll('script[type="application/ld+json"]');

    expect($scripts)->toHaveCount(1);

    expect(json_decode($scripts->item(0)->textContent, true, flags: JSON_THROW_ON_ERROR))
        ->toMatchArray([
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => 'vertrouwdag',
            'description' => 'Weddingplanner in Zuid-Holland',
            'url' => route('home'),
            'email' => 'test@voorbeeld.nl',
            'telephone' => '+31600000000',
            'areaServed' => 'Zuid-Holland',
            'sameAs' => ['https://www.instagram.com/test/'],
        ])
        ->toHaveKey('image');
});

test('suppliers can mail about working together or send a WhatsApp message', function () {
    config([
        'vertrouwdag.email' => 'test@voorbeeld.nl',
        'vertrouwdag.whatsapp.nummer' => '31600000000',
        'vertrouwdag.whatsapp.tekst' => 'Hoi!',
    ]);

    $document = documentVoor('/');
    $strook = $document->querySelector('#samenwerken');

    expect($strook?->querySelector('a.btn')?->getAttribute('href'))->toBe('mailto:test@voorbeeld.nl?subject=Samenwerking%20met%20vertrouwdag')
        ->and($strook?->querySelector('a.founding__alt')?->getAttribute('href'))->toBe('https://wa.me/31600000000?text=Hoi%21');
});

test('the checklist form on the homepage does not ask for the wedding date', function () {
    $document = documentVoor('/');

    $formulier = $document->querySelector('#checklist form#checklist-form');

    expect(attributenVan($formulier?->querySelectorAll('input'), 'name'))->toBe(['voornaam', 'email', 'avg'])
        ->and(platteTekst($formulier?->querySelector('h2')))->toBe('Wat regel je wanneer?');
});

test('the homepage links its buttons to the right pages', function () {
    $document = documentVoor('/');

    expect(attributenVan($document->querySelectorAll('[data-cms-strip="hero"] a.btn'), 'href'))->toBe([route('kennismaken'), route('diensten')])
        ->and($document->querySelector('[data-cms-strip="diensten"] a.btn')?->getAttribute('href'))->toBe(route('diensten'))
        ->and($document->querySelector('[data-cms-strip="stappen"] a.btn')?->getAttribute('href'))->toBe(route('werkwijze'))
        ->and($document->querySelector('[data-cms-strip="over-mij-intro"] a.btn')?->getAttribute('href'))->toBe(route('over-mij'));
});
