<?php

namespace App\Cms\Strips;

use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook artikel-citaat in het CMS: één zin uit een blog, groot op
 * papier. Hij rendert met de view
 * resources/views/strips/artikel-citaat.blade.php. Anders dan de strook
 * citaat heeft hij geen kop en geen monogram.
 */
final class ArtikelCitaat extends StripType
{
    public function key(): string
    {
        return 'artikel-citaat';
    }

    public function label(): string
    {
        return 'Blog: citaat';
    }

    public function description(): string
    {
        return 'Eén zin uit een blog, groot en iets scheef op papier.';
    }

    public function fields(): array
    {
        return [
            LongText::make('citaat')
                ->required(),
        ];
    }
}
