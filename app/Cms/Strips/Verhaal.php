<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\RichText;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook verhaal in het CMS: een grote kop met daaronder lopende tekst.
 * Hij rendert met de view resources/views/strips/verhaal.blade.php.
 *
 * De tekst is opgemaakte tekst: een of meer alinea's. Op /over-mij is het
 * nog Lorem ipsum, zoals in het ontwerp. De echte tekst levert Romy aan
 * in #27.
 */
final class Verhaal extends StripType
{
    public function key(): string
    {
        return 'verhaal';
    }

    public function label(): string
    {
        return 'Verhaal';
    }

    public function description(): string
    {
        return 'Grote kop met daaronder een of meer alinea\'s tekst.';
    }

    public function fields(): array
    {
        return [
            Velden::kopMetAccent()
                ->required()
                ->default('Van stagiaire *tot planner*'),
            RichText::make('tekst')
                ->required()
                ->default('<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante venenatis dapibus. Curabitur blandit tempus porttitor, vestibulum id ligula porta felis euismod semper. Nullam quis risus eget urna mollis ornare vel eu leo.</p><p>Donec ullamcorper nulla non metus auctor fringilla. Maecenas faucibus mollis interdum, cras mattis consectetur purus sit amet fermentum. Aenean lacinia bibendum nulla sed consectetur, sed posuere consectetur est at lobortis.</p>'),
        ];
    }
}
