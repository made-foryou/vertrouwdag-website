<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook twee-kolommen in het CMS: "Hart én hoofd", een grote kop met
 * een inleiding en twee kolommen met een foto en een kaart erop. Hij
 * rendert met de view resources/views/strips/twee-kolommen.blade.php.
 *
 * De foto hoort bij de plek van de kolom en ligt vast tot #21. Er zijn
 * twee foto's, dus hooguit twee kolommen.
 */
final class TweeKolommen extends StripType
{
    public function key(): string
    {
        return 'twee-kolommen';
    }

    public function label(): string
    {
        return 'Twee kolommen';
    }

    public function description(): string
    {
        return 'Grote kop met een inleiding en twee kolommen met een foto en een kaart.';
    }

    public function fields(): array
    {
        return [
            Velden::kopMetAccent()
                ->required()
                ->default('Hart *én* hoofd'),
            LongText::make('tekst')
                ->default('Warm en persoonlijk, en tegelijk strak georganiseerd. Die combinatie geeft jullie rust, van het eerste gesprek tot de laatste dans.'),
            Items::make('kolommen', [
                Text::make('label')
                    ->hint('Korte regel boven de kop van de kaart.'),
                Velden::kopMetAccent()->required(),
                LongText::make('tekst')->required(),
            ])
                ->min(1)
                ->max(2)
                ->itemLabel('Kolom')
                ->default([
                    ['label' => 'Het hart van', 'kop' => '*een goede* vriendin', 'tekst' => 'Ik denk met jullie mee zoals een zus dat zou doen. Eerlijk, warm en met oog voor wat jullie echt belangrijk vinden.'],
                    ['label' => 'Het hoofd van', 'kop' => '*een* projectmanager', 'tekst' => 'Als General Manager stuur ik al jaren projecten met deadlines, budgetten en veel partijen. Punctueel, tot in de details.'],
                ])
                ->hint('De foto hoort bij de plek van de kolom.'),
        ];
    }
}
