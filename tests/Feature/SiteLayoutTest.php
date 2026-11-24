<?php

use Database\Seeders\HomepaginaSeeder;
use Dom\HTMLDocument;
use Illuminate\Support\Facades\Vite;
use Made\CmsPackage\Content\Models\Page;

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

test('the canonical of a page of the cms is its path on the url of the site, without the query string', function (string $url, string $path) {
    $this->withoutVite();
    $this->seed(HomepaginaSeeder::class);
    Page::factory()->published()->at('/blog/archief')->create(['title' => 'Archief']);

    $document = documentVoor($url);
    $canonical = rtrim(url('/'), '/').$path;

    expect($document->querySelector('link[rel="canonical"]')?->getAttribute('href'))->toBe($canonical)
        ->and($document->querySelector('meta[property="og:url"]')?->getAttribute('content'))->toBe($canonical);
})->with([
    'homepage' => ['/?utm_source=nieuwsbrief', '/'],
    'subpage' => ['/blog/archief?pagina=2', '/blog/archief'],
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
    $this->seed(HomepaginaSeeder::class);

    $response = $this->get('/');

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

test('the layout preloads the fonts of the text above the fold, the font of the menu only where the menu shows', function () {
    metGebouwdeFotos();

    $document = renderComponent('<x-layouts::site title="Titel" description="Omschrijving">Inhoud</x-layouts::site>');

    $preloads = [];

    foreach ($document->querySelectorAll('link[rel="preload"][as="font"]') as $link) {
        expect($link->getAttribute('type'))->toBe('font/woff2')
            ->and($link->hasAttribute('crossorigin'))->toBeTrue();

        $preloads[$link->getAttribute('href')] = $link->getAttribute('media');
    }

    expect($preloads)->toBe([
        Vite::asset('resources/fonts/cormorant-latin-500-normal.woff2') => null,
        Vite::asset('resources/fonts/cormorant-latin-400-italic.woff2') => null,
        Vite::asset('resources/fonts/unbounded-latin-500-normal.woff2') => null,
        Vite::asset('resources/fonts/unbounded-latin-400-normal.woff2') => '(min-width: 1041px)',
    ]);
});
