<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Bedrijf
    |--------------------------------------------------------------------------
    |
    | Hoe zoekmachines het bedrijf beschrijven, in de gestructureerde
    | gegevens (JSON-LD) van de homepage. Het werkgebied is de regio waar
    | Romy bruiloften begeleidt. De eigenaar is de schrijver van de blogs.
    |
    */

    'bedrijf' => [
        'naam' => 'vertrouwdag',
        'eigenaar' => 'Romy van Dillen',
        'omschrijving' => 'Weddingplanner in Zuid-Holland',
        'werkgebied' => 'Zuid-Holland',
    ],

    /*
    |--------------------------------------------------------------------------
    | Canoniek adres
    |--------------------------------------------------------------------------
    |
    | Het adres van de live site, zonder slash aan het eind. De canonical
    | en og:url van elke pagina wijzen hierheen, ook op staging en lokaal,
    | zodat zoekmachines alleen de live site als de echte zien. Alleen voor
    | een lokale meting met Lighthouse zet je hier tijdelijk het lokale adres.
    |
    */

    'canonieke_url' => env('VERTROUWDAG_CANONIEKE_URL', 'https://vertrouwdag.nl'),

    /*
    |--------------------------------------------------------------------------
    | Btw
    |--------------------------------------------------------------------------
    |
    | Romy zet de vanaf-prijzen exclusief btw in het CMS, in de strook
    | pakketten op /diensten (#17). De site rekent zelf het bedrag
    | inclusief btw uit met dit percentage.
    |
    */

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
    | Cookiebanner
    |--------------------------------------------------------------------------
    |
    | De teksten en categorieën van de banner. In de tekst wordt
    | :cookiebeleid een link naar het cookiebeleid, met het label uit
    | cookiebeleid. Een verplichte categorie staat altijd aan en heeft
    | geen vinkje. Voor elke andere categorie vraagt de banner toestemming.
    | Een script geeft zelf aan bij welke categorie het hoort, met
    | data-toestemming (zie resources/js/modules/toestemming.js).
    |
    | Verhoog de versie als de categorieën of hun doelen wijzigen. De
    | banner vraagt dan opnieuw, ook aan wie al een keuze maakte.
    |
    */

    'cookies' => [
        'versie' => 1,
        'titel' => 'Even over cookies',
        'tekst' => 'Ik gebruik cookies om te zien hoe de site gebruikt wordt en om de afsprakenplanner te tonen. Jullie kiezen zelf. Meer in het :cookiebeleid.',
        'cookiebeleid' => 'cookiebeleid',
        'knoppen' => [
            'voorkeuren' => 'Voorkeuren',
            'weigeren' => 'Alleen noodzakelijk',
            'accepteren' => 'Alles accepteren',
            'opslaan' => 'Keuze opslaan',
        ],
        'altijd_aan' => 'Altijd aan',
        'categorieen' => [
            'noodzakelijk' => [
                'naam' => 'Noodzakelijk',
                'uitleg' => 'Nodig om de site te laten werken en jullie keuze te onthouden.',
                'verplicht' => true,
            ],
            'statistieken' => [
                'naam' => 'Statistieken',
                'uitleg' => 'Google Analytics, anoniem, om de site te verbeteren.',
                'verplicht' => false,
            ],
            'marketing' => [
                'naam' => 'Marketing',
                'uitleg' => 'Calendly-agenda en later mogelijk Google Ads.',
                'verplicht' => false,
            ],
        ],
    ],

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
    | Per link het pad met het label. Een pagina uit het CMS heeft geen
    | routenaam, daarom zijn het paden. Een menulink is actief op zijn
    | eigen pad en op de paden eronder: Blog ook op /blog/archief en de
    | artikelen. Het hoofdmenu komt later uit de paginaboom van het CMS
    | (#19). De knop naar de kennismaking en de footerlinks blijven hier.
    |
    */

    'menu' => [
        '/diensten' => 'Diensten',
        '/werkwijze' => 'Werkwijze',
        '/over-mij' => 'Over mij',
        '/blog' => 'Blog',
        '/veelgestelde-vragen' => 'Veelgestelde vragen',
    ],

    'cta' => [
        'pad' => '/kennismaken',
        'label' => 'Plan een kennismaking',
    ],

    'footer' => [
        'paginas' => [
            '/diensten' => 'Diensten',
            '/werkwijze' => 'Werkwijze',
            '/over-mij' => 'Over mij',
            '/blog' => 'Blog',
            '/veelgestelde-vragen' => 'Veelgestelde vragen',
            '/checklist' => 'Gratis checklist',
            '/kennismaken' => 'Plan een kennismaking',
        ],
        'juridisch' => [
            '/privacyverklaring' => 'Privacyverklaring',
            '/cookiebeleid' => 'Cookiebeleid',
            '/algemene-voorwaarden' => 'Algemene voorwaarden',
        ],
    ],

];
