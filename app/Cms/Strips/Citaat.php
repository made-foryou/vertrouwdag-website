<?php

namespace App\Cms\Strips;

use Made\CmsPackage\Content\Strips\Fields\Choice;
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
 *
 * Since the strips were merged (2026-11-28) the variant "In een artikel" is the old artikel-citaat: one line out
 * of a blog, in the narrow column of the article, without a heading or the monogram. Those strips were moved to
 * this key with that variant by a migration.
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
        return 'Eén groot citaat op papier: met het monogram, of in een artikel.';
    }

    public function fields(): array
    {
        return [
            Choice::make('variant', [
                'groot' => 'Groot op de strepen, met een kop en het monogram',
                'artikel' => 'In een artikel: in de smalle kolom, zonder kop en monogram',
            ])
                ->label('Uiterlijk')
                ->required()
                ->default('groot'),
            Text::make('kop')
                ->default('Kiezen voor wat mijn hart al lang zei')
                ->hint('Korte regel boven het citaat. Mag leeg blijven. Niet in een artikel.'),
            LongText::make('citaat')
                ->required()
                ->default('Na een pittige periode heb ik bewust stilgestaan bij wat ik echt wil. Het antwoord was er al heel lang: stellen helpen om hun dag zonder zorgen te beleven.'),
        ];
    }
}
