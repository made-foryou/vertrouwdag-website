<?php

namespace Database\Seeders;

use App\Cms\Strips\Juridisch;

/**
 * Zet /privacyverklaring in het CMS (#18) als strook juridisch, niet in het
 * menu. De tekst is nog een placeholder, zoals in het ontwerp (#9). Romy
 * levert de definitieve privacyverklaring en de datum aan in #27.
 */
class PrivacyverklaringSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return '/privacyverklaring';
    }

    protected function titel(): string
    {
        return 'Privacyverklaring';
    }

    protected function inMenu(): bool
    {
        return false;
    }

    protected function stroken(): array
    {
        return [
            ['juridisch', [
                'kop' => 'Privacy*verklaring*',
                'inleiding' => '<p>[Plak hier je privacyverklaring, Romy.]</p>',
                'secties' => [],
                'cookieknop' => null,
                'afsluiting' => '<p>Vragen over privacy? Mail naar '.Juridisch::mailLink().'.</p>',
            ]],
        ];
    }
}
