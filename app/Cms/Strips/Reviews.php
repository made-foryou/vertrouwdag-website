<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook reviews in het CMS: reviews op papier onder een kop. Hij
 * rendert met de view resources/views/strips/reviews.blade.php.
 *
 * De eerste review krijgt het monogram; dat ligt vast in de view. De
 * standaardwaarden zijn de placeholders van de homepage, tot de echte
 * reviews er zijn (#27).
 */
final class Reviews extends StripType
{
    public function key(): string
    {
        return 'reviews';
    }

    public function label(): string
    {
        return 'Reviews';
    }

    public function description(): string
    {
        return 'Reviews op papier onder een kop, de eerste met het monogram.';
    }

    public function fields(): array
    {
        return [
            Velden::kopMetAccent()
                ->required()
                ->default('Wat anderen *zeggen*'),
            Items::make('reviews', [
                Text::make('label')
                    ->hint('Wie het zegt, zoals "Collega uit de trouwbranche".'),
                LongText::make('citaat')->required(),
                Text::make('naam'),
            ])
                ->min(1)
                ->itemLabel('Review')
                ->default([
                    ['label' => 'Collega uit de trouwbranche', 'citaat' => '"[Tekst review leverancier 1]"', 'naam' => '[Naam], [Bedrijf]'],
                    ['label' => 'Collega uit de trouwbranche', 'citaat' => '"[Tekst review leverancier 2]"', 'naam' => '[Naam], [Bedrijf]'],
                ]),
        ];
    }
}
