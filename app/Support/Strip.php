<?php

namespace App\Support;

/**
 * Eén strook van een pagina met zijn inhoud, zoals een strip-view in
 * resources/views/strips/{type}.blade.php hem als $strip krijgt.
 *
 * Gedraagt zich als RenderedStrip uit made-cms-package: een veld lees je
 * als property ($strip->kop) en een veld zonder waarde geeft null. Een
 * lijst is een array van items, en elk item is een array met al zijn
 * velden. Zolang de inhoud vastligt, vult de paginaview deze objecten.
 * Met de CMS-koppeling (#13) levert het package ze, en blijven de views
 * gelijk.
 */
final readonly class Strip
{
    /**
     * @param  array<string, mixed>  $values
     */
    public function __construct(
        public string $id,
        public string $type,
        public array $values,
    ) {}

    public function __get(string $name): mixed
    {
        return $this->values[$name] ?? null;
    }

    public function __isset(string $name): bool
    {
        return isset($this->values[$name]);
    }
}
