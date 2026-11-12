<?php

use Database\Seeders\HomepaginaSeeder;
use Dom\Node;

beforeEach(function () {
    $this->withoutVite();
});

test('every page shows the main menu with the right active link', function (string $adres, ?string $activeRoute) {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor($adres);
    $menu = config('vertrouwdag.menu');

    $links = $document->querySelectorAll('#site-nav a:not(.btn)');

    expect(attributenVan($links, 'href'))->toBe(array_map(fn (string $route): string => route($route), array_keys($menu)))
        ->and(tekstenVan($links))->toBe(array_values($menu));

    $current = $document->querySelectorAll('#site-nav [aria-current="page"]');

    if ($activeRoute === null) {
        expect($current)->toHaveCount(0);
    } else {
        expect($current)->toHaveCount(1)
            ->and($current->item(0)->getAttribute('href'))->toBe(route($activeRoute));
    }

    expect($document->querySelectorAll('header [aria-current]')->length)->toBeLessThanOrEqual(1);
})->with([
    'home' => ['/', null],
    'diensten' => ['/diensten', 'diensten'],
    'werkwijze' => ['/werkwijze', 'werkwijze'],
    'over mij' => ['/over-mij', 'over-mij'],
    'blog' => ['/blog', 'blog'],
    'blogarchief' => ['/blog/archief', 'blog'],
    'blogartikel' => ['/blog/wanneer-weddingplanner-inschakelen', 'blog'],
    'veelgestelde vragen' => ['/veelgestelde-vragen', 'veelgestelde-vragen'],
    'checklist' => ['/checklist', null],
    'kennismaken' => ['/kennismaken', null],
    'privacyverklaring' => ['/privacyverklaring', null],
    'cookiebeleid' => ['/cookiebeleid', null],
    'algemene voorwaarden' => ['/algemene-voorwaarden', null],
]);

test('the header links the logo home and the call to action to the configured route', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');
    $cta = config('vertrouwdag.cta');

    expect($document->querySelector('.site-header__logo')?->getAttribute('href'))->toBe(url('/'));

    foreach (['#site-nav a.btn', '.site-header__cta'] as $selector) {
        $button = $document->querySelector($selector);

        expect($button?->getAttribute('href'))->toBe(route($cta['route']))
            ->and(trim($button->textContent))->toBe($cta['label']);
    }
});

test('the header has a closed menu toggle for the mobile menu', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    $toggle = $document->querySelector('button.nav-toggle');

    expect($toggle)->not->toBeNull()
        ->and($toggle->getAttribute('type'))->toBe('button')
        ->and($toggle->getAttribute('aria-controls'))->toBe('site-nav')
        ->and($toggle->getAttribute('aria-expanded'))->toBe('false')
        ->and($toggle->getAttribute('aria-label'))->not->toBeEmpty()
        ->and($document->getElementById('site-nav'))->not->toBeNull();
});

test('the menu follows its toggle so the tab key continues into the opened menu', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    $toggle = $document->querySelector('button.nav-toggle');
    $menu = $document->getElementById('site-nav');

    expect($toggle->compareDocumentPosition($menu) & Node::DOCUMENT_POSITION_FOLLOWING)->toBeGreaterThan(0);
});

test('the footer and the floating button take the contact details from the config', function () {
    config([
        'vertrouwdag.email' => 'test@voorbeeld.nl',
        'vertrouwdag.email_onderwerp' => 'Vraag & antwoord',
        'vertrouwdag.whatsapp.nummer' => '31600000000',
        'vertrouwdag.whatsapp.tekst' => 'Hoi! Een vraag?',
        'vertrouwdag.instagram' => 'https://www.instagram.com/test/',
    ]);
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');
    $whatsappUrl = 'https://wa.me/31600000000?text=Hoi%21%20Een%20vraag%3F';

    $mail = $document->querySelector('.site-footer a[href^="mailto:"]');

    expect($mail?->getAttribute('href'))->toBe('mailto:test@voorbeeld.nl?subject=Vraag%20%26%20antwoord')
        ->and(trim($mail->textContent))->toBe('test@voorbeeld.nl');

    $whatsappLinks = $document->querySelectorAll('.site-footer a[href^="https://wa.me/"], a.wa');

    expect($whatsappLinks)->toHaveCount(2)
        ->and(attributenVan($whatsappLinks, 'href'))->each->toBe($whatsappUrl)
        ->and(attributenVan($whatsappLinks, 'target'))->each->toBe('_blank')
        ->and(attributenVan($whatsappLinks, 'rel'))->each->toBe('noopener');

    $instagram = $document->querySelector('.site-footer a[href^="https://www.instagram.com/"]');

    expect($instagram?->getAttribute('href'))->toBe('https://www.instagram.com/test/')
        ->and($instagram->getAttribute('target'))->toBe('_blank')
        ->and($instagram->getAttribute('rel'))->toBe('noopener');
});

test('the footer links to the configured pages and legal pages and opens the cookie settings', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');
    $footer = config('vertrouwdag.footer');

    $pageLinks = $document->querySelectorAll('.site-footer nav a');

    expect(attributenVan($pageLinks, 'href'))->toBe(array_map(fn (string $route): string => route($route), array_keys($footer['paginas'])))
        ->and(tekstenVan($pageLinks))->toBe(array_values($footer['paginas']));

    foreach ($footer['juridisch'] as $route => $label) {
        $link = $document->querySelector('.site-footer a[href="'.route($route).'"]');

        expect($link)->not->toBeNull()
            ->and(trim($link->textContent))->toBe($label);
    }

    $cookieButton = $document->querySelector('.site-footer button[data-cookie-open]');

    expect($cookieButton)->not->toBeNull()
        ->and($cookieButton->getAttribute('type'))->toBe('button');
});

test('the footer shows no chamber of commerce line while the number is empty', function () {
    config(['vertrouwdag.kvk' => null]);
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    expect($document->querySelector('.site-footer__bottom')?->textContent)->not->toContain('KvK');
});

test('the footer shows the chamber of commerce number once it is filled in', function () {
    config(['vertrouwdag.kvk' => '12345678']);
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    expect($document->querySelector('.site-footer__bottom')?->textContent)->toContain('KvK 12345678');
});

test('the footer shows the current year', function () {
    $this->travelTo(now()->setDate(2031, 6, 1));
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    expect(preg_replace('/\s+/', ' ', $document->querySelector('.site-footer__bottom')?->textContent))
        ->toContain('© 2031 vertrouwdag');
});
