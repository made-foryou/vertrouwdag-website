<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bedrijf
    |--------------------------------------------------------------------------
    |
    | Hoe zoekmachines het bedrijf beschrijven, in de gestructureerde
    | gegevens (JSON-LD) van de homepage. Het werkgebied is de regio waar
    | Romy bruiloften begeleidt.
    |
    */

    'bedrijf' => [
        'naam' => 'vertrouwdag',
        'omschrijving' => 'Weddingplanner in Zuid-Holland',
        'werkgebied' => 'Zuid-Holland',
    ],

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
    | Het onderwerp staat alvast ingevuld als iemand op het e-mailadres
    | klikt. Het WhatsApp-nummer staat in internationale notatie, zonder
    | plus en zonder de 0: 06 51 59 88 60 wordt 31651598860. De tekst is
    | het bericht dat alvast klaarstaat.
    |
    */

    'email' => 'romy@vertrouwdag.nl',

    'email_onderwerp' => 'Vraag via vertrouwdag.nl',

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

    /*
    |--------------------------------------------------------------------------
    | Navigatie
    |--------------------------------------------------------------------------
    |
    | Per link de routenaam met het label. Een menulink is actief op zijn
    | eigen route en op de routes eronder: Blog ook op blog.archief en
    | blog.artikel. Het hoofdmenu komt later uit de paginaboom van het
    | CMS (#19). De knop en de footerlinks blijven hier.
    |
    */

    'menu' => [
        'diensten' => 'Diensten',
        'werkwijze' => 'Werkwijze',
        'over-mij' => 'Over mij',
        'blog' => 'Blog',
        'veelgestelde-vragen' => 'Veelgestelde vragen',
    ],

    'cta' => [
        'route' => 'kennismaken',
        'label' => 'Plan een kennismaking',
    ],

    'footer' => [
        'paginas' => [
            'diensten' => 'Diensten',
            'werkwijze' => 'Werkwijze',
            'over-mij' => 'Over mij',
            'blog' => 'Blog',
            'veelgestelde-vragen' => 'Veelgestelde vragen',
            'checklist' => 'Gratis checklist',
            'kennismaken' => 'Plan een kennismaking',
        ],
        'juridisch' => [
            'privacyverklaring' => 'Privacyverklaring',
            'cookiebeleid' => 'Cookiebeleid',
            'algemene-voorwaarden' => 'Algemene voorwaarden',
        ],
    ],

];
