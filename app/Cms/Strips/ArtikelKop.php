<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use App\Support\Blog;
use App\Support\Blogcategorie;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\Image;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook artikel-kop in het CMS: de kop van een blog, met onderwerp,
 * leestijd, titel en een grote foto. Hij rendert met de view
 * resources/views/strips/artikel-kop.blade.php.
 *
 * Een pagina direct onder /blog met deze strook is een artikel (#20,
 * optie B). Titel, intro, onderwerp, leestijd en foto staan ook op de
 * kaart op /blog en in het archief (App\Support\Blogs). Het package heeft
 * nog geen collectie voor blogs; tot die er is, voegt Romy de stroken van
 * een artikel zelf toe.
 *
 * Titel, intro en foto hebben geen standaardwaarde: ze verschillen per
 * artikel, en een vergeten standaardtekst zou als kaart op /blog staan.
 */
final class ArtikelKop extends StripType
{
    public function key(): string
    {
        return 'artikel-kop';
    }

    public function label(): string
    {
        return 'Blog: artikelkop';
    }

    public function description(): string
    {
        return 'De kop van een blog, als eerste strook. Titel, intro, onderwerp en foto staan ook op de kaart op /blog en in het archief.';
    }

    public function fields(): array
    {
        return [
            Velden::kopMetAccent('titel')
                ->tag('h1')
                ->label('Titel')
                ->required(),
            LongText::make('intro')
                ->required()
                ->hint('Eén of twee zinnen voor de kaart op /blog en in het archief. Ze staan niet in het artikel zelf.'),
            Choice::make('categorie', Blogcategorie::class)
                ->label('Onderwerp')
                ->required(),
            Choice::make('leestijd', self::leestijden())
                ->hint('Leeg: zonder leestijd.'),
            Image::make('foto')
                ->required()
                ->hint('Liggend. Dezelfde foto staat op de kaart en bij een gedeelde link.'),
            LongText::make('sticker')
                ->hint('Het rondje op de foto, op één of twee korte regels. Mag leeg blijven.'),
        ];
    }

    /**
     * @return array{blog: ?Blog}
     */
    public function with(array $values): array
    {
        return ['blog' => Blog::uitVelden($values)];
    }

    /**
     * De leestijden om uit te kiezen, in hele minuten.
     *
     * @return array<string, string>
     */
    private static function leestijden(): array
    {
        $leestijden = [];

        foreach (range(1, 20) as $minuten) {
            $leestijden[(string) $minuten] = $minuten === 1 ? '1 minuut' : "{$minuten} minuten";
        }

        return $leestijden;
    }
}
