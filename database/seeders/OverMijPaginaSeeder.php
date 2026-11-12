<?php

namespace Database\Seeders;

/**
 * Zet /over-mij in het CMS (#17), met de stroken uit het ontwerp (#7).
 * De tekst van het verhaal is nog Lorem ipsum, tot Romy hem aanlevert
 * (#27).
 */
class OverMijPaginaSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return '/over-mij';
    }

    protected function titel(): string
    {
        return 'Over mij';
    }

    protected function stroken(): array
    {
        return [
            ['hero-over-mij'],
            ['lopende-band', [
                'teksten' => [
                    ['tekst' => 'Punctueel'],
                    ['tekst' => 'Oog voor detail'],
                    ['tekst' => 'Vanuit het hart'],
                    ['tekst' => 'Thuis in Zuid-Holland'],
                ],
            ]],
            ['verhaal'],
            ['kijkje'],
            ['citaat'],
            ['kwaliteiten'],
            ['cta', [
                'kop' => 'Ik maak graag *kennis*',
                'tekst' => 'Vertel me over jullie plannen. Een half uur, online en vrijblijvend.',
                'variant' => 'eenvoudig',
            ]],
        ];
    }
}
