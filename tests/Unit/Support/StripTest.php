<?php

use App\Support\Strip;

test('a strip gives the value of a field as a property', function () {
    $strip = new Strip('hero', 'hero', ['kop' => 'Jullie dag in *vertrouwde* handen']);

    expect($strip->kop)->toBe('Jullie dag in *vertrouwde* handen')
        ->and($strip->id)->toBe('hero')
        ->and($strip->type)->toBe('hero');
});

test('a strip gives null for a field it does not have', function () {
    $strip = new Strip('hero', 'hero', []);

    expect($strip->label)->toBeNull();
});

test('a field is only set when it has a value', function () {
    $strip = new Strip('hero', 'hero', ['kop' => 'Kop', 'label' => null]);

    expect(isset($strip->kop))->toBeTrue()
        ->and(isset($strip->label))->toBeFalse()
        ->and(isset($strip->tekst))->toBeFalse();
});
