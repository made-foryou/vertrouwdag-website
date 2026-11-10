<?php

beforeEach(function () {
    $this->withoutVite();
});

test('the acquaintance page responds with the title and description from the design', function () {
    $document = documentVoor('/kennismaken');

    expect($document->querySelector('title')?->textContent)->toBe('Plan een kennismaking | vertrouwdag weddingplanner')
        ->and($document->querySelector('meta[name="description"]')?->getAttribute('content'))
        ->toBe('Plan een vrijblijvend kennismakingsgesprek van 30 minuten, online of op locatie. Vertel over jullie plannen, ik vertel hoe ik kan helpen.');
});

test('every section of the acquaintance page is a strip, in the order of the design', function () {
    $document = documentVoor('/kennismaken');

    expect(attributenVan($document->querySelectorAll('main > *'), 'data-cms-strip'))->toBe([
        'hero-kennismaken',
        'contact',
    ]);
});

test('the acquaintance page shows the three steps of the conversation in order', function () {
    $document = documentVoor('/kennismaken');
    $hero = $document->querySelector('[data-cms-strip="hero-kennismaken"]');

    expect(platteTekst($hero?->querySelector('h1')))->toBe('Zullen we kennismaken?')
        ->and(tekstenVan($hero?->querySelectorAll('ol.mini-steps[data-cms-items="stappen"] > li > span')))->toBe(['1', '2', '3'])
        ->and(tekstenVan($hero?->querySelectorAll('ol.mini-steps > li > p')))->toBe([
            'Jullie vertellen over jullie plannen en wat jullie belangrijk vinden.',
            'Ik stel vragen en vertel hoe ik werk.',
            'Binnen een week een voorstel. Daarna beslissen jullie, zonder haast.',
        ]);
});

test('the acquaintance page loads nothing from Calendly before consent', function () {
    $response = $this->get('/kennismaken');

    $response->assertOk()
        ->assertDontSee('assets.calendly.com', false)
        ->assertDontSee('<iframe', false);
});

test('the calendar shows a gate with a button and a link to Calendly from the config', function () {
    config()->set('vertrouwdag.calendly', 'https://calendly.com/test/kennismaken');

    $document = documentVoor('/kennismaken');
    $agenda = $document->getElementById('agenda');

    expect($agenda?->querySelector('#cal-gate')?->hasAttribute('hidden'))->toBeFalse()
        ->and($agenda?->querySelector('#cal-gate button#cal-show[type="button"]')?->textContent)->toBe('Agenda tonen')
        ->and($agenda?->querySelector('#cal-gate a.btn')?->getAttribute('href'))->toBe('https://calendly.com/test/kennismaken')
        ->and($agenda?->querySelector('#cal-embed')?->getAttribute('data-calendly-url'))->toBe('https://calendly.com/test/kennismaken')
        ->and($agenda?->querySelector('#cal-embed')?->hasAttribute('hidden'))->toBeTrue()
        ->and($agenda?->querySelector('#cal-embed')?->childElementCount)->toBe(0);
});

test('the fallback of the calendar is hidden and links to Calendly, WhatsApp and mail from the config', function () {
    config([
        'vertrouwdag.calendly' => 'https://calendly.com/test/kennismaken',
        'vertrouwdag.email' => 'test@voorbeeld.nl',
        'vertrouwdag.whatsapp.nummer' => '31600000000',
    ]);

    $document = documentVoor('/kennismaken');
    $vangnet = $document->getElementById('cal-fallback');

    expect($vangnet?->hasAttribute('hidden'))->toBeTrue()
        ->and(attributenVan($vangnet?->querySelectorAll('a'), 'href'))->sequence(
            fn ($href) => $href->toBe('https://calendly.com/test/kennismaken'),
            fn ($href) => $href->toStartWith('https://wa.me/31600000000?text='),
            fn ($href) => $href->toStartWith('mailto:test@voorbeeld.nl'),
        );
});

test('the notes in the contact band link to WhatsApp, mail and Instagram from the config', function () {
    config([
        'vertrouwdag.email' => 'test@voorbeeld.nl',
        'vertrouwdag.email_onderwerp' => 'Vraag',
        'vertrouwdag.whatsapp' => ['nummer' => '31600000000', 'tekst' => 'Hoi & dag'],
        'vertrouwdag.instagram' => 'https://www.instagram.com/test/',
    ]);

    $document = documentVoor('/kennismaken');
    $briefjes = $document->querySelectorAll('[data-cms-strip="contact"] .contact-notes > a.note');

    expect(attributenVan($briefjes, 'href'))->toBe([
        'https://wa.me/31600000000?text=Hoi%20%26%20dag',
        'mailto:test@voorbeeld.nl?subject=Vraag',
        'https://www.instagram.com/test/',
    ])
        ->and(attributenVan($briefjes, 'target'))->toBe(['_blank', null, '_blank'])
        ->and(tekstenVan($document->querySelectorAll('[data-cms-strip="contact"] .contact-notes__big')))->toBe([
            '"Hoi Romy, wij gaan trouwen..."',
            'test@voorbeeld.nl',
            'Stuur een DM naar @vertrouwdag',
        ]);
});
