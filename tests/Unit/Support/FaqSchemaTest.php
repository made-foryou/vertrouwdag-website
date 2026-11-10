<?php

use App\Support\FaqSchema;

test('the questions become a FAQ page for search engines, in their order', function () {
    $vragen = [
        ['id' => null, 'vraag' => 'Wat als het regent?', 'antwoord' => '<p>Er is altijd een plan B.</p>'],
        ['id' => null, 'vraag' => 'Werk je met vaste leveranciers?', 'antwoord' => '<p>Jullie zitten nergens aan vast.</p>'],
    ];

    expect(FaqSchema::vanVragen($vragen))->toBe([
        '@context' => 'https://schema.org',
        '@type' => 'FAQPage',
        'mainEntity' => [
            [
                '@type' => 'Question',
                'name' => 'Wat als het regent?',
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Er is altijd een plan B.'],
            ],
            [
                '@type' => 'Question',
                'name' => 'Werk je met vaste leveranciers?',
                'acceptedAnswer' => ['@type' => 'Answer', 'text' => 'Jullie zitten nergens aan vast.'],
            ],
        ],
    ]);
});

test('an answer becomes plain text without markup, entities or extra whitespace', function () {
    $vragen = [[
        'id' => null,
        'vraag' => 'Wat kost een weddingplanner?',
        'antwoord' => "<p>Vanaf <span data-prijs-incl=\"dag\">€1.028,50</span>, incl.&nbsp;btw &amp; <strong>vast</strong>.</p>\n<p>Bekijk <a href=\"/diensten\">de pakketten</a>.</p><ul><li>Een</li><li>Twee</li></ul>",
    ]];

    expect(FaqSchema::vanVragen($vragen)['mainEntity'][0]['acceptedAnswer']['text'])
        ->toBe('Vanaf €1.028,50, incl. btw & vast. Bekijk de pakketten. Een Twee');
});
