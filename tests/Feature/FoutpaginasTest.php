<?php

use Dom\HTMLDocument;
use Illuminate\Support\Facades\View;

beforeEach(function () {
    $this->withoutVite();
});

test('an unknown address shows the not found page in the layout of the site, hidden from search engines', function (string $url) {
    $response = $this->get($url);

    $response->assertNotFound();

    $document = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);

    expect(tekstenVan($document->querySelectorAll('title')))->toBe(['Pagina niet gevonden | vertrouwdag'])
        ->and($document->querySelector('meta[name="robots"]')?->getAttribute('content'))->toBe('noindex')
        ->and($document->querySelector('link[rel="canonical"]'))->toBeNull()
        ->and(platteTekst($document->querySelector('main h1')))->toBe('Deze pagina is zoek')
        ->and(attributenVan($document->querySelectorAll('main a.btn'), 'href'))->toBe([route('home'), route(config('vertrouwdag.cta.route'))])
        ->and($document->querySelector('#site-nav'))->not->toBeNull();
})->with([
    'an address without a route' => ['/bestaat-niet'],
    'a blog without an article' => ['/blog/bestaat-niet'],
]);

test('a server error shows its own bare page with the mail address, hidden from search engines', function () {
    config(['app.debug' => false, 'vertrouwdag.email' => 'test@voorbeeld.nl']);
    // Een fout tijdens het renderen van een bestaande pagina. Een eigen testroute kan niet: die komt na de catch-all
    // van het CMS (Route::madeCms) en wordt dus nooit bereikt.
    View::composer('pages.home', fn () => throw new RuntimeException('Iets ging stuk.'));

    $response = $this->get('/');

    $response->assertInternalServerError();

    $document = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);

    expect(tekstenVan($document->querySelectorAll('title')))->toBe(['Er ging iets mis | vertrouwdag'])
        ->and($document->querySelector('meta[name="robots"]')?->getAttribute('content'))->toBe('noindex')
        ->and(platteTekst($document->querySelector('main h1')))->toBe('Er ging iets mis')
        ->and($document->querySelector('main a[href^="mailto:test@voorbeeld.nl"]'))->not->toBeNull()
        ->and($document->querySelector('main a.btn')?->getAttribute('href'))->toBe(url('/'))
        ->and($document->querySelector('#site-nav'))->toBeNull()
        ->and($response->getContent())->not->toContain('Iets ging stuk.');
});
