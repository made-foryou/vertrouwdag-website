<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook leveranciers in het CMS: een oproep aan leveranciers om samen
 * te werken, met een knop die een mail opent en een link naar WhatsApp.
 * Hij rendert met de view resources/views/strips/leveranciers.blade.php.
 *
 * Het e-mailadres, het onderwerp van de mail en het WhatsApp-nummer
 * liggen vast; het adres en het nummer komen uit config/vertrouwdag.php.
 * Alleen de teksten van de knop en de link zijn velden.
 */
final class Leveranciers extends StripType
{
    public function key(): string
    {
        return 'leveranciers';
    }

    public function label(): string
    {
        return 'Voor leveranciers';
    }

    public function description(): string
    {
        return 'Oproep aan leveranciers, met een knop naar de mail en een link naar WhatsApp.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Voor leveranciers')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Samen maken we *mooie dagen*'),
            LongText::make('tekst')
                ->default('Ben jij fotograaf, bloemist, locatie of een andere trouwprofessional in Zuid-Holland? Ik bouw aan een netwerk van leveranciers die ik met een gerust hart aanbeveel aan mijn bruidsparen. Ik maak graag kennis.'),
            Text::make('knop_tekst')
                ->label('Tekst van de knop')
                ->required()
                ->default('Neem contact op')
                ->hint('De knop opent een mail aan Romy.'),
            Text::make('app_tekst')
                ->label('Tekst van de WhatsApp-link')
                ->required()
                ->default('of stuur een appje'),
        ];
    }
}
