<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook trouwdag in het CMS: de trouwdag zelf als fase op de
 * aubergine band, met briefjes over wie wat doet. Hij rendert met de view
 * resources/views/strips/trouwdag.blade.php.
 *
 * Het nummer van de fase is tekst, want het package kent nog geen getal;
 * leeg of iets anders dan een positief heel getal geeft geen boogje. Het
 * nummer herlaadt het frame na het opslaan. Kleur en draaiing van de
 * briefjes horen bij hun plek.
 */
final class Trouwdag extends StripType
{
    public function key(): string
    {
        return 'trouwdag';
    }

    public function label(): string
    {
        return 'Trouwdag met briefjes';
    }

    public function description(): string
    {
        return 'De trouwdag als fase op de donkere band, met briefjes over wie wat doet.';
    }

    public function fields(): array
    {
        return [
            Text::make('nummer')
                ->label('Nummer van de fase')
                ->default('5')
                ->hint('Een getal, zoals 5. Leeg: geen nummer.'),
            Text::make('label')
                ->default('Jullie trouwdag')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Wat jullie familie *die dag doet*'),
            Items::make('briefjes', [
                Text::make('wie')->required(),
                LongText::make('tekst')->required(),
            ])
                ->min(1)
                ->itemLabel('Briefje')
                ->default([
                    ['wie' => 'Jullie ouders', 'tekst' => 'Genieten. Verder niets.'],
                    ['wie' => 'De getuigen', 'tekst' => 'Een speech en een proost.'],
                    ['wie' => 'Jullie', 'tekst' => 'Ja zeggen, en de rest van de dag dansen.'],
                    ['wie' => 'Ik', 'tekst' => 'Alles wat jullie niet hoeven te merken.'],
                ])
                ->hint('De kleuren beginnen na het vierde briefje opnieuw.'),
            Text::make('slotzin')
                ->default('Ik ben er van de opbouw tot de laatste dans. Leveranciers, vragen en onverwachte dingen komen bij mij terecht.'),
        ];
    }
}
