<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook hero-checklist in het CMS: de opening van /checklist met links
 * een polaroid en een papiertje, en rechts de kop, de tekst en het
 * aanmeldformulier met trouwdatum. Hij rendert met de view
 * resources/views/strips/hero-checklist.blade.php.
 *
 * De visual en het formulier liggen vast, net als in checklist-aanmelding
 * (#16). Na het versturen toont het formulier de strook checklist-bedankt.
 */
final class HeroChecklist extends StripType
{
    public function key(): string
    {
        return 'hero-checklist';
    }

    public function label(): string
    {
        return 'Opening van Checklist';
    }

    public function description(): string
    {
        return 'Opening met een visual, kop, tekst en het aanmeldformulier voor de checklist.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Gratis checklist')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Wat regel je *wanneer?*'),
            LongText::make('tekst')
                ->default('De checklist die ik zelf gebruik. Maand voor maand, in de juiste volgorde, met ruimte voor jullie eigen notities.'),
        ];
    }
}
