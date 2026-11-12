<?php

use App\Support\Prijs;

test('the price component shows the price including vat', function () {
    $document = renderComponent('<x-prijs :prijs="$prijs" dienst="dag" />', ['prijs' => new Prijs(1000, 21)]);

    expect($document->querySelector('span[data-prijs-incl="dag"]')?->textContent)->toBe('€1.210');
});

test('the price component shows the price excluding vat when asked', function () {
    $document = renderComponent('<x-prijs :prijs="$prijs" dienst="partial" excl />', ['prijs' => new Prijs(1850, 21)]);

    expect($document->querySelector('span[data-prijs="partial"]')?->textContent)->toBe('€1.850');
});

test('the price line shows the price including and excluding vat', function () {
    $document = renderComponent('<x-prijsregel :prijs="$prijs" dienst="full" class="tf__price" />', ['prijs' => new Prijs(3500, 21)]);
    $regel = $document->querySelector('p.price.tf__price');

    expect(preg_replace('/\s+/', ' ', $regel?->textContent))->toBe('vanaf €4.235 incl. btw€3.500 excl. btw')
        ->and($document->querySelectorAll('.muted'))->toHaveCount(0);
});

test('the price line dims both small lines when asked', function () {
    $document = renderComponent('<x-prijsregel :prijs="$prijs" dienst="full" gedempt />', ['prijs' => new Prijs(3500, 21)]);

    expect($document->querySelectorAll('small.muted, span.incl.muted'))->toHaveCount(2);
});

test('the price line shows nothing without a price, so an unreadable price does not break the page', function () {
    $document = renderComponent('<x-prijsregel :prijs="null" dienst="full" />');

    expect($document->querySelector('.price'))->toBeNull();
});
