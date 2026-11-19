<?php

/*
|--------------------------------------------------------------------------
| Validatiemeldingen
|--------------------------------------------------------------------------
|
| Alleen de regels die de site gebruikt. Een regel die hier ontbreekt, valt
| terug op de Engelse melding van Laravel.
|
*/

return [

    'accepted' => 'Vink :attribute aan.',
    'date_format' => 'Vul een geldige :attribute in.',
    'email' => 'Vul een geldig :attribute in.',
    'max' => [
        'string' => ':Attribute mag niet langer zijn dan :max tekens.',
    ],
    'required' => 'Vul je :attribute in.',
    'string' => ':Attribute moet tekst zijn.',

    'custom' => [
        'avg' => [
            'accepted' => 'Vink aan dat jullie de checklist en af en toe een mail willen ontvangen.',
        ],
    ],

    'attributes' => [
        'voornaam' => 'voornaam',
        'email' => 'e-mailadres',
        'trouwdatum' => 'trouwdatum',
        'avg' => 'toestemming',
    ],

];
