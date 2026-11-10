<?php

beforeEach(function () {
    $this->withoutVite();
});

test('a legal page is one legal strip with the title and heading from the design', function (string $url, string $titel, string $kop, string $accent) {
    $document = documentVoor($url);
    $strook = $document->querySelector('main > section.wrap.legal');

    expect($document->querySelector('title')?->textContent)->toBe($titel)
        ->and(attributenVan($document->querySelectorAll('main > *'), 'data-cms-strip'))->toBe(['juridisch'])
        ->and($strook?->querySelector('p.eb')?->textContent)->toBe('Goed om te weten')
        ->and(platteTekst($strook?->querySelector('h1')))->toBe($kop)
        ->and($strook?->querySelector('h1 em')?->textContent)->toBe($accent)
        ->and(platteTekst($strook?->querySelector('.legal__meta')))->toBe('Laatst bijgewerkt: [datum]');
})->with([
    'privacyverklaring' => ['/privacyverklaring', 'Privacyverklaring | vertrouwdag', 'Privacyverklaring', 'verklaring'],
    'cookiebeleid' => ['/cookiebeleid', 'Cookiebeleid | vertrouwdag', 'Cookiebeleid', 'beleid'],
    'algemene voorwaarden' => ['/algemene-voorwaarden', 'Algemene voorwaarden | vertrouwdag', 'Algemene voorwaarden', 'voorwaarden'],
]);

test('the privacy statement and the terms keep the placeholder for Romy until the final text arrives', function (string $url, string $placeholder) {
    $document = documentVoor($url);

    expect(tekstenVan($document->querySelectorAll('.legal .prose .todo')))->toBe([$placeholder]);
})->with([
    'privacyverklaring' => ['/privacyverklaring', '[Plak hier je privacyverklaring, Romy.]'],
    'algemene voorwaarden' => ['/algemene-voorwaarden', '[Plak hier je algemene voorwaarden, Romy.]'],
]);

test('the privacy statement points questions to the email address from the config', function () {
    config()->set('vertrouwdag.email', 'privacy@example.test');

    $document = documentVoor('/privacyverklaring');
    $link = $document->querySelector('.legal .prose a[href^="mailto:"]');

    expect($link?->textContent)->toBe('privacy@example.test')
        ->and($link?->getAttribute('href'))->toStartWith('mailto:privacy@example.test')
        ->and(platteTekst($document->querySelector('[data-cms-field="afsluiting"]')))->toBe('Vragen over privacy? Mail naar privacy@example.test.');
});

test('the cookie policy explains each category under a real heading', function () {
    $document = documentVoor('/cookiebeleid');

    expect(tekstenVan($document->querySelectorAll('.legal .prose h2')))->toBe([
        'Noodzakelijk',
        'Statistieken',
        'Marketing',
        'Je keuze aanpassen',
    ])
        ->and($document->querySelector('.legal .prose code')?->textContent)->toBe('vd-cookies');
});

test('the cookie policy opens the cookie settings and links to the privacy statement and the email address', function () {
    config()->set('vertrouwdag.email', 'cookies@example.test');

    $document = documentVoor('/cookiebeleid');
    $prose = $document->querySelector('.legal .prose');
    $knop = $prose?->querySelector('button[data-cookie-open]');

    expect($knop?->textContent)->toBe('Cookie-instellingen openen')
        ->and($knop?->getAttribute('type'))->toBe('button')
        ->and($prose?->querySelector('a[href^="mailto:"]')?->textContent)->toBe('cookies@example.test')
        ->and($prose?->querySelector('a[href="'.route('privacyverklaring').'"]')?->textContent)->toBe('privacyverklaring');
});
