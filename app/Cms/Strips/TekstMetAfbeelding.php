<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\Image;
use Made\CmsPackage\Content\Strips\Fields\RichText;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook tekst-met-afbeelding in het CMS: een kop en opgemaakte tekst,
 * met links of rechts ervan een staande afbeelding in een boog. Een
 * algemene bouwsteen voor elke pagina, zonder plek in het ontwerp. Hij
 * rendert met de view resources/views/strips/tekst-met-afbeelding.blade.php.
 *
 * De afbeelding heeft geen standaardwaarde (de id van een bestand
 * verschilt per database). Er is ook geen foto uit het ontwerp om op
 * terug te vallen, dus publiceren kan pas als de afbeelding gekozen is.
 */
final class TekstMetAfbeelding extends StripType
{
    public function key(): string
    {
        return 'tekst-met-afbeelding';
    }

    public function label(): string
    {
        return 'Tekst met afbeelding';
    }

    public function description(): string
    {
        return 'Een kop met tekst, en links of rechts ervan een afbeelding.';
    }

    public function fields(): array
    {
        return [
            Velden::kopMetAccent()
                ->default('Een kop met *accent*')
                ->hint('Mag leeg blijven. Zet het accent tussen sterretjes, zoals: Jullie dag in *vertrouwde* handen.'),
            RichText::make('tekst')
                ->required()
                ->default('<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante venenatis dapibus. Curabitur blandit tempus porttitor, vestibulum id ligula porta felis euismod semper.</p><p>Donec ullamcorper nulla non metus auctor fringilla. Maecenas faucibus mollis interdum, cras mattis consectetur purus sit amet fermentum.</p>'),
            Image::make('afbeelding')
                ->required()
                ->hint('Staand, 4 bij 5: de boog snijdt hem bij. Het focuspunt blijft in beeld.'),
            Choice::make('positie', [
                'links' => 'Links van de tekst',
                'rechts' => 'Rechts van de tekst',
            ])
                ->label('Plek van de afbeelding')
                ->required()
                ->default('links')
                ->hint('Op een telefoon staat de afbeelding altijd boven de tekst.'),
        ];
    }
}
