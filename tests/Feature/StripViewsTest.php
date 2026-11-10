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
