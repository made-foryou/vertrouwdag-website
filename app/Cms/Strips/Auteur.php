<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\Button;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook auteur in het CMS: wie een blog schreef, met een polaroid van
 * Romy en een link naar meer over haar. Hij rendert met de view
 * resources/views/strips/auteur.blade.php. De foto is te kiezen; leeg is
 * de foto uit het ontwerp (#21).
 */
final class Auteur extends StripType
{
    public function key(): string
    {
        return 'auteur';
    }

    public function label(): string
    {
        return 'Blog: auteur';
    }

    public function description(): string
    {
        return 'Wie de blog schreef, met een foto van Romy en een link naar Over mij.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->required()
                ->default('Geschreven door Romy'),
            LongText::make('tekst')
                ->required()
                ->default('Weddingplanner in Zuid-Holland en de planner achter vertrouwdag.'),
            Button::make('link')
                ->default(['text' => 'Meer over mij', 'link' => ['url' => '/over-mij']])
                ->hint('Direct na de tekst.'),
            Velden::foto('foto', 'Vierkant: de polaroid snijdt hem bij.')
                ->label('Foto op de polaroid'),
        ];
    }
}
