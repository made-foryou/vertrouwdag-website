<?php

namespace App\Cms;

use Made\CmsPackage\Content\Strips\Fields\Heading;
use Made\CmsPackage\Content\Strips\Fields\Image;

/**
 * Velden die in meerdere striptypes terugkomen, zodat ze overal hetzelfde
 * werken en op één plek veranderen.
 */
final class Velden
{
    /**
     * Een kop met nadruk: de woorden tussen sterretjes worden schuin en
     * gekleurd, zoals "Jullie dag in *vertrouwde* handen" (#15). De waarde
     * is {text, tag}: de SEO-specialist kiest in de bewerkschil het element
     * (h1 t/m h6, of span). Het standaardniveau is h2; een strook met een
     * ander niveau in het ontwerp zet dat met ->tag().
     *
     * De view print de kop met <x-made-cms::heading … emphasis /> en geeft
     * de opmaak mee als class (.h1 t/m .h6). Zo verandert een ander niveau
     * het element, en niet hoe de kop eruitziet (#22). Het package toont de
     * hint over de sterretjes zelf.
     */
    public static function kopMetAccent(string $key = 'kop'): Heading
    {
        return Heading::make($key)->emphasis();
    }

    /**
     * Een foto uit de Media-bibliotheek op een plek waar het ontwerp al een
     * foto had (#21). Zolang het veld leeg is, toont de view die vaste foto
     * (<x-foto>). Daarom is het veld nooit verplicht: een strook van voor
     * #21 heeft geen foto en blijft zo op te slaan en te publiceren.
     *
     * vorm zegt hoe de plek de foto bijsnijdt, zoals "Staand: de boog
     * snijdt hem bij." Het focuspunt blijft daarbij in beeld.
     */
    public static function foto(string $key, string $vorm): Image
    {
        return Image::make($key)
            ->hint("{$vorm} Het focuspunt blijft in beeld. Leeg: de foto uit het ontwerp.");
    }
}
