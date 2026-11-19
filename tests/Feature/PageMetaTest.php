<?php

use Database\Seeders\DienstenpaginaSeeder;
use Made\CmsPackage\Content\Actions\UpdatePageMeta;
use Made\CmsPackage\Content\Data\PageMetaChanges;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\Page;

beforeEach(function () {
    $this->withoutVite();
});

test('a page shows the title, description and canonical address from the cms', function () {
    Page::factory()->published()->at('/testpagina')->withMeta('Een eigen titel | vertrouwdag', 'Een eigen omschrijving.')->create(['title' => 'Testpagina']);

    $document = documentVoor('/testpagina?utm_source=nieuwsbrief');

    expect(tekstenVan($document->querySelectorAll('title')))->toBe(['Een eigen titel | vertrouwdag'])
        ->and(attributenVan($document->querySelectorAll('meta[name="description"]'), 'content'))->toBe(['Een eigen omschrijving.'])
        ->and(attributenVan($document->querySelectorAll('link[rel="canonical"]'), 'href'))->toBe([url('/testpagina')])
        ->and($document->querySelector('meta[name="robots"]'))->toBeNull()
        ->and($document->querySelector('meta[property="og:type"]')?->getAttribute('content'))->toBe('website')
        ->and($document->querySelector('meta[property="og:site_name"]')?->getAttribute('content'))->toBe('vertrouwdag')
        ->and($document->querySelector('meta[property="og:title"]')?->getAttribute('content'))->toBe('Een eigen titel | vertrouwdag')
        ->and(attributenVan($document->querySelectorAll('meta[property="og:description"]'), 'content'))->toBe(['Een eigen omschrijving.'])
        ->and($document->querySelector('meta[property="og:url"]')?->getAttribute('content'))->toBe(url('/testpagina'))
        ->and($document->querySelector('meta[name="made-cms:page"]')?->getAttribute('content'))->toBe('Testpagina');
});

test('a page without a title of its own is named after the page and the site', function () {
    Page::factory()->published()->at('/testpagina')->create(['title' => 'Testpagina']);
    Page::factory()->published()->at('/')->create(['title' => 'Home']);

    expect(tekstenVan(documentVoor('/testpagina')->querySelectorAll('title')))->toBe(['Testpagina · vertrouwdag'])
        ->and(tekstenVan(documentVoor('/')->querySelectorAll('title')))->toBe(['vertrouwdag']);
});

test('a page without a description of its own has none, so the cms can tell the editor', function () {
    Page::factory()->published()->at('/testpagina')->create(['title' => 'Testpagina']);

    $document = documentVoor('/testpagina');

    expect($document->querySelector('meta[name="description"]'))->toBeNull()
        ->and($document->querySelector('meta[property="og:description"]'))->toBeNull();
});

test('the services page describes the services with their current prices, unless the editor wrote a description', function () {
    $this->seed(DienstenpaginaSeeder::class);
    config(['vertrouwdag.btw_percentage' => 21]);

    $generated = 'De Dag in handen (dagcoördinatie) vanaf €1.028,50, Hand in hand (gedeeltelijke planning) vanaf €2.238,50 en Helemaal in handen (full service) vanaf €4.235, inclusief btw. Vaste vanaf-prijzen, zodat jullie vooraf weten waar jullie aan toe zijn.';
    $document = documentVoor('/diensten');

    expect(attributenVan($document->querySelectorAll('meta[name="description"]'), 'content'))->toBe([$generated])
        ->and(attributenVan($document->querySelectorAll('meta[property="og:description"]'), 'content'))->toBe([$generated]);

    $page = Page::query()->atPath(PagePath::fromString('/diensten'))->sole();
    UpdatePageMeta::run($page, new PageMetaChanges($page->meta_title, 'Drie pakketten, van de dag zelf tot alles.', true, true));
    $document = documentVoor('/diensten');

    expect(attributenVan($document->querySelectorAll('meta[name="description"]'), 'content'))->toBe(['Drie pakketten, van de dag zelf tot alles.'])
        ->and(attributenVan($document->querySelectorAll('meta[property="og:description"]'), 'content'))->toBe(['Drie pakketten, van de dag zelf tot alles.']);
});

test('an article without a description of its own is described by its intro', function () {
    artikelOnderBlog('trouwen-in-de-winter', [
        'titel' => 'Trouwen in de *winter*',
        'intro' => 'Kort licht, warme kleuren en een feest binnen.',
    ]);

    $document = documentVoor('/blog/trouwen-in-de-winter');

    expect(attributenVan($document->querySelectorAll('meta[name="description"]'), 'content'))->toBe(['Kort licht, warme kleuren en een feest binnen.'])
        ->and(attributenVan($document->querySelectorAll('meta[property="og:description"]'), 'content'))->toBe(['Kort licht, warme kleuren en een feest binnen.']);
});

test('a page that google may not show, or that is hidden from visitors, asks search engines to skip it', function (string $state) {
    Page::factory()->published()->at('/testpagina')->{$state}()->create(['title' => 'Testpagina']);

    expect(documentVoor('/testpagina')->querySelector('meta[name="robots"]')?->getAttribute('content'))->toBe('noindex');
})->with([
    'noindex in the cms' => ['noindex'],
    'hidden from visitors' => ['hidden'],
]);

test('a page without a share image of its own shares the default image of the site', function () {
    Page::factory()->published()->at('/testpagina')->create(['title' => 'Testpagina']);

    $document = documentVoor('/testpagina');

    expect($document->querySelector('meta[property="og:image"]')?->getAttribute('content'))->toBe(url('/deelafbeelding.jpg'))
        ->and($document->querySelector('meta[property="og:image:width"]')?->getAttribute('content'))->toBe('1100')
        ->and($document->querySelector('meta[property="og:image:height"]')?->getAttribute('content'))->toBe('733')
        ->and($document->querySelector('meta[property="og:image:alt"]')?->getAttribute('content'))->toBe('Bruidspaar wandelt door een weids landschap')
        ->and($document->querySelector('meta[name="twitter:card"]')?->getAttribute('content'))->toBe('summary_large_image')
        ->and(file_exists(public_path('deelafbeelding.jpg')))->toBeTrue();
});

test('a page with a share image of its own shares that image', function () {
    $photo = Media::factory()->withAlt('Romy aan tafel')->create();
    Page::factory()->published()->at('/testpagina')->withShareImage($photo)->create(['title' => 'Testpagina']);

    $document = documentVoor('/testpagina');

    expect($document->querySelector('meta[property="og:image"]')?->getAttribute('content'))->toEndWith("/{$photo->path}")
        ->and($document->querySelector('meta[property="og:image:alt"]')?->getAttribute('content'))->toBe('Romy aan tafel');
});
