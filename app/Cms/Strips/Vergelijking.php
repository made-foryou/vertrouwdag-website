<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use App\Support\Aanbod;
use App\Support\Dienst;
use App\Support\Pakket;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook vergelijking in het CMS: "Wat zit waarin?", de pakketten naast
 * elkaar in een tabel. Hij rendert met de view
 * resources/views/strips/vergelijking.blade.php.
 *
 * Een redacteur beheert de rijen: per onderdeel of het in een dienst zit.
 * De kolommen, hun namen en de prijsrij volgen de strook pakketten op
 * /diensten (besluit Menno, 2026-11-12, zie #17), zodat een naam en een
 * prijs op één plek staan. with() geeft ze als pakketten. De legenda op
 * mobiel ligt vast. Elke wijziging in de tabel herlaadt het frame.
 */
final class Vergelijking extends StripType
{
    /**
     * De antwoorden per dienst, zoals de view ze kent.
     *
     * @var array<string, string>
     */
    private const array ANTWOORDEN = [
        'ja' => 'Ja, zit erin',
        'deels' => 'Deels',
        'nee' => 'Nee, zit er niet in',
    ];

    public function key(): string
    {
        return 'vergelijking';
    }

    public function label(): string
    {
        return 'Vergelijking van de pakketten';
    }

    public function description(): string
    {
        return 'Tabel met per onderdeel wat in welk pakket zit, met de prijzen eronder. Namen en prijzen komen uit de pakketten.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Naast elkaar')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Wat zit *waarin?*'),
            LongText::make('sticker')
                ->default("maatwerk\nmogelijk")
                ->hint('Elke regel staat op de sticker op een eigen regel. Leeg: geen sticker.'),
            Items::make('onderdelen', [
                Text::make('onderdeel')->required(),
                Choice::make(Dienst::Dag->value, self::ANTWOORDEN)
                    ->label('Dagcoördinatie')
                    ->required()
                    ->default('ja'),
                Choice::make(Dienst::Partial->value, self::ANTWOORDEN)
                    ->label('Gedeeltelijke planning')
                    ->required()
                    ->default('ja'),
                Choice::make(Dienst::Full->value, self::ANTWOORDEN)
                    ->label('Full service')
                    ->required()
                    ->default('ja'),
            ])
                ->min(1)
                ->itemLabel('Onderdeel')
                ->default([
                    ['onderdeel' => 'Coördinatie op de dag zelf', 'dag' => 'ja', 'partial' => 'ja', 'full' => 'ja'],
                    ['onderdeel' => 'Draaiboek en tijdsplanning', 'dag' => 'ja', 'partial' => 'ja', 'full' => 'ja'],
                    ['onderdeel' => 'Leveranciers zoeken en boeken', 'dag' => 'nee', 'partial' => 'deels', 'full' => 'ja'],
                    ['onderdeel' => 'Budgetoverzicht', 'dag' => 'nee', 'partial' => 'ja', 'full' => 'ja'],
                    ['onderdeel' => 'Locatie zoeken', 'dag' => 'nee', 'partial' => 'nee', 'full' => 'ja'],
                    ['onderdeel' => 'Concept en styling', 'dag' => 'nee', 'partial' => 'nee', 'full' => 'ja'],
                ])
                ->hint('De kolommen en hun namen volgen de pakketten.'),
            Text::make('prijs_label')
                ->label('Kop van de prijsrij')
                ->required()
                ->default('Vanaf, incl. btw')
                ->hint('De prijzen zelf staan bij de pakketten.'),
            Text::make('slotzin')
                ->default('Past geen pakket precies? In het kennismakingsgesprek kijken we samen wat jullie nodig hebben.'),
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array{pakketten: list<Pakket>}
     */
    public function with(array $values): array
    {
        return ['pakketten' => app(Aanbod::class)->pakketten()];
    }
}
