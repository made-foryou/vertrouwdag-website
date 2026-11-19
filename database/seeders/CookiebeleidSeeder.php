<?php

namespace Database\Seeders;

/**
 * Zet /cookiebeleid in het CMS (#18) als strook juridisch, niet in het
 * menu. De tekst is de standaardwaarde van de strook; Romy levert de
 * definitieve versie en de datum aan in #27.
 */
class CookiebeleidSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return '/cookiebeleid';
    }

    protected function titel(): string
    {
        return 'Cookiebeleid';
    }

    protected function metaTitle(): ?string
    {
        return 'Cookiebeleid | vertrouwdag';
    }

    protected function metaDescription(): ?string
    {
        return 'Welke cookies vertrouwdag.nl gebruikt, waarvoor, en hoe jullie je keuze aanpassen.';
    }

    protected function inMenu(): bool
    {
        return false;
    }

    protected function stroken(): array
    {
        return [
            ['juridisch'],
        ];
    }
}
