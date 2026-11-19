<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use App\Support\Icoon;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook herkenning in het CMS: "Klinkt dit bekend?" met citaten van
 * bruidsparen als briefjes. Hij rendert met de view
 * resources/views/strips/herkenning.blade.php.
 *
 * Kleur en draaiing horen bij de plek van een briefje en beginnen na het
 * vierde opnieuw; die liggen vast in de view. Het icoon kiest de
 * redacteur per citaat uit App\Support\Icoon; zonder keuze is het dat
 * van de plek. De knop is de kennismaking uit config/vertrouwdag.php.
 */
final class Herkenning extends StripType
{
    public function key(): string
    {
        return 'herkenning';
    }

    public function label(): string
    {
        return 'Herkenning';
    }

    public function description(): string
    {
        return 'Citaten van bruidsparen als briefjes, met een slotzin en de knop naar de kennismaking.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Herkennen jullie dit?')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Klinkt dit *een beetje* als jullie?'),
            Items::make('citaten', [
                LongText::make('tekst')->required(),
                Choice::make('icoon', Icoon::class)
                    ->hint('Zonder keuze krijgt het briefje het icoon van zijn plek: klok, hart, lijstje of telefoon.'),
            ])
                ->min(1)
                ->itemLabel('Citaat')
                ->default([
                    ['tekst' => '"We hebben na het werk gewoon geen tijd om alles uit te zoeken."'],
                    ['tekst' => '"Mijn moeder wil helpen, maar ik wil juist dat zij geniet."'],
                    ['tekst' => '"Drie offertes, vijf tabbladen en nog steeds geen overzicht."'],
                    ['tekst' => '"Wie belt de bloemist als er op de dag iets misgaat?"'],
                ])
                ->hint('De kleur hoort bij de plek en begint na het vierde citaat opnieuw.'),
            Text::make('slotzin')
                ->default('Jullie hoeven op jullie dag niets meer te regelen, en jullie familie ook niet.'),
        ];
    }
}
