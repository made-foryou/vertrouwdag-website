<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook kwaliteiten in het CMS: "Waarom vertrouwdag", een kop met een
 * inleiding en de kwaliteiten van Romy als briefjes met een icoon. Hij
 * rendert met de view resources/views/strips/kwaliteiten.blade.php.
 *
 * Kleur, draaiing en icoon horen bij de plek van het briefje en beginnen
 * na het derde opnieuw.
 */
final class Kwaliteiten extends StripType
{
    public function key(): string
    {
        return 'kwaliteiten';
    }

    public function label(): string
    {
        return 'Kwaliteiten als briefjes';
    }

    public function description(): string
    {
        return 'Kop met inleiding en de kwaliteiten van Romy als briefjes met een icoon.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Waarom vertrouwdag')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Vertrouwd, *zoals een zus* of goede vriendin'),
            LongText::make('tekst')
                ->default('Iemand die jullie wensen kent, eerlijk meedenkt en op de dag zelf alles opvangt. Zodat jullie, en jullie familie, er gewoon kunnen zijn.'),
            Items::make('kwaliteiten', [
                Text::make('titel')->required(),
                LongText::make('tekst')->required(),
            ])
                ->min(1)
                ->itemLabel('Kwaliteit')
                ->default([
                    ['titel' => 'Punctueel', 'tekst' => 'Afspraak is afspraak, ook met leveranciers.'],
                    ['titel' => 'Oog voor detail', 'tekst' => 'Van naamkaartjes tot de looproute van de ceremonie.'],
                    ['titel' => 'Lokaal', 'tekst' => 'Thuis in Zuid-Holland, van Rotterdam tot de Hoeksche Waard.'],
                ])
                ->hint('Het icoon en de kleur horen bij de plek en beginnen na de derde opnieuw.'),
        ];
    }
}
