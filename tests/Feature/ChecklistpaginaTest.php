<?php

beforeEach(function () {
    $this->withoutVite();
});

test('the checklist page responds with the title and description from the design', function () {
    $document = documentVoor('/checklist');

    expect($document->querySelector('title')?->textContent)->toBe('Gratis trouwplanning-checklist | vertrouwdag')
        ->and($document->querySelector('meta[name="description"]')?->getAttribute('content'))
        ->toBe('Wat regel je wanneer? Download de gratis trouwplanning-checklist van weddingplanner Romy: maand voor maand, van twaalf maanden ervoor tot de week zelf.');
});

test('every section of the checklist page is a strip, in the order of the design', function () {
    $document = documentVoor('/checklist');

    expect(attributenVan($document->querySelectorAll('main > *'), 'data-cms-strip'))->toBe([
        'hero-checklist',
        'checklist-bedankt',
    ]);
});

test('the checklist page asks for a first name, email address, optional wedding date and consent', function () {
    $document = documentVoor('/checklist');
    $hero = $document->querySelector('[data-cms-strip="hero-checklist"]');
    $formulier = $hero?->querySelector('form#checklist-form.cl-form');

    expect(platteTekst($hero?->querySelector('h1')))->toBe('Wat regel je wanneer?')
        ->and($hero?->querySelector('h1 em')?->textContent)->toBe('wanneer?')
        ->and(attributenVan($formulier?->querySelectorAll('input'), 'name'))->toBe(['voornaam', 'email', 'trouwdatum', 'avg'])
        ->and(attributenVan($formulier?->querySelectorAll('input[required]'), 'name'))->toBe(['voornaam', 'email', 'avg'])
        ->and($formulier?->querySelector('input[name="email"]')?->getAttribute('type'))->toBe('email')
        ->and($formulier?->querySelector('.check a')?->getAttribute('href'))->toBe(route('privacyverklaring'))
        ->and($formulier?->querySelector('button[type="submit"]')?->textContent)->toBe('Stuur mij de checklist');
});

test('the thank-you block is hidden until the form is sent and leads to the acquaintance', function () {
    $document = documentVoor('/checklist');
    $bedankt = $document->getElementById('bedankt');

    expect($bedankt?->getAttribute('data-cms-strip'))->toBe('checklist-bedankt')
        ->and($bedankt?->hasAttribute('hidden'))->toBeTrue()
        ->and(platteTekst($bedankt?->querySelector('h2[tabindex="-1"]')))->toBe('Dank jullie wel')
        ->and($bedankt?->querySelector('a.btn')?->getAttribute('href'))->toBe(route('kennismaken'));
});
