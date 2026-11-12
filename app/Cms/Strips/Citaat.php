<?php

namespace App\Cms\Strips;

use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook citaat in het CMS: één groot citaat van Romy zelf op papier,
 * met het monogram, op de gestreepte achtergrond van de reviews. Hij
 * rendert met de view resources/views/strips/citaat.blade.php.
 *
 * Anders dan de strook reviews is dit geen lijst en heeft het geen naam.
 * Het zegel ligt vast.
 */
final class Citaat extends StripType
{
    public function key(): string
    {
        return 'citaat';
    }

    public function label(): string
    {
        return 'Citaat';
    }

    public function description(): string
    {
        return 'Eén groot citaat op papier, met het monogram.';
    }

    public function fields(): array
    {
        return [
            Text::make('kop')
                ->default('Kiezen voor wat mijn hart al lang zei')
                ->hint('Korte regel boven het citaat. Mag leeg blijven.'),
            LongText::make('citaat')
                ->required()
                ->default('Na een pittige periode heb ik bewust stilgestaan bij wat ik echt wil. Het antwoord was er al heel lang: stellen helpen om hun dag zonder zorgen te beleven.'),
        ];
    }
}
