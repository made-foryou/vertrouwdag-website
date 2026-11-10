<?php

namespace App\Support;

/**
 * Eén blog uit de lijst, zoals de kaarten, de uitgelichte blog en de kop
 * van een artikel hem tonen.
 *
 * De titel heeft het accent tussen sterretjes, zoals elke kop op de site
 * (<x-accent>). De afbeelding is een url.
 */
final readonly class Blog
{
    public function __construct(
        public string $titel,
        public string $intro,
        public Blogcategorie $categorie,
        public ?int $leestijd,
        public string $afbeelding,
        public string $alt,
        public ?string $slug = null,
        public bool $binnenkort = false,
    ) {}

    /**
     * Een blog staat online als hij een adres heeft en niet binnenkort komt.
     */
    public function isGepubliceerd(): bool
    {
        return ! $this->binnenkort && $this->slug !== null;
    }

    /**
     * Het adres van het artikel, of null zolang de blog niet online staat.
     */
    public function url(): ?string
    {
        return $this->isGepubliceerd() ? route('blog.artikel', $this->slug) : null;
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
     * De titel zonder de sterretjes van het accent, voor zoekmachines.
     */
    public function platteTitel(): string
    {
        return str_replace('*', '', $this->titel);
    }
}
