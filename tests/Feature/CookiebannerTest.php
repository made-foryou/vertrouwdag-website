<?php

beforeEach(function () {
    $this->withoutVite();
});

test('every page has the hidden cookie banner as a labelled dialog between the skip link and the header', function (string $page, array $parameters = []) {
    $document = documentVoor(route($page, $parameters));

    $banner = $document->getElementById('cookie');
    $dialoog = $banner?->querySelector('.cookie__box[role="dialog"]');

    expect($document->querySelectorAll('#cookie'))->toHaveCount(1)
        ->and($banner->hasAttribute('hidden'))->toBeTrue()
        ->and($banner->previousElementSibling?->getAttribute('class'))->toBe('skip')
        ->and($banner->nextElementSibling?->tagName)->toBe('HEADER')
        ->and($dialoog)->not->toBeNull()
        ->and($document->getElementById($dialoog->getAttribute('aria-labelledby'))?->textContent)->toBe('Even over cookies');
})->with([
    'home' => ['home'],
    'kennismaken' => ['kennismaken'],
    'cookiebeleid' => ['cookiebeleid'],
    'blogartikel' => ['blog.artikel', ['wanneer-weddingplanner-inschakelen']],
]);

test('the banner takes its title, text, policy link and buttons from the config', function () {
    config([
        'vertrouwdag.cookies.titel' => 'Koekjes?',
        'vertrouwdag.cookies.tekst' => 'Wij & jullie. Lees het :cookiebeleid.',
        'vertrouwdag.cookies.cookiebeleid' => 'beleid',
        'vertrouwdag.cookies.knoppen' => [
            'voorkeuren' => 'Zelf kiezen',
            'weigeren' => 'Nee',
            'accepteren' => 'Ja',
            'opslaan' => 'Bewaren',
        ],
    ]);

    $document = documentVoor(route('home'));
    $banner = $document->getElementById('cookie');
    $tekst = $document->getElementById('ck-tekst');

    expect($document->getElementById('ck-title')?->textContent)->toBe('Koekjes?')
        ->and($tekst?->textContent)->toBe('Wij & jullie. Lees het beleid.')
        ->and($tekst?->querySelector('a')?->getAttribute('href'))->toBe(route('cookiebeleid'))
        ->and(tekstenVan($banner?->querySelectorAll('button[data-ck]')))->toBe(['Zelf kiezen', 'Nee', 'Ja', 'Bewaren']);
});

test('refusing takes one click on a button that looks the same as accepting', function () {
    $document = documentVoor(route('home'));

    $weigeren = $document->querySelector('#cookie .cookie__main button[data-ck="none"]');
    $accepteren = $document->querySelector('#cookie .cookie__main button[data-ck="all"]');

    expect($weigeren?->getAttribute('type'))->toBe('button')
        ->and($weigeren?->getAttribute('class'))->toBe($accepteren?->getAttribute('class'))
        ->and($weigeren?->closest('[hidden]:not(#cookie)'))->toBeNull()
        ->and($accepteren?->closest('[hidden]:not(#cookie)'))->toBeNull();
});

test('the preferences are collapsed behind a toggle that controls them', function () {
    $document = documentVoor(route('home'));

    $knop = $document->querySelector('#cookie button[data-ck="prefs"]');
    $voorkeuren = $document->getElementById($knop?->getAttribute('aria-controls') ?? '');

    expect($knop?->getAttribute('aria-expanded'))->toBe('false')
        ->and($voorkeuren?->getAttribute('id'))->toBe('cookie-prefs')
        ->and($voorkeuren?->hasAttribute('hidden'))->toBeTrue()
        ->and($voorkeuren?->querySelector('button[data-ck="save"]'))->not->toBeNull();
});

test('the preferences list each category from the config, with an unchecked box only for optional ones', function () {
    config([
        'vertrouwdag.cookies.altijd_aan' => 'Staat aan',
        'vertrouwdag.cookies.categorieen' => [
            'noodzakelijk' => ['naam' => 'Nodig', 'uitleg' => 'Voor de site.', 'verplicht' => true],
            'statistieken' => ['naam' => 'Tellen', 'uitleg' => 'Voor de cijfers.', 'verplicht' => false],
            'video' => ['naam' => 'Video', 'uitleg' => 'Voor filmpjes.', 'verplicht' => false],
        ],
    ]);

    $document = documentVoor(route('home'));
    $voorkeuren = $document->getElementById('cookie-prefs');
    $vinkjes = $voorkeuren?->querySelectorAll('input[type="checkbox"]');

    expect(tekstenVan($voorkeuren?->querySelectorAll('.pref b')))->toBe(['Nodig', 'Tellen', 'Video'])
        ->and(tekstenVan($voorkeuren?->querySelectorAll('.pref b + span')))->toBe(['Voor de site.', 'Voor de cijfers.', 'Voor filmpjes.'])
        ->and(attributenVan($vinkjes, 'value'))->toBe(['statistieken', 'video'])
        ->and(attributenVan($vinkjes, 'checked'))->each->toBeNull()
        ->and(platteTekst($voorkeuren?->querySelector('div.pref')))->toBe('NodigVoor de site.Staat aan');

    foreach ($vinkjes as $vinkje) {
        expect($vinkje->closest('label.pref'))->not->toBeNull();
    }
});

test('the banner passes the version and the optional categories to the script', function () {
    config([
        'vertrouwdag.cookies.versie' => 3,
        'vertrouwdag.cookies.categorieen' => [
            'noodzakelijk' => ['naam' => 'Nodig', 'uitleg' => 'Voor de site.', 'verplicht' => true],
            'marketing' => ['naam' => 'Marketing', 'uitleg' => 'Voor de agenda.', 'verplicht' => false],
            'video' => ['naam' => 'Video', 'uitleg' => 'Voor filmpjes.', 'verplicht' => false],
        ],
    ]);

    $document = documentVoor(route('home'));
    $banner = $document->getElementById('cookie');

    expect($banner?->getAttribute('data-versie'))->toBe('3')
        ->and(json_decode($banner?->getAttribute('data-categorieen'), true))->toBe(['marketing', 'video']);
});
