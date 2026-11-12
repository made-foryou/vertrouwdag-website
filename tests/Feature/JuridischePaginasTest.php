<?php

use Database\Seeders\CookiebeleidSeeder;
use Database\Seeders\PaginasSeeder;
use Database\Seeders\PrivacyverklaringSeeder;

beforeEach(function () {
    $this->withoutVite();
});

describe('the seeded legal pages', function () {
    beforeEach(function () {
        $this->seed(PaginasSeeder::class);
    });

    test('a legal page comes from the cms as one legal strip with the title and heading from the design', function (string $url, string $titel, string $kop, string $accent) {
        $this->get($url)->assertOk()->assertViewIs('page');

        $document = documentVoor($url);
        $strook = $document->querySelector('main > section.wrap.legal');

        expect($document->querySelector('title')?->textContent)->toBe($titel)
            ->and(striptypenOp($url, $document))->toBe(['juridisch'])
            ->and($strook?->querySelector('p.eb')?->textContent)->toBe('Goed om te weten')
            ->and(platteTekst($strook?->querySelector('h1')))->toBe($kop)
            ->and($strook?->querySelector('h1 em')?->textContent)->toBe($accent)
            ->and(platteTekst($strook?->querySelector('.legal__meta')))->toBe('Laatst bijgewerkt: [datum]');
    })->with([
        'privacyverklaring' => ['/privacyverklaring', 'Privacyverklaring | vertrouwdag', 'Privacyverklaring', 'verklaring'],
        'cookiebeleid' => ['/cookiebeleid', 'Cookiebeleid | vertrouwdag', 'Cookiebeleid', 'beleid'],
        'algemene voorwaarden' => ['/algemene-voorwaarden', 'Algemene voorwaarden | vertrouwdag', 'Algemene voorwaarden', 'voorwaarden'],
    ]);

    test('the privacy statement and the terms keep the placeholder for Romy until the final text arrives, without sections or a button', function (string $url, string $placeholder) {
        $prose = documentVoor($url)->querySelector('.legal .prose');

        expect(platteTekst($prose?->querySelector('[data-cms-field="inleiding"]')))->toBe($placeholder)
            ->and($prose?->querySelector('h2'))->toBeNull()
            ->and($prose?->querySelector('button'))->toBeNull();
    })->with([
        'privacyverklaring' => ['/privacyverklaring', '[Plak hier je privacyverklaring, Romy.]'],
        'algemene voorwaarden' => ['/algemene-voorwaarden', '[Plak hier je algemene voorwaarden, Romy.]'],
    ]);

    test('the cookie policy explains each category under a real heading', function () {
        $prose = documentVoor('/cookiebeleid')->querySelector('.legal .prose');

        expect(tekstenVan($prose?->querySelectorAll('h2')))->toBe([
            'Noodzakelijk',
            'Statistieken',
            'Marketing',
            'Je keuze aanpassen',
        ])
            ->and(platteTekst($prose?->querySelector('h2 + [data-cms-field="tekst"]')))->toContain('in de browser (vd-cookies)');
    });

    test('a section added in the cms gets its heading as a real h2', function () {
        wijzigStrook('/algemene-voorwaarden', 'juridisch', fn (array $waarden): array => [
            'secties' => [['kop' => 'Artikel 1. Definities', 'tekst' => '<p>In deze voorwaarden betekent <strong>opdrachtgever</strong> het bruidspaar.</p>']],
        ]);

        $prose = documentVoor('/algemene-voorwaarden')->querySelector('.legal .prose');

        expect(tekstenVan($prose?->querySelectorAll('h2')))->toBe(['Artikel 1. Definities'])
            ->and($prose?->querySelector('h2')?->nextElementSibling?->getAttribute('data-cms-field'))->toBe('tekst')
            ->and(platteTekst($prose?->querySelector('h2 + [data-cms-field="tekst"]')))->toBe('In deze voorwaarden betekent opdrachtgever het bruidspaar.');
    });

});

test('the privacy statement points questions to the email address from the config at the time of seeding', function () {
    config()->set('vertrouwdag.email', 'privacy@example.test');
    $this->seed(PrivacyverklaringSeeder::class);

    $document = documentVoor('/privacyverklaring');
    $link = $document->querySelector('.legal .prose a[href^="mailto:"]');

    expect($link?->textContent)->toBe('privacy@example.test')
        ->and($link?->getAttribute('href'))->toStartWith('mailto:privacy@example.test')
        ->and(platteTekst($document->querySelector('[data-cms-field="afsluiting"]')))->toBe('Vragen over privacy? Mail naar privacy@example.test.');
});

test('the cookie policy opens the cookie settings and links to the privacy statement and the email address', function () {
    config()->set('vertrouwdag.email', 'cookies@example.test');
    $this->seed(CookiebeleidSeeder::class);

    $prose = documentVoor('/cookiebeleid')->querySelector('.legal .prose');
    $knop = $prose?->querySelector('button[data-cookie-open]');

    expect($knop?->textContent)->toBe('Cookie-instellingen openen')
        ->and($knop?->getAttribute('type'))->toBe('button')
        ->and($prose?->querySelector('a[href^="mailto:"]')?->textContent)->toBe('cookies@example.test')
        ->and($prose?->querySelector('a[href="/privacyverklaring"]')?->textContent)->toBe('privacyverklaring');
});
