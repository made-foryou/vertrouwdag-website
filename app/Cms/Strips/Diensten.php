<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use App\Support\Dienst;
use Made\CmsPackage\Content\Strips\Fields\Button;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook diensten in het CMS: de drie diensten als mapjes met prijs,
 * die elk de eigen map op /diensten openen. Hij rendert met de view
 * resources/views/strips/diensten.blade.php.
 *
 * Het veld dienst kiest een van de drie diensten uit App\Support\Dienst.
 * Dat bepaalt de prijs, de kleur, de foto en het anker op /diensten, dus
 * verandert de volgorde, dan schuift de prijs mee met de dienst. De
 * prijzen komen uit config/vertrouwdag.php, dezelfde bron als op
 * /diensten, tot #17 kiest waar Romy ze wijzigt.
 */
final class Diensten extends StripType
{
    public function key(): string
    {
        return 'diensten';
    }

    public function label(): string
    {
        return 'Diensten';
    }

    public function description(): string
    {
        return 'De drie diensten als mapjes met prijs, elk met een link naar zijn map op Diensten.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Diensten')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Mijn *diensten*'),
            Button::make('knop')
                ->default(['text' => 'Alle diensten en wat erin zit', 'link' => ['url' => '/diensten']]),
            Items::make('diensten', [
                Choice::make('dienst', [
                    Dienst::Dag->value => 'Dagcoördinatie',
                    Dienst::Partial->value => 'Gedeeltelijke planning',
                    Dienst::Full->value => 'Full service',
                ])
                    ->required()
                    ->default(Dienst::Dag->value)
                    ->hint('Bepaalt de prijs, de kleur, de foto en de map op Diensten.'),
                Text::make('soort')
                    ->required()
                    ->hint('Korte regel boven de titel.'),
                Text::make('titel')->required(),
                LongText::make('omschrijving')->required(),
            ])
                ->min(1)
                ->max(3)
                ->itemLabel('Dienst')
                ->default([
                    ['dienst' => Dienst::Dag->value, 'soort' => 'Dagcoördinatie', 'titel' => 'De Dag in handen', 'omschrijving' => 'Jullie plannen, ik neem het zes weken ervoor over en ben op de dag het aanspreekpunt.'],
                    ['dienst' => Dienst::Partial->value, 'soort' => 'Gedeeltelijke planning', 'titel' => 'Hand in hand', 'omschrijving' => 'Locatie staat al? Ik help met leveranciers, planning en budget, en coördineer de dag.'],
                    ['dienst' => Dienst::Full->value, 'soort' => 'Full service', 'titel' => 'Helemaal in handen', 'omschrijving' => 'Van de eerste locatiebezichtiging tot de dag erna. Jullie kiezen, ik regel de rest.'],
                ])
                ->hint('Elke dienst één keer. De prijs hoort bij de dienst.'),
        ];
    }
}
