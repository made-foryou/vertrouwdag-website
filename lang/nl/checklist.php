<?php

return [

    'signup' => [
        'thanks' => 'Dankjewel! De checklist is onderweg naar jullie mailbox.',
        'throttled' => 'Dat waren veel aanvragen achter elkaar. Probeer het over een paar minuten opnieuw.',
        'expired' => 'Deze pagina stond te lang open. Vernieuw de pagina en probeer het opnieuw.',
        'failed' => 'Het versturen lukte niet. Probeer het later opnieuw, of mail naar :email.',
    ],

    'mail' => [
        'subject' => 'Jullie trouwplanning-checklist',
        'greeting' => 'Hoi :name,',
        'intro' => 'Wat leuk dat jullie de checklist willen gebruiken! Met de knop hieronder openen jullie hem: maand voor maand, van twaalf maanden ervoor tot de week zelf.',
        'button' => 'Open de checklist',
        'fallback' => 'Werkt de knop niet? Kopieer dan deze link in jullie browser:',
        'why' => [
            'Jullie krijgen deze mail omdat dit e-mailadres de checklist aanvroeg op vertrouwdag.nl.',
            'Met de knop bevestigen jullie ook dat ik af en toe een mail mag sturen.',
            'Afmelden kan altijd, met de link onderaan elke mail.',
            'Niet zelf aangevraagd? Dan hoeven jullie niets te doen: zonder klik komt dit adres niet op de lijst.',
        ],
        'closing' => 'Liefs,',
        'signature' => 'Romy',
    ],

    'prune' => [
        'pruned' => '{0} Geen onbevestigde aanmeldingen om op te ruimen.|{1} :count onbevestigde aanmelding opgeruimd.|[2,*] :count onbevestigde aanmeldingen opgeruimd.',
    ],

];
