<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook hero-kennismaken in het CMS: de uitnodiging met de stappen van
 * het gesprek, en rechts de Calendly-agenda met de polaroid van Romy. Hij
 * rendert met de view resources/views/strips/hero-kennismaken.blade.php.
 *
 * De agenda ligt vast: de poort met "Agenda tonen", het vangnet en hun
 * teksten (#10). De Calendly-link staat in config/vertrouwdag.php. Het
 * nummer van een stap volgt de volgorde, de kleur hoort bij de plek. De
 * foto op de polaroid is te kiezen; leeg is de foto uit het ontwerp (#21).
 */
final class HeroKennismaken extends StripType
{
    public function key(): string
    {
        return 'hero-kennismaken';
    }

    public function label(): string
    {
        return 'Opening van Kennismaken';
    }

    public function description(): string
    {
        return 'Uitnodiging met de stappen van het gesprek en de Calendly-agenda.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Kennismaken')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->tag('h1')
                ->required()
                ->default('Zullen we *kennismaken?*'),
            LongText::make('tekst')
                ->default('Kies een moment dat jullie past. We spreken elkaar online, ongeveer 30 minuten. Vrijblijvend, en gewoon vanaf de bank.'),
            Items::make('stappen', [
                Text::make('tekst')->required(),
            ])
                ->itemLabel('Stap')
                ->default([
                    ['tekst' => 'Jullie vertellen over jullie plannen en wat jullie belangrijk vinden.'],
                    ['tekst' => 'Ik stel vragen en vertel hoe ik werk.'],
                    ['tekst' => 'Binnen een week een voorstel. Daarna beslissen jullie, zonder haast.'],
                ])
                ->hint('Genummerd in deze volgorde. De kleur hoort bij de plek en begint na de derde opnieuw.'),
            Text::make('opmerking')
                ->label('Regel onder de stappen')
                ->default('Liever fysiek afspreken? Dat kan ook. Laat het weten in het bericht.')
                ->hint('Mag leeg blijven.'),
            Velden::foto('foto', 'Vierkant: de polaroid snijdt hem bij.')
                ->label('Foto op de polaroid'),
            Text::make('bijschrift')
                ->label('Bijschrift bij de polaroid')
                ->default('tot zo, Romy'),
        ];
    }
}
