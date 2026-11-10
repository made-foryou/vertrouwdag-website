<?php

use Dom\HTMLDocument;

beforeEach(function () {
    $this->withoutVite();
});

/**
 * De FAQPage uit de head van de pagina.
 *
 * @return array<string, mixed>
 */
function faqSchemaVan(HTMLDocument $document): array
{
    foreach ($document->querySelectorAll('head script[type="application/ld+json"]') as $script) {
        $data = json_decode($script->textContent, true, flags: JSON_THROW_ON_ERROR);

        if (($data['@type'] ?? null) === 'FAQPage') {
            return $data;
        }
    }

    throw new RuntimeException('De pagina heeft geen FAQPage in de head.');
}

test('the FAQ page responds with the title from the design and the corrected description', function () {
    $document = documentVoor('/veelgestelde-vragen');

    expect($document->querySelector('title')?->textContent)->toBe('Veelgestelde vragen over een weddingplanner | vertrouwdag')
        ->and($document->querySelector('meta[name="description"]')?->getAttribute('content'))
        ->toBe('Wat kost een weddingplanner, wanneer schakel je er een in en wat als het regent? Antwoorden op de vragen die bruidsparen mij het vaakst stellen.');
});

test('every section of the FAQ page is a strip, in the order of the design', function () {
    $document = documentVoor('/veelgestelde-vragen');

    expect(attributenVan($document->querySelectorAll('main > *'), 'data-cms-strip'))->toBe([
        'hero-faq',
        'faq',
        'cta',
    ]);
});

test('the FAQ page shows eleven questions of which only the first is open', function () {
    $document = documentVoor('/veelgestelde-vragen');
    $vragen = $document->querySelectorAll('[data-cms-strip="faq"] details.faq');

    expect($vragen)->toHaveCount(11)
        ->and(trim($vragen->item(0)->querySelector('summary')->textContent))->toBe('Wanneer schakelen we een weddingplanner in?')
        ->and(trim($vragen->item(10)->querySelector('summary')->textContent))->toBe('Hoe groot mag onze bruiloft zijn?')
        ->and(array_map(fn ($vraag): bool => $vraag->hasAttribute('open'), iterator_to_array($vragen)))
        ->toBe([true, false, false, false, false, false, false, false, false, false, false]);
});

test('the structured data has the same questions and answers as the page', function () {
    $document = documentVoor('/veelgestelde-vragen');
    $zichtbaar = [];

    foreach ($document->querySelectorAll('[data-cms-strip="faq"] details.faq') as $vraag) {
        $zichtbaar[] = [
            'name' => platteTekst($vraag->querySelector('summary')),
            'text' => platteTekst($vraag->querySelector('[data-cms-field="antwoord"]')),
        ];
    }

    $schema = faqSchemaVan($document);

    expect(array_map(fn (array $vraag): array => ['name' => $vraag['name'], 'text' => $vraag['acceptedAnswer']['text']], $schema['mainEntity']))
        ->toBe($zichtbaar)
        ->and($zichtbaar)->toHaveCount(11);
});

test('the answer about the costs shows the prices including VAT from the config, on the page and in the structured data', function () {
    config()->set('vertrouwdag.prijzen', ['dag' => 1000, 'partial' => 2000, 'full' => 3000]);
    config()->set('vertrouwdag.btw_percentage', 9);

    $document = documentVoor('/veelgestelde-vragen');
    $antwoord = $document->querySelectorAll('[data-cms-strip="faq"] details.faq')->item(1)->querySelector('[data-cms-field="antwoord"]');
    $verwacht = 'De Dag in handen (dagcoördinatie) begint bij €1.090, Hand in hand (gedeeltelijke planning) bij €2.180 en Helemaal in handen (full service) bij €3.270, steeds inclusief 9% btw. Na de kennismaking krijgen jullie een voorstel met een vaste prijs. Bekijk wat er in elk pakket zit.';

    expect(platteTekst($antwoord))->toBe($verwacht)
        ->and(attributenVan($antwoord->querySelectorAll('[data-prijs-incl]'), 'data-prijs-incl'))->toBe(['dag', 'partial', 'full'])
        ->and(faqSchemaVan($document)['mainEntity'][1]['acceptedAnswer']['text'])->toBe($verwacht);
});

test('the answers link to the packages and to the meeting', function () {
    $document = documentVoor('/veelgestelde-vragen');

    expect(attributenVan($document->querySelectorAll('[data-cms-strip="faq"] [data-cms-field="antwoord"] a'), 'href'))
        ->toBe([route('diensten'), route('kennismaken')]);
});

test('the FAQ hero offers a WhatsApp message to the number from the config', function () {
    config()->set('vertrouwdag.whatsapp', ['nummer' => '31600000000', 'tekst' => 'Hoi & dag']);

    $document = documentVoor('/veelgestelde-vragen');
    $hero = $document->querySelector('[data-cms-strip="hero-faq"]');

    expect(platteTekst($hero?->querySelector('h1')))->toBe('Goed om te weten')
        ->and($hero?->querySelector('h1 em')?->textContent)->toBe('om te weten')
        ->and($hero?->querySelector('.lead a')?->getAttribute('href'))->toBe('https://wa.me/31600000000?text=Hoi%20%26%20dag')
        ->and(platteTekst($hero?->querySelector('.lead')))->toBe('Staat jullie vraag er niet tussen? Stel hem gerust in het kennismakingsgesprek, of stuur me een appje.')
        ->and(tekstenVan($hero?->querySelectorAll('.faq-notes[aria-hidden="true"] .note p')))->toBe(['"Wat als het regent?"', '"Wanneer moeten we beginnen?"', '"Wat kost het?"']);
});

test('the FAQ page closes with the question to get acquainted', function () {
    $document = documentVoor('/veelgestelde-vragen');
    $cta = $document->querySelector('[data-cms-strip="cta"]');

    expect(platteTekst($cta?->querySelector('h2')))->toBe('Nog een vraag?')
        ->and($cta?->querySelector('.cta-split__photo')?->getAttribute('style'))->toBe('min-height:440px')
        ->and($cta?->querySelector('a.btn')?->getAttribute('href'))->toBe(route('kennismaken'));
});
