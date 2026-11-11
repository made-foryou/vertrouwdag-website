<?php

namespace App\Cms;

use Made\CmsPackage\Content\Strips\Fields\Text;

/**
 * Velden die in meerdere striptypes terugkomen, zodat ze overal hetzelfde
 * werken en op één plek veranderen.
 */
final class Velden
{
    /**
     * Een kop met een accent: de woorden tussen sterretjes worden schuin en
     * gekleurd, zoals "Jullie dag in *vertrouwde* handen". De view zet de
     * kop om met <x-accent>.
     *
     * Het package kent de sterretjes nog niet (made-cms-package#144). Tot
     * dan herlaadt het frame bij een kop met accent na het opslaan, in
     * plaats van direct mee te kijken. Kent het package de markering, dan
     * wordt dit een Heading met nadruk (#22), en verandert alleen deze plek.
     */
    public static function kopMetAccent(string $key = 'kop'): Text
    {
        return Text::make($key)
            ->hint('Zet het accent tussen sterretjes, zoals: Jullie dag in *vertrouwde* handen.');
    }
}
