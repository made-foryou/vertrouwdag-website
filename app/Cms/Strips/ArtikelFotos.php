<?php

namespace App\Cms\Strips;

use Made\CmsPackage\Content\Strips\Fields\Image;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook artikel-fotos in het CMS: een paar polaroids naast elkaar in
 * de kolom van een blog, om en om scheef. Hij rendert met de view
 * resources/views/strips/artikel-fotos.blade.php.
 *
 * Een foto heeft geen standaardwaarde (de id van een bestand verschilt
 * per database). De twee standaardfoto's hebben alleen een bijschrift;
 * publiceren kan pas als elke foto gekozen is.
 */
final class ArtikelFotos extends StripType
{
    public function key(): string
    {
        return 'artikel-fotos';
    }

    public function label(): string
    {
        return "Blog: foto's";
    }

    public function description(): string
    {
        return 'Een paar polaroids naast elkaar in de tekst van een blog, met een bijschrift.';
    }

    public function fields(): array
    {
        return [
            Items::make('fotos', [
                Image::make('foto')->required(),
                Text::make('bijschrift')
                    ->hint('Kort, onder de foto. Mag leeg blijven.'),
            ])
                ->label("Foto's")
                ->itemLabel('Foto')
                ->min(1)
                ->max(4)
                ->default([
                    ['bijschrift' => 'eerst de locatie'],
                    ['bijschrift' => 'dan de details'],
                ]),
        ];
    }
}
