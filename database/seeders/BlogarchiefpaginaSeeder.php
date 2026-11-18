<?php

namespace Database\Seeders;

/**
 * Zet het blogarchief in het CMS (#20), onder /blog, met de stroken uit
 * het ontwerp. De kaarten komen uit de artikelen (App\Support\Blogs). Het
 * archief is zelf geen artikel: het heeft geen artikelkop.
 */
class BlogarchiefpaginaSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return '/blog/archief';
    }

    protected function titel(): string
    {
        return 'Archief';
    }

    protected function ouder(): ?string
    {
        return '/blog';
    }

    protected function inMenu(): bool
    {
        return false;
    }

    protected function stroken(): array
    {
        return [
            ['hero-archief'],
            ['blog-archief'],
            ['cta', [
                'kop' => ['text' => 'Liever direct *persoonlijk advies?*'],
                'tekst' => null,
                'variant' => 'compact',
            ]],
        ];
    }
}
