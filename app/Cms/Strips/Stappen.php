<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Button;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook stappen in het CMS: de werkwijze in genummerde stappen, met
 * een knop naar de pagina erover. Hij rendert met de view
 * resources/views/strips/stappen.blade.php.
 *
 * Het nummer volgt de volgorde van de stappen. De kleur hoort bij de plek
 * van een stap en begint na de vierde opnieuw; die ligt vast in de view.
 */
final class Stappen extends StripType
{
    public function key(): string
    {
        return 'stappen';
    }

    public function label(): string
    {
        return 'Stappen';
    }

    public function description(): string
    {
        return 'De werkwijze in genummerde stappen, met een knop.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Werkwijze')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Vier stappen *naar ja*'),
            Button::make('knop')
                ->default(['text' => 'Zo werk ik', 'link' => ['url' => '/werkwijze']]),
            Items::make('stappen', [
                Text::make('titel')->required(),
                LongText::make('tekst')->required(),
            ])
                ->min(1)
                ->itemLabel('Stap')
                ->default([
                    ['titel' => 'Kennismaken', 'tekst' => 'Een half uur online. Jullie vertellen, ik luister.'],
                    ['titel' => 'Voorstel', 'tekst' => 'Binnen een week, helder en met een vaste prijs.'],
                    ['titel' => 'Samen plannen', 'tekst' => 'Eén aanspreekpunt voor alle leveranciers.'],
                    ['titel' => 'Genieten', 'tekst' => 'Op jullie dag hoeft niemand iets te regelen.'],
                ])
                ->hint('Het nummer volgt de volgorde.'),
        ];
    }
}
