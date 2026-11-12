<?php

use Database\Seeders\PaginasSeeder;
use Dom\HTMLDocument;
use Made\CmsPackage\Access\Enums\Role;
use Made\CmsPackage\Access\Models\Editor;

beforeEach(function () {
    $this->withoutVite();
    $this->seed(PaginasSeeder::class);
});

test('the checklist page responds with the title and description from the design', function () {
    $document = documentVoor('/checklist');

    expect($document->querySelector('title')?->textContent)->toBe('Gratis trouwplanning-checklist | vertrouwdag')
        ->and($document->querySelector('meta[name="description"]')?->getAttribute('content'))
        ->toBe('Wat regel je wanneer? Download de gratis trouwplanning-checklist van weddingplanner Romy: maand voor maand, van twaalf maanden ervoor tot de week zelf.');
});

test('the seeded checklist page comes from the cms with every strip in the order of the design', function () {
    $this->get('/checklist')->assertOk()->assertViewIs('page');

    expect(striptypenOp('/checklist', documentVoor('/checklist')))->toBe([
        'hero-checklist',
        'checklist-bedankt',
    ]);
});

test('the checklist page asks for a first name, email address, optional wedding date and consent', function () {
    $hero = strookVanType('/checklist', documentVoor('/checklist'), 'hero-checklist');
    $formulier = $hero?->querySelector('form#checklist-form.cl-form');

    expect(platteTekst($hero?->querySelector('h1')))->toBe('Wat regel je wanneer?')
        ->and($hero?->querySelector('h1 em')?->textContent)->toBe('wanneer?')
        ->and(attributenVan($formulier?->querySelectorAll('input'), 'name'))->toBe(['voornaam', 'email', 'trouwdatum', 'avg'])
        ->and(attributenVan($formulier?->querySelectorAll('input[required]'), 'name'))->toBe(['voornaam', 'email', 'avg'])
        ->and($formulier?->querySelector('input[name="email"]')?->getAttribute('type'))->toBe('email')
        ->and($formulier?->querySelector('.check a')?->getAttribute('href'))->toBe(url('/privacyverklaring'))
        ->and($formulier?->querySelector('button[type="submit"]')?->textContent)->toBe('Stuur mij de checklist');
});

test('the thank-you block is hidden until the form is sent and leads to the acquaintance', function () {
    $document = documentVoor('/checklist');
    $bedankt = $document->getElementById('bedankt');

    expect($bedankt)->toBe(strookVanType('/checklist', $document, 'checklist-bedankt'))
        ->and($bedankt?->hasAttribute('hidden'))->toBeTrue()
        ->and(platteTekst($bedankt?->querySelector('h2[tabindex="-1"]')))->toBe('Dank jullie wel')
        ->and($bedankt?->querySelector('a.btn')?->getAttribute('href'))->toBe(url('/kennismaken'));
});

test('the thank-you block is open in the edit mode, so an editor sees what they change', function () {
    $editor = Editor::factory()->withRoles(Role::Admin)->create();

    $response = $this->actingAs($editor, 'made-cms')->get('/checklist?made-cms=edit');
    $response->assertOk();
    $bedankt = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR)->getElementById('bedankt');

    expect($bedankt)->not->toBeNull()
        ->and($bedankt?->hasAttribute('hidden'))->toBeFalse();
});
