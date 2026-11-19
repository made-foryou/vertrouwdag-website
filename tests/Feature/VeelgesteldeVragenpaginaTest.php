<?php

use App\Cms\Strips\Faq;
use Database\Seeders\PaginasSeeder;
use Database\Seeders\VeelgesteldeVragenpaginaSeeder;
use Dom\Element;
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

/**
 * De vragen op de pagina, zoals een bezoeker ze leest: de vraag, en het
 * antwoord met alles wat erboven staat, als platte tekst.
 *
 * @return list<array{name: ?string, text: string}>
 */
function zichtbareVragen(HTMLDocument $document): array
{
    $vragen = [];

    foreach (strookVanType('/veelgestelde-vragen', $document, 'faq')?->querySelectorAll('details.faq') ?? [] as $vraag) {
        $samenvatting = $vraag->querySelector('summary');
        $antwoord = array_filter(iterator_to_array($vraag->children), fn (Element $kind): bool => $kind !== $samenvatting);

        $vragen[] = [
            'name' => platteTekst($samenvatting),
            'text' => trim(preg_replace('/\s+/', ' ', implode(' ', array_map(fn (Element $kind): string => $kind->textContent, $antwoord)))),
        ];
    }

    return $vragen;
}

/**
 * De tweede vraag, over de kosten.
 */
function kostenvraagOp(HTMLDocument $document): ?Element
{
    return strookVanType('/veelgestelde-vragen', $document, 'faq')?->querySelectorAll('details.faq')->item(1);
}

describe('the seeded FAQ page', function () {
    beforeEach(function () {
        $this->seed(PaginasSeeder::class);
    });

    test('responds with the title from the design and the corrected description', function () {
        $document = documentVoor('/veelgestelde-vragen');

        expect($document->querySelector('title')?->textContent)->toBe('Veelgestelde vragen over een weddingplanner | vertrouwdag')
            ->and($document->querySelector('meta[name="description"]')?->getAttribute('content'))
            ->toBe('Wat kost een weddingplanner, wanneer schakel je er een in en wat als het regent? Antwoorden op de vragen die bruidsparen mij het vaakst stellen.');
    });

    test('comes from the cms with every strip in the order of the design', function () {
        $this->get('/veelgestelde-vragen')->assertOk()->assertViewIs('page');

        expect(striptypenOp('/veelgestelde-vragen', documentVoor('/veelgestelde-vragen')))->toBe([
            'hero-faq',
            'faq',
            'cta',
        ]);
    });

    test('shows eleven questions of which only the first is open', function () {
        $vragen = strookVanType('/veelgestelde-vragen', documentVoor('/veelgestelde-vragen'), 'faq')?->querySelectorAll('details.faq');

        expect($vragen)->toHaveCount(11)
            ->and(trim($vragen->item(0)->querySelector('summary')->textContent))->toBe('Wanneer schakelen we een weddingplanner in?')
            ->and(trim($vragen->item(10)->querySelector('summary')->textContent))->toBe('Hoe groot mag onze bruiloft zijn?')
            ->and(array_map(fn ($vraag): bool => $vraag->hasAttribute('open'), iterator_to_array($vragen)))
            ->toBe([true, false, false, false, false, false, false, false, false, false, false]);
    });

    test('gives the structured data the same questions and answers as the page', function () {
        $document = documentVoor('/veelgestelde-vragen');
        $zichtbaar = zichtbareVragen($document);

        expect(array_map(fn (array $vraag): array => ['name' => $vraag['name'], 'text' => $vraag['acceptedAnswer']['text']], faqSchemaVan($document)['mainEntity']))
            ->toBe($zichtbaar)
            ->and($zichtbaar)->toHaveCount(11);
    });

    test('puts a question added in the cms on the page and in the structured data', function () {
        wijzigStrook('/veelgestelde-vragen', 'faq', fn (array $waarden): array => [
            'vragen' => [...$waarden['vragen'], ['vraag' => 'Mogen we onze hond meenemen?', 'antwoord' => '<p>Natuurlijk, <strong>graag</strong> zelfs.</p>']],
        ]);

        $document = documentVoor('/veelgestelde-vragen');
        $schema = faqSchemaVan($document)['mainEntity'];

        expect(zichtbareVragen($document)[11] ?? null)->toBe(['name' => 'Mogen we onze hond meenemen?', 'text' => 'Natuurlijk, graag zelfs.'])
            ->and($schema)->toHaveCount(12)
            ->and($schema[11]['name'])->toBe('Mogen we onze hond meenemen?')
            ->and($schema[11]['acceptedAnswer']['text'])->toBe('Natuurlijk, graag zelfs.');
    });

    test('shows the prices including VAT from the packages above the answer about the costs, on the page and in the structured data', function () {
        config()->set('vertrouwdag.btw_percentage', 9);

        $document = documentVoor('/veelgestelde-vragen');
        $vraag = kostenvraagOp($document);
        $verwacht = 'De Dag in handen (dagcoördinatie) begint bij €926,50, Hand in hand (gedeeltelijke planning) bij €2.016,50 en Helemaal in handen (full service) bij €3.815, steeds inclusief 9% btw. Na de kennismaking krijgen jullie een voorstel met een vaste prijs. Bekijk wat er in elk pakket zit.';

        expect(zichtbareVragen($document)[1]['text'])->toBe($verwacht)
            ->and(attributenVan($vraag?->querySelectorAll('summary + p [data-prijs-incl]'), 'data-prijs-incl'))->toBe(['dag', 'partial', 'full'])
            ->and($vraag?->querySelector('[data-cms-field="antwoord"] [data-prijs-incl]'))->toBeNull()
            ->and(faqSchemaVan($document)['mainEntity'][1]['acceptedAnswer']['text'])->toBe($verwacht);
    });

    test('names the packages as an editor renamed them in the sentence with the prices', function () {
        wijzigPakketten(['dag' => ['soort' => 'Alleen de dag', 'titel' => 'De Grote Dag']]);

        expect(platteTekst(kostenvraagOp(documentVoor('/veelgestelde-vragen'))?->querySelector('summary + p')))
            ->toStartWith('De Grote Dag (alleen de dag) begint bij €1.028,50, Hand in hand');
    });

    test('leaves out the sentence with the prices when a package has no price', function () {
        wijzigPakketten(['partial' => ['prijs' => 'op aanvraag']]);

        $document = documentVoor('/veelgestelde-vragen');

        expect(kostenvraagOp($document)?->querySelector('[data-prijs-incl]'))->toBeNull()
            ->and(zichtbareVragen($document)[1]['text'])->toBe('Na de kennismaking krijgen jullie een voorstel met een vaste prijs. Bekijk wat er in elk pakket zit.')
            ->and(faqSchemaVan($document)['mainEntity'][1]['acceptedAnswer']['text'])->toBe('Na de kennismaking krijgen jullie een voorstel met een vaste prijs. Bekijk wat er in elk pakket zit.');
    });

    test('puts the questions of a second tab on the page and in the structured data, with the prices when asked', function () {
        config()->set('vertrouwdag.btw_percentage', 9);
        wijzigStrook('/veelgestelde-vragen', 'faq', fn (array $waarden): array => [
            'label_2' => 'Kosten',
            'vragen_2' => [['vraag' => 'Betalen we een aanbetaling?', 'antwoord' => '<p>Ja, een kwart bij de bevestiging.</p>', 'prijzen' => Faq::MET_PRIJZEN]],
        ]);

        $document = documentVoor('/veelgestelde-vragen');
        $strook = strookVanType('/veelgestelde-vragen', $document, 'faq');
        $zichtbaar = zichtbareVragen($document);

        expect(tekstenVan($strook?->querySelectorAll('[role="tab"]')))->toBe(['Alle vragen', 'Kosten'])
            ->and(platteTekst($strook?->querySelector('[data-cms-items="vragen_2"] summary')))->toBe('Betalen we een aanbetaling?')
            ->and($zichtbaar[11] ?? null)->toBe([
                'name' => 'Betalen we een aanbetaling?',
                'text' => 'De Dag in handen (dagcoördinatie) begint bij €926,50, Hand in hand (gedeeltelijke planning) bij €2.016,50 en Helemaal in handen (full service) bij €3.815, steeds inclusief 9% btw. Ja, een kwart bij de bevestiging.',
            ])
            ->and(array_map(fn (array $vraag): array => ['name' => $vraag['name'], 'text' => $vraag['acceptedAnswer']['text']], faqSchemaVan($document)['mainEntity']))
            ->toBe($zichtbaar);
    });

    test('shows the prices only above the answers that ask for them', function () {
        wijzigStrook('/veelgestelde-vragen', 'faq', fn (array $waarden): array => [
            'vragen' => array_map(fn (array $vraag): array => [...$vraag, 'prijzen' => $vraag['vraag'] === 'Kunnen we een pakket aanpassen?' ? Faq::MET_PRIJZEN : null], $waarden['vragen']),
        ]);

        $vragen = strookVanType('/veelgestelde-vragen', documentVoor('/veelgestelde-vragen'), 'faq')?->querySelectorAll('details.faq');
        $metPrijzen = array_keys(array_filter(iterator_to_array($vragen), fn (Element $vraag): bool => $vraag->querySelector('[data-prijs-incl]') !== null));

        expect($metPrijzen)->toBe([9]);
    });

    test('links the answers to the packages and to the meeting', function () {
        $document = documentVoor('/veelgestelde-vragen');

        expect(attributenVan(strookVanType('/veelgestelde-vragen', $document, 'faq')?->querySelectorAll('[data-cms-field="antwoord"] a'), 'href'))
            ->toBe(['/diensten', '/kennismaken']);
    });

    test('offers a WhatsApp message to the number from the config in its hero', function () {
        config()->set('vertrouwdag.whatsapp', ['nummer' => '31600000000', 'tekst' => 'Hoi & dag']);

        $hero = strookVanType('/veelgestelde-vragen', documentVoor('/veelgestelde-vragen'), 'hero-faq');

        expect(platteTekst($hero?->querySelector('h1')))->toBe('Goed om te weten')
            ->and($hero?->querySelector('h1 em')?->textContent)->toBe('om te weten')
            ->and($hero?->querySelector('.lead a')?->getAttribute('href'))->toBe('https://wa.me/31600000000?text=Hoi%20%26%20dag')
            ->and(platteTekst($hero?->querySelector('.lead')))->toBe('Staat jullie vraag er niet tussen? Stel hem gerust in het kennismakingsgesprek, of stuur me een appje.')
            ->and(tekstenVan($hero?->querySelectorAll('.faq-notes[aria-hidden="true"] .note p')))->toBe(['"Wat als het regent?"', '"Wanneer moeten we beginnen?"', '"Wat kost het?"']);
    });

    test('closes with the question to get acquainted', function () {
        $cta = strookVanType('/veelgestelde-vragen', documentVoor('/veelgestelde-vragen'), 'cta');

        expect(platteTekst($cta?->querySelector('h2')))->toBe('Nog een vraag?')
            ->and($cta?->querySelector('.cta-split__photo')?->getAttribute('style'))->toBe('min-height:440px')
            ->and($cta?->querySelector('a.btn')?->getAttribute('href'))->toBe(url('/kennismaken'));
    });
});

test('the answer about the costs names no amounts while the services page is not in the cms', function () {
    $this->seed(VeelgesteldeVragenpaginaSeeder::class);

    $document = documentVoor('/veelgestelde-vragen');

    expect(kostenvraagOp($document)?->querySelector('[data-prijs-incl]'))->toBeNull()
        ->and(zichtbareVragen($document)[1]['text'])->toBe('Na de kennismaking krijgen jullie een voorstel met een vaste prijs. Bekijk wat er in elk pakket zit.');
});
