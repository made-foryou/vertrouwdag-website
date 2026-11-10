<?php

use Dom\HTMLDocument;
use Illuminate\Support\Facades\Vite;

test('the layout renders the title, description and canonical the page passes', function () {
    $this->withoutVite();

    $view = $this->blade(
        '<x-layouts::site title="Hart & hoofd | vertrouwdag" description="Testomschrijving" canonical="https://vertrouwdag.nl/diensten">Inhoud</x-layouts::site>'
    );

    $view->assertSee('<html lang="nl">', false)
        ->assertSee('<title>Hart &amp; hoofd | vertrouwdag</title>', false)
        ->assertSee('<meta name="description" content="Testomschrijving">', false)
        ->assertSee('<link rel="canonical" href="https://vertrouwdag.nl/diensten">', false)
        ->assertSeeInOrder(['<a class="skip" href="#inhoud">', '<main id="inhoud">', 'Inhoud', '</main>'], false);
});

test('the homepage responds with one title and a canonical to itself', function () {
    $this->withoutVite();

    $response = $this->get(route('home'));

    $response->assertOk();

    $document = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);
    $titles = $document->querySelectorAll('title');

    expect($titles)->toHaveCount(1)
        ->and(trim($titles->item(0)->textContent))->not->toBeEmpty()
        ->and($document->querySelector('link[rel="canonical"]')?->getAttribute('href'))->toBe(route('home'));
});

test('the homepage loads nothing from an external domain', function () {
    // Forceer de gebouwde manifest, ook als de Vite-devserver lokaal draait.
    Vite::useHotFile(storage_path('framework/testing/geen-vite-devserver'));

    $response = $this->get(route('home'));

    $response->assertOk();

    $document = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);
    $urls = [];

    foreach ($document->querySelectorAll('link[href], script[src], img[src], img[srcset], iframe[src], source[src], source[srcset]') as $element) {
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
