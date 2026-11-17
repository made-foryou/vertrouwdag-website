<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook checklist-aanmelding in het CMS: de gratis checklist, met
 * links een polaroid en een papiertje en rechts het aanmeldformulier.
 * Hij rendert met de view resources/views/strips/checklist-aanmelding.blade.php.
 *
 * De visual (het bijschrift van de polaroid en het papiertje) is
 * decoratie en ligt vast (besluit Menno, 2026-11-11, zie #16). De foto op
 * de polaroid is te kiezen; leeg is de foto uit het ontwerp (#21). Het
 * formulier is hetzelfde als op /checklist; het versturen komt in #25.
 */
final class ChecklistAanmelding extends StripType
{
    public function key(): string
    {
        return 'checklist-aanmelding';
    }

    public function label(): string
    {
        return 'Checklist aanvragen';
    }

    public function description(): string
    {
        return 'De gratis checklist met een visual en het aanmeldformulier.';
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
                ->default('De checklist die ik zelf gebruik, van twaalf maanden ervoor tot de week zelf.'),
            Velden::foto('foto', 'Liggend, 4 bij 3: de polaroid snijdt hem bij. Decoratie, dus zonder alt-tekst op de site.')
                ->label('Foto op de polaroid'),
        ];
    }
}
