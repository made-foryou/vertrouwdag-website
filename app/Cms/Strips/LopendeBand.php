<?php

namespace App\Cms\Strips;

use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook lopende-band in het CMS: korte teksten die met hartjes
 * ertussen voorbij schuiven. Hij rendert met de view
 * resources/views/strips/lopende-band.blade.php.
 *
 * De band heeft geen data-cms-items: lopende-band.js vult hem aan tot hij
 * vol is, dus zijn kinderen zijn geen items. Een wijziging herlaadt het
 * frame na het opslaan.
 */
final class LopendeBand extends StripType
{
    public function key(): string
    {
        return 'lopende-band';
    }

    public function label(): string
    {
        return 'Lopende band';
    }

    public function description(): string
    {
        return 'Korte teksten die met hartjes ertussen voorbij schuiven.';
    }

    public function fields(): array
    {
        return [
            Items::make('teksten', [
                Text::make('tekst')->required(),
            ])
                ->min(1)
                ->itemLabel('Tekst')
                ->default([
                    ['tekst' => 'Jullie dag in vertrouwde handen'],
                    ['tekst' => 'Rust, aandacht en overzicht'],
                    ['tekst' => 'Ik heb het overzicht, jullie genieten'],
                    ['tekst' => 'Weddingplanner in Zuid-Holland'],
                ])
                ->hint('Kort houden: de band herhaalt de teksten tot hij vol is.'),
        ];
    }
}
