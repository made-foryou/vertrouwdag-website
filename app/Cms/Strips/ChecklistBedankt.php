<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook checklist-bedankt in het CMS: het bedankblok dat verschijnt
 * als het checklistformulier verstuurd is, met de knop naar de
 * kennismaking uit config/vertrouwdag.php. Hij rendert met de view
 * resources/views/strips/checklist-bedankt.blade.php.
 *
 * Een bezoeker ziet het blok pas na het versturen. In de bewerkmodus staat
 * het altijd open, zodat een redacteur het kan wijzigen.
 */
final class ChecklistBedankt extends StripType
{
    public function key(): string
    {
        return 'checklist-bedankt';
    }

    public function label(): string
    {
        return 'Bedankt voor de checklist';
    }

    public function description(): string
    {
        return 'Het bedankblok na het aanvragen van de checklist, met de knop naar de kennismaking.';
    }

    public function fields(): array
    {
        return [
            Velden::kopMetAccent()
                ->required()
                ->default('Dank *jullie wel*'),
            LongText::make('tekst')
                ->required()
                ->default('De checklist is onderweg naar jullie mailbox. Niets ontvangen? Kijk dan even bij reclame of updates.'),
            Text::make('vraag')
                ->label('Regel boven de knop')
                ->default('Willen jullie het liever samen doornemen?')
                ->hint('Mag leeg blijven.'),
        ];
    }
}
