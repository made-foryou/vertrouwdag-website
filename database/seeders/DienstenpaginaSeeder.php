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

    protected function metaTitle(): ?string
    {
        return 'Diensten en prijzen | vertrouwdag weddingplanner';
    }

    protected function shareImage(): ?string
    {
        return 'ceremonie-buiten.jpg';
    }

    protected function stroken(): array
    {
        return [
            ['hero', [
                'variant' => 'diensten',
                'label' => 'Diensten en prijzen',
                'kop' => ['text' => 'Zoveel hulp *als jullie* nodig hebben'],
                'tekst' => 'Van alleen de dag zelf tot het hele traject. Elk pakket heeft een vaste vanaf-prijs, zodat jullie vooraf weten waar jullie aan toe zijn.',
                'knop' => ['text' => 'Vergelijk de pakketten', 'link' => ['url' => '#vergelijk']],
                'bijschrift' => 'alles geregeld',
                'sticker' => "vaste\nprijzen",
            ]],
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
                'kop' => ['text' => 'Twijfelen jullie *welk pakket?*'],
                'tekst' => 'Dat zoeken we samen uit, in een vrijblijvend gesprek van een half uur.',
                'variant' => 'groot',
            ]],
        ];
    }
}
