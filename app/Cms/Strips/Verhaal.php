<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\RichText;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook verhaal in het CMS: een kop met daaronder lopende tekst. Hij
 * rendert met de view resources/views/strips/verhaal.blade.php.
 *
 * De tekst is opgemaakte tekst: een of meer alinea's. Opgemaakte tekst
 * (RichText) kent geen tussenkoppen: een <h2> wordt daar gewone tekst.
 * Daarom heeft de strook een eigen kop, en begint een tussenkop een
 * nieuwe strook, zoals bij artikel-tekst.
 *
 * Since the strips were merged (2026-11-28) this is the one text strip of the site, called "Tekst" in the cms. It
 * took over tekst (the same markup under another class name) and tekst-met-afbeelding (the same fields plus a photo
 * and its side). Without a photo it is the column of the old verhaal; with a photo it is the old text with an
 * image. The key stays verhaal, so the stored strips did not have to change. The photo has no default (the id of a
 * file differs per database) and nothing to fall back on: empty means no photo.
 */
final class Verhaal extends StripType
{
    public function key(): string
    {
        return 'verhaal';
    }

    public function label(): string
    {
        return 'Tekst';
    }

    public function description(): string
    {
        return 'Een kop met tekst, met of zonder afbeelding ernaast.';
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
            Velden::foto('afbeelding', 'Mag leeg blijven. Staand, 4 bij 5: de boog snijdt hem bij. Zonder afbeelding staat de tekst in één kolom.')
                ->label('Afbeelding'),
            Choice::make('positie', [
                'links' => 'Links van de tekst',
                'rechts' => 'Rechts van de tekst',
            ])
                ->label('Plek van de afbeelding')
                ->required()
                ->default('links')
                ->hint('Alleen met een afbeelding. Op een telefoon staat de afbeelding altijd boven de tekst.'),
        ];
    }
}
