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

test('a price typed in the cms is read as whole euros in the notations an editor uses', function (string $tekst, int $euros) {
    expect(Prijs::eurosUitTekst($tekst))->toBe($euros);
})->with([
    'alleen cijfers' => ['1850', 1850],
    'met een punt' => ['1.850', 1850],
    'met euroteken en spatie' => ['€ 1850', 1850],
    'met euroteken zonder spatie' => ['€850', 850],
    'met streepje' => ['1850,-', 1850],
    'met nul centen' => ['1.850,00', 1850],
    'met witruimte eromheen' => ['  3500 ', 3500],
    'met een harde spatie' => ["€\u{00A0}3.500", 3500],
    'boven een miljoen' => ['1.250.000', 1250000],
]);

test('a price typed in the cms that is no whole amount reads as no price', function (?string $tekst) {
    expect(Prijs::eurosUitTekst($tekst))->toBeNull();
})->with([
    'leeg' => [''],
    'niets' => [null],
    'in woorden' => ['acht'],
    'met centen' => ['850,50'],
    'nul' => ['0'],
    'negatief' => ['-850'],
    'een verkeerde punt' => ['18.50'],
    'met tekst erachter' => ['850 euro'],
    'te groot' => ['123456789'],
]);
