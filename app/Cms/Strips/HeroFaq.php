<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook hero-faq in het CMS: de opening van /veelgestelde-vragen met
 * kop en tekst, en rechts briefjes met vragen die bruidsparen vaak
 * stellen. Hij rendert met de view resources/views/strips/hero-faq.blade.php.
 *
 * De tekst eindigt met een link naar WhatsApp; het nummer komt uit
 * config/vertrouwdag.php. Kleur en draaiing van een briefje horen bij zijn
 * plek en beginnen na het derde opnieuw.
 */
final class HeroFaq extends StripType
{
    public function key(): string
    {
        return 'hero-faq';
    }

    public function label(): string
    {
        return 'Opening van Veelgestelde vragen';
    }

    public function description(): string
    {
        return 'Opening met kop, tekst, een link naar WhatsApp en briefjes met vragen.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Veelgestelde vragen')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Goed *om te weten*'),
            LongText::make('tekst')
                ->default('Staat jullie vraag er niet tussen? Stel hem gerust in het kennismakingsgesprek, of')
                ->hint('De link naar WhatsApp volgt direct na deze tekst.'),
            Text::make('app_tekst')
                ->label('Tekst van de WhatsApp-link')
                ->required()
                ->default('stuur me een appje'),
            Items::make('briefjes', [
                Text::make('tekst')->required(),
            ])
                ->itemLabel('Briefje')
                ->default([
                    ['tekst' => '"Wat als het regent?"'],
                    ['tekst' => '"Wanneer moeten we beginnen?"'],
                    ['tekst' => '"Wat kost het?"'],
                ])
                ->hint('Alleen op grotere schermen. De kleur hoort bij de plek en begint na het derde opnieuw.'),
        ];
    }
}
