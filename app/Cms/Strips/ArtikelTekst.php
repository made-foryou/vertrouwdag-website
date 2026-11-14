<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\RichText;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook artikel-tekst in het CMS: lopende tekst van een blog, in een
 * smalle kolom. Hij rendert met de view
 * resources/views/strips/artikel-tekst.blade.php.
 *
 * Opgemaakte tekst (RichText) kent geen tussenkoppen: een <h2> wordt daar
 * gewone tekst. Daarom heeft de strook een eigen kop, en begint een
 * tussenkop een nieuwe strook (#20), zoals de secties van juridisch.
 */
final class ArtikelTekst extends StripType
{
    public function key(): string
    {
        return 'artikel-tekst';
    }

    public function label(): string
    {
        return 'Blog: tekst';
    }

    public function description(): string
    {
        return 'Een stuk tekst van een blog, met een tussenkop als je wilt. Begin voor elke tussenkop een nieuwe strook.';
    }

    public function fields(): array
    {
        return [
            Velden::kopMetAccent()
                ->hint('Tussenkop boven de tekst. Mag leeg blijven. Zet het accent tussen sterretjes, zoals: Het begin *bepaalt de rest*.'),
            LongText::make('inleiding')
                ->hint('Groter, boven de tekst. Meestal alleen in de eerste tekst van een blog. Mag leeg blijven.'),
            RichText::make('tekst')
                ->required(),
        ];
    }
}
