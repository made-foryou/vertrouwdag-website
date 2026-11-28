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
 * laatste opnieuw; die liggen vast in de view. Het icoon kiest de
 * redacteur per briefje uit App\Support\Icoon; zonder keuze is het dat
 * van de plek. De knop is de kennismaking uit config/vertrouwdag.php.
 *
 * Since the strips were merged (2026-11-28) this is the one strip with notes, called "Briefjes" in the cms. The
 * variant picks the look of kwaliteiten (light, a title and an icon per note, an intro) or trouwdag (dark, who does
 * what, the number of the phase) instead of herkenning. The key and the list key (citaten) stay, so the stored
 * strips did not have to change; kwaliteiten and trouwdag were on no page. A field a variant does not show says so
 * in its hint, because the panel shows every field.
 */
final class Herkenning extends StripType
{
    public function key(): string
    {
        return 'herkenning';
    }

    public function label(): string
    {
        return 'Briefjes';
    }

    public function description(): string
    {
        return 'Een kop met briefjes: herkenbare citaten, kwaliteiten of wie wat doet op de trouwdag.';
    }

    public function fields(): array
    {
        return [
            Choice::make('variant', [
                'herkenning' => 'Herkenning: donker, met iconen, een slotzin en de knop naar de kennismaking',
                'kwaliteiten' => 'Kwaliteiten: licht, met een inleiding en een titel en icoon per briefje',
                'trouwdag' => 'Trouwdag: donker, met een nummer, wie het doet en een slotzin',
            ])
                ->label('Uiterlijk')
                ->required()
                ->default('herkenning'),
            Text::make('nummer')
                ->label('Nummer van de fase')
                ->hint('Alleen bij Trouwdag. Een getal, zoals 5. Leeg: geen nummer.'),
            Text::make('label')
                ->default('Herkennen jullie dit?')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Klinkt dit *een beetje* als jullie?'),
            LongText::make('tekst')
                ->label('Inleiding')
                ->hint('Alleen bij Kwaliteiten: de tekst onder de kop.'),
            Items::make('citaten', [
                Text::make('titel')
                    ->hint('Bij Kwaliteiten de kop van het briefje, bij Trouwdag wie het doet. Niet bij Herkenning.'),
                LongText::make('tekst')->required(),
                Choice::make('icoon', Icoon::class)
                    ->hint('Niet bij Trouwdag. Zonder keuze krijgt het briefje het icoon van zijn plek.'),
            ])
                ->label('Briefjes')
                ->min(1)
                ->itemLabel('Briefje')
                ->default([
                    ['tekst' => '"We hebben na het werk gewoon geen tijd om alles uit te zoeken."'],
                    ['tekst' => '"Mijn moeder wil helpen, maar ik wil juist dat zij geniet."'],
                    ['tekst' => '"Drie offertes, vijf tabbladen en nog steeds geen overzicht."'],
                    ['tekst' => '"Wie belt de bloemist als er op de dag iets misgaat?"'],
                ])
                ->hint('De kleur hoort bij de plek en begint na het laatste briefje van de reeks opnieuw.'),
            Text::make('slotzin')
                ->default('Jullie hoeven op jullie dag niets meer te regelen, en jullie familie ook niet.')
                ->hint('Niet bij Kwaliteiten.'),
        ];
    }
}
