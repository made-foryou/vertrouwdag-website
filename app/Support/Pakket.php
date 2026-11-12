<?php

namespace App\Support;

/**
 * Eén dienst zoals hij in de strook pakketten op /diensten staat: welke
 * dienst het is, de soort ("Dagcoördinatie"), de titel ("De Dag in
 * handen") en de vanaf-prijs. De prijs is null als de redacteur geen heel
 * bedrag typte.
 */
final readonly class Pakket
{
    public function __construct(
        public Dienst $dienst,
        public string $soort,
        public string $titel,
        public ?Prijs $prijs,
    ) {}
}
