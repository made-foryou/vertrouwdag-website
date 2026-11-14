<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook kennismaking in het CMS: een uitnodiging om kennis te maken,
 * op aubergine, als afsluiter van een blog. Hij rendert met de view
 * resources/views/strips/kennismaking.blade.php. De knop komt uit
 * config/vertrouwdag.php, net als die in de header.
 */
final class Kennismaking extends StripType
{
    public function key(): string
    {
        return 'kennismaking';
    }

    public function label(): string
    {
        return 'Blog: kennismaking';
    }

    public function description(): string
    {
        return 'Afsluiter van een blog: een uitnodiging om kennis te maken, met de knop naar de kennismaking.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Zullen we kennismaken?')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Benieuwd wat het *voor jullie* kan betekenen?'),
            LongText::make('tekst')
                ->default('Een half uur, online en vrijblijvend.'),
        ];
    }
}
