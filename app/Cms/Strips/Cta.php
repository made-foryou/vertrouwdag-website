<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
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
 *
 * Since the strips were merged (2026-11-28) the variant Kaart is the old kennismaking: the invitation on an aubergine
 * card that closes an article, with a label above the heading and without the photo. Those strips were moved to this
 * key with that variant by a migration.
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
        return 'Afsluitend blok met kop en de knop naar de kennismaking: met een foto, of als kaart onder een blog.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->hint('Alleen bij Kaart: een korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Zullen we *kennismaken?*'),
            LongText::make('tekst')
                ->default('Een half uur, online en vrijblijvend. Jullie vertellen over jullie plannen, ik vertel hoe ik kan helpen.')
                ->hint('Eén of twee korte zinnen onder de kop. Mag leeg blijven.'),
            Velden::foto('foto', 'Vult de helft naast de tekst. Niet bij Kaart.'),
            Choice::make('variant', [
                'groot' => 'Groot, zoals op Diensten',
                'eenvoudig' => 'Eenvoudig, zoals op Werkwijze en Over mij',
                'middel' => 'Middel, zoals op Veelgestelde vragen',
                'compact' => 'Compact, zoals op de blog',
                'kaart' => 'Kaart zonder foto, zoals onder een blogartikel',
            ])->hint('Leeg: met een hartje boven de kop, zoals op de homepage.'),
        ];
    }
}
