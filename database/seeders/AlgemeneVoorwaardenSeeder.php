<?php

namespace Database\Seeders;

/**
 * Zet /algemene-voorwaarden in het CMS (#18) als strook juridisch, niet in
 * het menu. De tekst is nog een placeholder, zoals in het ontwerp (#9).
 * Romy levert de definitieve voorwaarden en de datum aan in #27.
 */
class AlgemeneVoorwaardenSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return '/algemene-voorwaarden';
    }

    protected function titel(): string
    {
        return 'Algemene voorwaarden';
    }

    protected function inMenu(): bool
    {
        return false;
    }

    protected function stroken(): array
    {
        return [
            ['juridisch', [
                'kop' => ['text' => 'Algemene *voorwaarden*'],
                'inleiding' => '<p>[Plak hier je algemene voorwaarden, Romy.]</p>',
                'secties' => [],
                'cookieknop' => null,
                'afsluiting' => null,
            ]],
        ];
    }
}
