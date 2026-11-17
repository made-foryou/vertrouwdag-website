<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook kijkje in het CMS: "Een kijkje achter de planner", een korte
 * tekst met twee persoonlijke polaroids. Hij rendert met de view
 * resources/views/strips/kijkje.blade.php.
 *
 * Het zijn precies twee polaroids, want de stylesheet legt ze met
 * nth-child neer. Hun foto's zijn te kiezen; leeg is de foto uit het
 * ontwerp (#21).
 */
final class Kijkje extends StripType
{
    public function key(): string
    {
        return 'kijkje';
    }

    public function label(): string
    {
        return 'Kijkje met twee polaroids';
    }

    public function description(): string
    {
        return 'Korte persoonlijke tekst met twee polaroids die over elkaar liggen.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Even voorstellen')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Een kijkje *achter de planner*'),
            LongText::make('tekst')
                ->default('Bruiloften hebben me altijd al geraakt, dat zie je wel aan die sluier. En als ik niet met jullie dag bezig ben, zoek ik de rust graag op, het liefst samen aan zee.'),
            Velden::foto('foto_links', 'Staand, 4 bij 5: de polaroid snijdt hem bij.')
                ->label('Foto op de linker polaroid'),
            Velden::foto('foto_rechts', 'Staand, 4 bij 5: de polaroid snijdt hem bij.')
                ->label('Foto op de rechter polaroid'),
            Text::make('bijschrift_links')
                ->label('Bijschrift bij de linker polaroid')
                ->default('al vroeg in het wit'),
            Text::make('bijschrift_rechts')
                ->label('Bijschrift bij de rechter polaroid')
                ->default('samen aan zee'),
        ];
    }
}
