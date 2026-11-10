<?php

use Dom\Element;
use Dom\HTMLDocument;

beforeEach(function () {
    $this->withoutVite();
});

/**
 * De scripts in <head> die de browser meteen uitvoert.
 *
 * @return list<Element>
 */
function directeScripts(HTMLDocument $document): array
{
    return array_values(array_filter(
        iterator_to_array($document->querySelectorAll('head script')),
        fn (Element $script): bool => ! $script->hasAttribute('type'),
    ));
}

test('the first script in the head denies all four consent signals by default', function () {
    $document = documentVoor(route('home'));

    $eerste = directeScripts($document)[0] ?? null;

    expect($eerste?->hasAttribute('src'))->toBeFalse()
        ->and($eerste?->textContent)->toContain("gtag('consent', 'default', { analytics_storage: 'denied', ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied' });");
});

test('every other call to gtag waits for consent for its category', function () {
    config(['vertrouwdag.ga_id' => 'G-TEST123']);

    $document = documentVoor(route('home'));

    $directMetGtag = array_filter(directeScripts($document), fn (Element $script): bool => str_contains($script->textContent, 'gtag('));
    $wachtend = $document->querySelectorAll('script[data-toestemming]');

    expect($directMetGtag)->toHaveCount(1)
        ->and(attributenVan($wachtend, 'type'))->each->toBe('text/plain');
});

test('consent for a category grants only its own consent signals', function (string $categorie, string $update) {
    $document = documentVoor(route('home'));

    $scripts = tekstenVan($document->querySelectorAll('script[type="text/plain"][data-toestemming="'.$categorie.'"]'));

    expect($scripts)->toBe([$update]);
})->with([
    'statistieken' => ['statistieken', "gtag('consent', 'update', { analytics_storage: 'granted' });"],
    'marketing' => ['marketing', "gtag('consent', 'update', { ad_storage: 'granted', ad_user_data: 'granted', ad_personalization: 'granted' });"],
]);

test('the page mentions no Google Analytics while the measurement id is empty', function () {
    config(['vertrouwdag.ga_id' => null]);

    $response = $this->get(route('home'));

    $response->assertOk()
        ->assertDontSee('googletagmanager', false)
        ->assertDontSee("gtag('config'", false);
});

test('Google Analytics with the id from the config waits for consent for statistics', function () {
    config(['vertrouwdag.ga_id' => 'G-TEST123']);

    $document = documentVoor(route('home'));

    $loader = $document->querySelector('script[data-src^="https://www.googletagmanager.com/"]');
    $configuratie = array_values(array_filter(
        iterator_to_array($document->querySelectorAll('script')),
        fn (Element $script): bool => str_contains($script->textContent, "gtag('config'"),
    ));

    expect($loader?->getAttribute('data-src'))->toBe('https://www.googletagmanager.com/gtag/js?id=G-TEST123')
        ->and($loader?->getAttribute('type'))->toBe('text/plain')
        ->and($loader?->getAttribute('data-toestemming'))->toBe('statistieken')
        ->and($loader?->hasAttribute('src'))->toBeFalse()
        ->and($document->querySelectorAll('script[src*="googletagmanager"]'))->toHaveCount(0);

    expect($configuratie)->toHaveCount(1)
        ->and($configuratie[0]->getAttribute('type'))->toBe('text/plain')
        ->and($configuratie[0]->getAttribute('data-toestemming'))->toBe('statistieken')
        ->and($configuratie[0]->textContent)->toBe("gtag('js', new Date()); gtag('config', 'G-TEST123');");
});
