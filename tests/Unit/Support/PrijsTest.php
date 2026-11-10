<?php

use App\Support\Prijs;

test('the price including vat is written in the notation of the design', function (int $exclBtw, string $inclBtw) {
    $prijs = new Prijs($exclBtw, 21);

    expect($prijs->inclBtw())->toBe($inclBtw);
})->with([
    'De Dag in handen' => [850, '€1.028,50'],
    'Hand in hand' => [1850, '€2.238,50'],
    'Helemaal in handen' => [3500, '€4.235'],
]);

test('the price excluding vat has no cents and a dot for thousands', function (int $exclBtw, string $notatie) {
    $prijs = new Prijs($exclBtw, 21);

    expect($prijs->exclBtw())->toBe($notatie);
})->with([
    [850, '€850'],
    [1850, '€1.850'],
    [3500, '€3.500'],
]);

test('the price including vat follows the vat percentage', function () {
    $prijs = new Prijs(850, 9);

    expect($prijs->inclBtw())->toBe('€926,50');
});
