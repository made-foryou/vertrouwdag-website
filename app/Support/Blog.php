<?php

namespace App\Support;

use Illuminate\Support\Facades\Vite;
use Made\CmsPackage\Content\Strips\Emphasis;

/**
 * Eén blog, zoals de kaarten, de uitgelichte blog en de kop van een
 * artikel hem tonen.
 *
 * Een artikel is een pagina in het CMS onder /blog, en de blog komt uit
 * de strook artikel-kop van die pagina (#20). Een blog die binnenkort
 * komt, staat als kaartje in de strook blog-laatste en heeft geen adres.
 *
 * De titel heeft de nadruk tussen sterretjes, zoals elke kop op de site
 * (<x-made-cms::emphasis>). De afbeelding is een url.
 */
final readonly class Blog
{
    /**
     * De foto van een blog waarvan de foto uit de Media-bibliotheek is
     * verdwenen, of nog niet gekozen in een concept: een pad voor
     * Vite::asset(). Zo houdt de kaart haar vorm.
     */
    public const string TERUGVALFOTO = 'resources/images/paar-landschap.jpg';

    public function __construct(
        public string $titel,
        public string $intro,
        public Blogcategorie $categorie,
        public ?int $leestijd,
        public string $afbeelding,
        public string $alt,
        public ?string $url = null,
        public bool $binnenkort = false,
    ) {}

    /**
     * De blog uit de velden van een strook, zoals het CMS ze rendert: de
     * artikelkop van een artikel, of een kaartje dat binnenkort komt. De
     * titel is een Kop ({text, tag}) in de artikelkop en een tekst in een
     * kaartje; de blog houdt alleen de tekst, want een kaart bepaalt zelf
     * het niveau. Een foto is {url, alt, …} of null. Null zolang de titel
     * of het onderwerp ontbreekt, zoals in een concept.
     *
     * @param  array<string, mixed>  $velden
     */
    public static function uitVelden(array $velden, ?string $url = null, bool $binnenkort = false): ?self
    {
        $titel = $velden['titel'] ?? null;
        $titel = is_array($titel) ? ($titel['text'] ?? null) : $titel;
        $categorie = is_string($velden['categorie'] ?? null) ? Blogcategorie::tryFrom($velden['categorie']) : null;
        $foto = is_array($velden['foto'] ?? null) ? $velden['foto'] : null;

        if (! is_string($titel) || $titel === '' || $categorie === null) {
            return null;
        }

        return new self(
            titel: $titel,
            intro: is_string($velden['intro'] ?? null) ? $velden['intro'] : '',
            categorie: $categorie,
            leestijd: is_numeric($velden['leestijd'] ?? null) ? (int) $velden['leestijd'] : null,
            afbeelding: is_string($foto['url'] ?? null) ? $foto['url'] : Vite::asset(self::TERUGVALFOTO),
            alt: is_string($foto['alt'] ?? null) ? $foto['alt'] : '',
            url: $url,
            binnenkort: $binnenkort,
        );
    }

    /**
     * Een blog staat online als hij een adres heeft en niet binnenkort komt.
     */
    public function isGepubliceerd(): bool
    {
        return ! $this->binnenkort && $this->url !== null;
    }

    /**
     * Het adres van het artikel, of null zolang de blog niet online staat.
     */
    public function url(): ?string
    {
        return $this->isGepubliceerd() ? $this->url : null;
    }

    /**
     * De regel boven de titel: de categorie met de leestijd, of met
     * "binnenkort" voor een blog die nog komt.
     */
    public function kenmerk(): string
    {
        return match (true) {
            $this->binnenkort => $this->categorie->label().' · binnenkort',
            $this->leestijd !== null => $this->categorie->label()." · {$this->leestijd} min",
            default => $this->categorie->label(),
        };
    }

    /**
     * De titel zonder de sterretjes van de nadruk, voor zoekmachines.
     */
    public function platteTitel(): string
    {
        return Emphasis::plain($this->titel);
    }
}
