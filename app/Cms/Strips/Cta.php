<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook cta in het CMS: het afsluitende blok met foto, kop, tekst en
 * de knop naar de kennismaking. Hij rendert met de view
 * resources/views/strips/cta.blade.php. De foto is te kiezen; leeg is
 * Romy aan tafel, de foto uit het ontwerp (#21). De knop komt uit
 * config/vertrouwdag.php.
 *
 * De standaardwaarden zijn die van de homepage. Op de blog en het archief
 * staat de strook zonder tekst.
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
            Velden::kopMetAccent()
                ->required()
                ->default('Zullen we *kennismaken?*'),
            LongText::make('tekst')
                ->default('Een half uur, online en vrijblijvend. Jullie vertellen over jullie plannen, ik vertel hoe ik kan helpen.')
                ->hint('Eén of twee korte zinnen onder de kop. Mag leeg blijven.'),
            Velden::foto('foto', 'Vult de helft naast de tekst.'),
            Choice::make('variant', [
                'groot' => 'Groot, zoals op Diensten',
                'eenvoudig' => 'Eenvoudig, zoals op Werkwijze en Over mij',
                'middel' => 'Middel, zoals op Veelgestelde vragen',
                'compact' => 'Compact, zoals op de blog',
            ])->hint('Leeg: met een hartje boven de kop, zoals op de homepage.'),
        ];
    }
}
