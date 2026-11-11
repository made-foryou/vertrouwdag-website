<?php

namespace App\Cms\Strips;

use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook cta in het CMS: het afsluitende blok met foto, kop, tekst en
 * de knop naar de kennismaking. Hij rendert met de view
 * resources/views/strips/cta.blade.php. De foto en de knop komen nog uit
 * de site zelf (#21 en config/vertrouwdag.php).
 *
 * Dit is de eerste strook in het CMS, voor de testpagina van #14. De
 * velden van alle striptypes legt #15 vast, ook het accent in de kop.
 */
final class Cta extends StripType
{
    public function key(): string
    {
        return 'cta';
    }

    public function label(): string
    {
        return 'Afsluiter met kennismaking';
    }

    public function description(): string
    {
        return 'Afsluitend blok met foto, kop en de knop naar de kennismaking.';
    }

    public function fields(): array
    {
        return [
            Text::make('kop')
                ->required()
                ->default('Zullen we *kennismaken?*')
                ->hint('Zet één woord tussen sterretjes voor het accent, zoals *kennismaken?*'),
            LongText::make('tekst')
                ->hint('Eén of twee korte zinnen onder de kop.'),
            Choice::make('variant', [
                'groot' => 'Groot, zoals op Diensten',
                'eenvoudig' => 'Eenvoudig, zoals op Werkwijze en Over mij',
                'middel' => 'Middel, zoals op Veelgestelde vragen',
                'compact' => 'Compact, zoals op de blog',
            ])->hint('Leeg: met een hartje boven de kop, zoals op de homepage.'),
        ];
    }
}
