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

    protected function metaTitle(): ?string
    {
        return 'Over Romy | Persoonlijke weddingplanner in Zuid-Holland';
    }

    protected function metaDescription(): ?string
    {
        return 'Ik ben Romy, de weddingplanner achter vertrouwdag. Punctueel, met oog voor detail en vanuit het hart, zodat jullie op jullie trouwdag alleen maar hoeven te genieten.';
    }

    protected function shareImage(): ?string
    {
        return 'romy.jpg';
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
                'kop' => ['text' => 'Ik maak graag *kennis*'],
                'tekst' => 'Vertel me over jullie plannen. Een half uur, online en vrijblijvend.',
                'variant' => 'eenvoudig',
            ]],
        ];
    }
}
