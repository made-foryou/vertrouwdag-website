<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Button;
use Made\CmsPackage\Content\Strips\Fields\RichText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook over-mij-intro in het CMS: een korte kennismaking met Romy,
 * met haar foto in een boog, een polaroid en een knop naar haar verhaal.
 * Hij rendert met de view resources/views/strips/over-mij-intro.blade.php.
 *
 * De tekst is opgemaakte tekst: een of meer alinea's. De foto's liggen
 * vast tot #21.
 */
final class OverMijIntro extends StripType
{
    public function key(): string
    {
        return 'over-mij-intro';
    }

    public function label(): string
    {
        return 'Over mij, kort';
    }

    public function description(): string
    {
        return 'Korte kennismaking met Romy, met haar foto, een polaroid en een knop naar haar verhaal.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Over mij')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->required()
                ->default('Hoi, ik ben *Romy*'),
            RichText::make('tekst')
                ->default('<p>Ik begon als stagiaire bij een creatief bureau en werk daar nu als General Manager. Plannen, overzicht houden en op de details letten zit in mijn hoofd en in mijn hart.</p><p>Met vertrouwdag doe ik dat voor jullie. Zodat jullie op jullie dag alleen maar hoeven te genieten.</p>'),
            Button::make('knop')
                ->default(['text' => 'Lees mijn verhaal', 'link' => ['url' => '/over-mij']]),
            Text::make('bijschrift')
                ->label('Bijschrift bij de polaroid')
                ->default('oog voor detail'),
        ];
    }
}
