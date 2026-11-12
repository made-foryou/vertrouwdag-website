<?php

namespace Database\Seeders;

use App\Support\Aanbod;

/**
 * Zet /diensten in het CMS (#17), met de stroken uit het ontwerp (#6). De
 * strook pakketten is de bron van de prijzen voor de hele site
 * (App\Support\Aanbod).
 */
class DienstenpaginaSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return Aanbod::ADRES;
    }

    protected function titel(): string
    {
        return 'Diensten';
    }

    protected function stroken(): array
    {
        return [
            ['hero-diensten'],
            ['lopende-band', [
                'teksten' => [
                    ['tekst' => 'De Dag in handen'],
                    ['tekst' => 'Hand in hand'],
                    ['tekst' => 'Helemaal in handen'],
                    ['tekst' => 'Maatwerk mogelijk'],
                ],
            ]],
            ['pakketten'],
            ['vergelijking'],
            ['cta', [
                'kop' => 'Twijfelen jullie *welk pakket?*',
                'tekst' => 'Dat zoeken we samen uit, in een vrijblijvend gesprek van een half uur.',
                'variant' => 'groot',
            ]],
        ];
    }
}
