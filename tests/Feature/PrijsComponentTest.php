<?php

use Illuminate\View\ViewException;

test('the price component shows the price including vat from the config', function () {
    config()->set('vertrouwdag.prijzen.dag', 1000);

    $document = renderComponent('<x-prijs dienst="dag" />');

    expect($document->querySelector('span[data-prijs-incl="dag"]')?->textContent)->toBe('€1.210');
});

test('the price component shows the price excluding vat when asked', function () {
    config()->set('vertrouwdag.prijzen.partial', 1850);

    $document = renderComponent('<x-prijs dienst="partial" excl />');

    expect($document->querySelector('span[data-prijs="partial"]')?->textContent)->toBe('€1.850');
});

test('the price component fails loudly for a service without a price', function () {
    $this->blade('<x-prijs dienst="bestaat-niet" />');
})->throws(ViewException::class, 'Er staat geen prijs voor de dienst [bestaat-niet]');

test('the price line shows the price including and excluding vat', function () {
    config()->set('vertrouwdag.prijzen.full', 3500);

    $document = renderComponent('<x-prijsregel dienst="full" class="tf__price" />');
    $regel = $document->querySelector('p.price.tf__price');

    expect(preg_replace('/\s+/', ' ', $regel?->textContent))->toBe('vanaf €4.235 incl. btw€3.500 excl. btw')
        ->and($document->querySelectorAll('.muted'))->toHaveCount(0);
});

test('the price line dims both small lines when asked', function () {
    $document = renderComponent('<x-prijsregel dienst="full" gedempt />');

    expect($document->querySelectorAll('small.muted, span.incl.muted'))->toHaveCount(2);
});
