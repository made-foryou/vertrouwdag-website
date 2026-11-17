<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\RichText;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook tekst in het CMS: een kop met daaronder opgemaakte tekst, in
 * één kolom. Een algemene bouwsteen voor elke pagina, zonder plek in het
 * ontwerp. Hij rendert met de view resources/views/strips/tekst.blade.php.
 *
 * Opgemaakte tekst (RichText) kent geen tussenkoppen: een <h2> wordt daar
 * gewone tekst. Daarom heeft de strook een eigen kop, en begint een
 * tussenkop een nieuwe strook, zoals bij artikel-tekst.
 */
final class Tekst extends StripType
{
    public function key(): string
    {
        return 'tekst';
    }

    public function label(): string
    {
        return 'Tekst';
    }

    public function description(): string
    {
        return 'Een kop met daaronder tekst: alinea\'s, lijstjes en links.';
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
        ];
    }
}
