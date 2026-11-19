<?php

use Made\CmsPackage\Content\Models\Page;

test('the sitemap lists every published page google may show, and nothing else', function () {
    Page::factory()->published()->at('/zichtbaar')->create(['title' => 'Zichtbaar']);
    Page::factory()->published()->at('/niet-voor-google')->noindex()->create(['title' => 'Niet voor Google']);
    Page::factory()->published()->at('/verborgen')->hidden()->create(['title' => 'Verborgen']);
    Page::factory()->at('/concept')->create(['title' => 'Concept']);

    $response = $this->get('/sitemap.xml');

    $response->assertOk();
    expect($response->headers->get('Content-Type'))->toStartWith('application/xml')
        ->and($response->getContent())->toContain('<loc>'.url('/zichtbaar').'</loc>')
        ->not->toContain('/niet-voor-google')
        ->not->toContain('/verborgen')
        ->not->toContain('/concept');
});

test('robots.txt comes from the cms and names the sitemap', function () {
    $response = $this->get('/robots.txt');

    $response->assertOk();
    expect($response->getContent())->toContain('User-agent: *')
        ->toContain('Sitemap: '.url('/sitemap.xml'))
        // The web server would serve a file in public before Laravel, and that one does not know the sitemap.
        ->and(file_exists(public_path('robots.txt')))->toBeFalse();
});
