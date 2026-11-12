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
