<?php

namespace Database\Seeders;

/**
 * Zet /veelgestelde-vragen in het CMS (#18), met de stroken uit het
 * ontwerp (#9). De vragen staan als standaardwaarden in de strook faq; het
 * antwoord over de kosten noemt de prijzen uit de pakketten op /diensten.
 */
class VeelgesteldeVragenpaginaSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return '/veelgestelde-vragen';
    }

    protected function titel(): string
    {
        return 'Veelgestelde vragen';
    }

    protected function stroken(): array
    {
        return [
            ['hero-faq'],
            ['faq'],
            ['cta', [
                'kop' => ['text' => 'Nog een *vraag?*'],
                'tekst' => 'Die beantwoord ik graag in een vrijblijvend gesprek.',
                'variant' => 'middel',
            ]],
        ];
    }
}
