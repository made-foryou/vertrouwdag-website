<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Prijzen
    |--------------------------------------------------------------------------
    |
    | De vanaf-prijzen per dienst, in hele euro's exclusief btw. De site
    | rekent zelf het bedrag inclusief btw uit met het btw-percentage.
    |
    */

    'prijzen' => [
        'dag' => 850,
        'partial' => 1850,
        'full' => 3500,
    ],

    'btw_percentage' => 21,

    /*
    |--------------------------------------------------------------------------
    | Contact
    |--------------------------------------------------------------------------
    |
    | Het WhatsApp-nummer staat in internationale notatie, zonder plus en
    | zonder de 0: 06 51 59 88 60 wordt 31651598860. De tekst is het
    | bericht dat alvast klaarstaat.
    |
    */

    'email' => 'romy@vertrouwdag.nl',

    'whatsapp' => [
        'nummer' => '31651598860',
        'tekst' => 'Hoi Romy! Ik ben benieuwd naar de mogelijkheden voor onze bruiloft.',
    ],

    'instagram' => 'https://www.instagram.com/vertrouwdag/',

    'calendly' => 'https://calendly.com/romy-vertrouwdag/30min',

    /*
    |--------------------------------------------------------------------------
    | Google Analytics 4
    |--------------------------------------------------------------------------
    |
    | Het meet-ID (G-XXXXXXX) verschilt per omgeving. Leeg betekent dat er
    | geen Analytics laadt, ook niet na toestemming.
    |
    */

    'ga_id' => env('VERTROUWDAG_GA_ID'),

    /*
    |--------------------------------------------------------------------------
    | KvK-nummer
    |--------------------------------------------------------------------------
    |
    | Zolang dit leeg is, toont de footer geen KvK-regel.
    |
    */

    'kvk' => null,

];
