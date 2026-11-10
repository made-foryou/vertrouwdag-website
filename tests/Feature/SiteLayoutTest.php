<?php

use Dom\HTMLDocument;
use Illuminate\Support\Facades\Vite;

test('the layout renders the title, description, canonical and open graph tags the page passes', function () {
    $this->withoutVite();

    $view = $this->blade(
        '<x-layouts::site title="Hart & hoofd | vertrouwdag" description="Testomschrijving" og-title="Hart & hoofd · vertrouwdag" og-description="Kort" og-type="article" canonical="https://vertrouwdag.nl/diensten">Inhoud</x-layouts::site>'
    );

    $view->assertSee('<html lang="nl">', false)
        ->assertSee('<title>Hart &amp; hoofd | vertrouwdag</title>', false)
        ->assertSee('<meta name="description" content="Testomschrijving">', false)
        ->assertSee('<link rel="canonical" href="https://vertrouwdag.nl/diensten">', false)
        ->assertSeeInOrder([
            '<meta property="og:type" content="article">',
            '<meta property="og:title" content="Hart &amp; hoofd · vertrouwdag">',
            '<meta property="og:description" content="Kort">',
            '<meta property="og:url" content="https://vertrouwdag.nl/diensten">',
        ], false)
        ->assertDontSee('name="robots"', false)
        ->assertSeeInOrder(['<a class="skip" href="#inhoud">', '<main id="inhoud">', 'Inhoud', '</main>'], false);
});

test('the open graph description falls back to the description and the type to website', function () {
    $this->withoutVite();

    $document = renderComponent('<x-layouts::site title="Titel" description="Testomschrijving" og-title="Titel">Inhoud</x-layouts::site>');

    expect($document->querySelector('meta[property="og:description"]')?->getAttribute('content'))->toBe('Testomschrijving')
        ->and($document->querySelector('meta[property="og:type"]')?->getAttribute('content'))->toBe('website');
});

test('the canonical points to the same path on the live address from the config, without the query string', function (string $url, string $canonical) {
    $this->withoutVite();
    config(['vertrouwdag.canonieke_url' => 'https://voorbeeld.nl/']);

    $document = documentVoor($url);

    expect($document->querySelector('link[rel="canonical"]')?->getAttribute('href'))->toBe($canonical)
        ->and($document->querySelector('meta[property="og:url"]')?->getAttribute('content'))->toBe($canonical);
})->with([
    'homepage' => ['/?utm_source=nieuwsbrief', 'https://voorbeeld.nl/'],
    'subpage' => ['/blog/archief?pagina=2', 'https://voorbeeld.nl/blog/archief'],
]);

test('a page with noindex asks search engines to skip it and has no canonical', function () {
    $this->withoutVite();

    $document = renderComponent('<x-layouts::site title="Titel" description="Omschrijving" og-title="Titel" noindex>Inhoud</x-layouts::site>');

    expect($document->querySelector('meta[name="robots"]')?->getAttribute('content'))->toBe('noindex')
        ->and($document->querySelector('link[rel="canonical"]'))->toBeNull()
        ->and($document->querySelector('meta[property="og:url"]'))->toBeNull();
});

test('the homepage loads nothing from an external domain', function () {
    // Forceer de gebouwde manifest, ook als de Vite-devserver lokaal draait.
    Vite::useHotFile(storage_path('framework/testing/geen-vite-devserver'));

    $response = $this->get(route('home'));

    $response->assertOk();

    $document = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);
    $urls = [];

    // De canonical laadt niets: hij wijst bewust naar het live adres.
    foreach ($document->querySelectorAll('link[href]:not([rel="canonical"]), script[src], img[src], img[srcset], iframe[src], source[src], source[srcset]') as $element) {
        foreach (['href', 'src'] as $attribute) {
            if ($element->hasAttribute($attribute)) {
                $urls[] = $element->getAttribute($attribute);
            }
        }

        foreach (explode(',', $element->getAttribute('srcset') ?? '') as $candidate) {
            if (trim($candidate) !== '') {
                $urls[] = strtok(trim($candidate), ' ');
            }
        }
    }

    $appHost = parse_url(config('app.url'), PHP_URL_HOST);
    $externalUrls = array_filter(
        $urls,
        fn (string $url): bool => ! in_array(parse_url($url, PHP_URL_HOST), [null, $appHost], true)
    );

    expect($urls)->not->toBeEmpty()
        ->and($externalUrls)->toBe([]);
});
