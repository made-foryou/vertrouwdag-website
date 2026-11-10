<?php

/*
|--------------------------------------------------------------------------
| Blogs
|--------------------------------------------------------------------------
|
| Alle blogs, de nieuwste bovenaan. /blog toont de eerste gepubliceerde
| blog groot en de drie daarna als kaart, ook wat binnenkort komt. Het
| archief toont alle gepubliceerde blogs. Gepubliceerd is een blog met
| een slug die niet binnenkort komt; hij staat dan op /blog/{slug}, met
| de view resources/views/pages/artikelen/{slug}.blade.php.
|
| titel: met het accent tussen sterretjes, zoals elke kop op de site.
| categorie: planning, locaties, budget, inspiratie of achter-de-schermen.
| leestijd: in minuten, of null. afbeelding: het pad voor Vite::asset().
|
| Deze lijst is tijdelijk. In #20 komen de blogs uit het CMS.
|
*/

return [

    [
        'titel' => 'Landelijk-romantisch trouwen in Zuid-Holland: waar begin je?',
        'intro' => 'Van boerderij tot buitenplaats, dicht bij huis.',
        'categorie' => 'locaties',
        'leestijd' => null,
        'afbeelding' => 'resources/images/ceremonie-buiten.jpg',
        'alt' => 'Ceremonieplek buiten met witte stoelen',
        'slug' => null,
        'binnenkort' => true,
    ],

    [
        'titel' => 'Waarom jullie familie op jullie trouwdag niets hoeft te regelen',
        'intro' => 'Zo mogen jullie ouders en getuigen vooral gast zijn.',
        'categorie' => 'achter-de-schermen',
        'leestijd' => null,
        'afbeelding' => 'resources/images/feesttafel.jpg',
        'alt' => 'Gedekte feesttafel met bloemen',
        'slug' => null,
        'binnenkort' => true,
    ],

    [
        'titel' => '[Volgend artikel]',
        'intro' => 'Hier komt jullie volgende blog.',
        'categorie' => 'budget',
        'leestijd' => null,
        'afbeelding' => 'resources/images/ringen-goud.jpg',
        'alt' => 'Twee gouden trouwringen',
        'slug' => null,
        'binnenkort' => true,
    ],

    [
        'titel' => 'Wanneer schakel je een weddingplanner in? *(En waarom eerder dan je denkt)*',
        'intro' => 'De meeste stellen bellen pas als het al druk is. Waarom een vroeg begin juist rust oplevert.',
        'categorie' => 'planning',
        'leestijd' => 6,
        'afbeelding' => 'resources/images/paar-meer-herfst.jpg',
        'alt' => 'Bruidspaar bij een meer in de herfst',
        'slug' => 'wanneer-weddingplanner-inschakelen',
        'binnenkort' => false,
    ],

];
