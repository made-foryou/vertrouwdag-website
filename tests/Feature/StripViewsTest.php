<?php

use App\Support\Strip;
use Dom\HTMLDocument;
use Illuminate\View\ViewException;

/**
 * Rendert een strip-view met een strook, zoals <x-strips> dat doet.
 *
 * @param  array<string, mixed>  $values
 */
function renderStrip(string $type, array $values): HTMLDocument
{
    return renderComponent("@include('strips.{$type}', ['strip' => \$strip])", ['strip' => new Strip('s1', $type, $values)]);
}

beforeEach(function () {
    $this->withoutVite();
});

test('the last word of the hero label does not wrap', function (string $label, string $laatsteWoord) {
    $document = renderStrip('hero', ['label' => $label, 'kop' => 'Kop']);

    expect(platteTekst($document->querySelector('[data-cms-field="label"]')))->toBe($label)
        ->and($document->querySelector('[data-cms-field="label"] span[style="white-space:nowrap"]')?->textContent)->toBe($laatsteWoord);
})->with([
    'meerdere woorden' => ['Weddingplanner in Zuid-Holland', 'Zuid-Holland'],
    'één woord' => ['Weddingplanner', 'Weddingplanner'],
]);

test('the services strip rejects a service without a price and names it', function () {
    $diensten = [['id' => null, 'dienst' => 'goud', 'soort' => 'Soort', 'titel' => 'Titel', 'omschrijving' => 'Omschrijving']];

    expect(fn () => renderStrip('diensten', ['kop' => 'Mijn *diensten*', 'diensten' => $diensten]))
        ->toThrow(ViewException::class, 'De strook diensten kent geen dienst [goud]');
});

test('a review without a label or name shows only its quote', function () {
    $reviews = [['id' => null, 'label' => null, 'citaat' => '"Fijn samengewerkt."', 'naam' => '']];

    $document = renderStrip('reviews', ['kop' => 'Wat anderen *zeggen*', 'reviews' => $reviews]);
    $review = $document->querySelector('[data-cms-items="reviews"] > figure');

    expect($review?->querySelector('blockquote')?->textContent)->toBe('"Fijn samengewerkt."')
        ->and($review?->querySelector('.review__label, figcaption'))->toBeNull();
});

test('the notes start again with the first colour after the fourth quote', function () {
    $citaten = array_map(fn (int $nummer): array => ['id' => null, 'tekst' => "Citaat {$nummer}"], range(1, 5));

    $document = renderStrip('herkenning', ['kop' => 'Klinkt dit bekend?', 'citaten' => $citaten]);
    $briefjes = $document->querySelectorAll('[data-cms-items="citaten"] > .note');

    expect($briefjes)->toHaveCount(5)
        ->and($briefjes->item(4)->getAttribute('style'))->toBe($briefjes->item(0)->getAttribute('style'))
        ->and($document->querySelectorAll('.deck-dots span'))->toHaveCount(5);
});

test('the steps are numbered in their order', function () {
    $stappen = [
        ['id' => null, 'titel' => 'Kennismaken', 'tekst' => 'Een half uur online.'],
        ['id' => null, 'titel' => 'Voorstel', 'tekst' => 'Binnen een week.'],
    ];

    $document = renderStrip('stappen', ['kop' => 'Vier stappen *naar ja*', 'stappen' => $stappen]);

    expect(tekstenVan($document->querySelectorAll('[data-cms-items="stappen"] > li .step__nr')))->toBe(['1', '2'])
        ->and($document->querySelector('.werk-head .btn'))->toBeNull();
});

test('the packages strip rejects a service without a price and names it', function () {
    $diensten = [['id' => null, 'dienst' => 'goud', 'soort' => 'Soort', 'titel' => 'Titel', 'omschrijving' => 'Omschrijving', 'kenmerken' => '', 'bijschrift' => null]];

    expect(fn () => renderStrip('pakketten', ['kop' => 'Welke map?', 'diensten' => $diensten]))
        ->toThrow(ViewException::class, 'De strook pakketten kent geen dienst [goud]');
});

test('the features of a package are its lines without the empty ones', function () {
    $diensten = [['id' => null, 'dienst' => 'dag', 'soort' => 'Dagcoördinatie', 'titel' => 'De Dag in handen', 'omschrijving' => 'Voor stellen.', 'kenmerken' => "Draaiboek\n\n  Afstemming met leveranciers  \r\n", 'bijschrift' => 'op de dag zelf']];

    $document = renderStrip('pakketten', ['kop' => 'Welke map?', 'diensten' => $diensten]);

    expect(tekstenVan($document->querySelectorAll('.tf__panel ul.hl li')))->toBe(['Draaiboek', 'Afstemming met leveranciers'])
        ->and(tekstenVan($document->querySelectorAll('.tf-m .folder ul.hl li')))->toBe(['Draaiboek', 'Afstemming met leveranciers']);
});

test('the comparison rejects an answer other than yes, no or partly and names the row', function () {
    $values = [
        'kop' => 'Wat zit *waarin?*',
        'kolommen' => [['id' => null, 'dienst' => 'dag', 'titel' => 'De Dag in handen']],
        'onderdelen' => [['id' => null, 'onderdeel' => 'Budgetoverzicht', 'dag' => 'misschien']],
    ];

    expect(fn () => renderStrip('vergelijking', $values))
        ->toThrow(ViewException::class, 'De strook vergelijking kent geen antwoord [misschien] bij [Budgetoverzicht]');
});

test('the large closing strip has a bigger heading and a taller photo but no heart', function () {
    $document = renderStrip('cta', ['kop' => 'Twijfelen jullie *welk pakket?*', 'tekst' => 'Dat zoeken we samen uit.', 'variant' => 'groot']);

    expect($document->querySelector('.cta-split__photo')?->getAttribute('style'))->toBe('min-height:480px')
        ->and($document->querySelector('.cta-split__txt h2')?->getAttribute('style'))->toBe('font-size:clamp(40px,5vw,68px)')
        ->and($document->querySelector('.cta-split__txt > svg'))->toBeNull();
});

test('the closing strip without a variant has a heart, as on the homepage', function () {
    $document = renderStrip('cta', ['kop' => 'Zullen we *kennismaken?*']);

    expect($document->querySelector('.cta-split__txt > svg'))->not->toBeNull()
        ->and($document->querySelector('.cta-split__photo')?->hasAttribute('style'))->toBeFalse();
});

test('the closing strip rejects an unknown variant', function () {
    expect(fn () => renderStrip('cta', ['kop' => 'Kop', 'variant' => 'klein']))
        ->toThrow(ViewException::class, 'De strook cta kent geen variant [klein]');
});

test('the plain closing strip has a taller photo but no heart and the normal heading', function () {
    $document = renderStrip('cta', ['kop' => 'Het begint met *een gesprek*', 'variant' => 'eenvoudig']);

    expect($document->querySelector('.cta-split__photo')?->getAttribute('style'))->toBe('min-height:480px')
        ->and($document->querySelector('.cta-split__txt > svg'))->toBeNull()
        ->and($document->querySelector('.cta-split__txt h2')?->hasAttribute('style'))->toBeFalse()
        ->and($document->querySelector('.cta-split__txt')?->hasAttribute('style'))->toBeFalse();
});

test('the timeline numbers its phases from its first number and alternates their sides', function () {
    $fases = array_map(fn (int $nummer): array => ['id' => null, 'periode' => "Periode {$nummer}", 'titel' => "Fase {$nummer}", 'tekst' => 'Tekst'], range(1, 3));

    $document = renderStrip('tijdlijn', ['eerste_nummer' => 6, 'fases' => $fases]);
    $lijst = $document->querySelector('ol.tl[data-cms-items="fases"]');

    expect($lijst?->getAttribute('start'))->toBe('6')
        ->and(tekstenVan($lijst?->querySelectorAll('.tl__badge span')))->toBe(['6', '7', '8'])
        ->and(attributenVan($document->querySelectorAll('ol.tl > li'), 'class'))->toBe(['tl__step', 'tl__step tl__step--r', 'tl__step']);
});

test('the timeline starts at one and lies on the band only with that variant', function (?string $variant, ?string $klasse) {
    $fases = [['id' => null, 'periode' => 'Periode', 'titel' => 'Fase', 'tekst' => 'Tekst']];

    $document = renderStrip('tijdlijn', ['variant' => $variant, 'fases' => $fases]);

    expect($document->querySelector('section[data-cms-strip]')?->getAttribute('class'))->toBe($klasse)
        ->and($document->querySelector('.tl__badge span')?->textContent)->toBe('1');
})->with([
    'zonder variant' => [null, null],
    'op de band' => ['band', 'band'],
]);

test('the timeline rejects an unknown variant', function () {
    expect(fn () => renderStrip('tijdlijn', ['variant' => 'aubergine', 'fases' => []]))
        ->toThrow(ViewException::class, 'De strook tijdlijn kent geen variant [aubergine]');
});

test('the wedding day band shows its phase number only when it has one', function (?int $nummer, array $badges) {
    $document = renderStrip('trouwdag', ['nummer' => $nummer, 'kop' => 'Kop', 'briefjes' => []]);

    expect(tekstenVan($document->querySelectorAll('.tl__badge span')))->toBe($badges);
})->with([
    'met nummer' => [5, ['5']],
    'zonder nummer' => [null, []],
]);

test('the notes of the wedding day start again with the first colour after the fourth', function () {
    $briefjes = array_map(fn (int $nummer): array => ['id' => null, 'wie' => "Wie {$nummer}", 'tekst' => "Tekst {$nummer}"], range(1, 5));

    $document = renderStrip('trouwdag', ['kop' => 'Kop', 'briefjes' => $briefjes]);
    $briefjesOpDePagina = $document->querySelectorAll('[data-cms-items="briefjes"] > .note');

    expect($briefjesOpDePagina)->toHaveCount(5)
        ->and($briefjesOpDePagina->item(4)->getAttribute('style'))->toBe($briefjesOpDePagina->item(0)->getAttribute('style'))
        ->and($document->querySelectorAll('.deck-dots span'))->toHaveCount(5);
});

test('the qualities start again with the first colour after the third', function () {
    $kwaliteiten = array_map(fn (int $nummer): array => ['id' => null, 'titel' => "Kwaliteit {$nummer}", 'tekst' => 'Tekst'], range(1, 4));

    $document = renderStrip('kwaliteiten', ['kop' => 'Kop', 'kwaliteiten' => $kwaliteiten]);
    $briefjes = $document->querySelectorAll('[data-cms-items="kwaliteiten"] > .note');

    expect($briefjes)->toHaveCount(4)
        ->and($briefjes->item(3)->getAttribute('style'))->toBe($briefjes->item(0)->getAttribute('style'))
        ->and($briefjes->item(3)->querySelector('svg')?->outerHTML)->toBe($briefjes->item(0)->querySelector('svg')?->outerHTML)
        ->and($document->querySelectorAll('.deck-dots.deck-dots--licht span'))->toHaveCount(4);
});

test('a quote without a heading shows only the quote', function () {
    $document = renderStrip('citaat', ['citaat' => 'Het antwoord was er al heel lang.']);
    $review = $document->querySelector('section.reviews figure.review.review--groot');

    expect($review?->querySelector('blockquote[data-cms-field="citaat"]')?->textContent)->toBe('Het antwoord was er al heel lang.')
        ->and($review?->querySelector('p.eb'))->toBeNull();
});
