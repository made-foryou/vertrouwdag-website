<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook contact in het CMS: een band voor wie liever eerst appt, met
 * briefjes naar WhatsApp, e-mail en Instagram. Hij rendert met de view
 * resources/views/strips/contact.blade.php.
 *
 * Het nummer, het e-mailadres en het Instagram-account komen uit
 * config/vertrouwdag.php. Het e-mailbriefje toont het adres zelf.
 */
final class Contact extends StripType
{
    public function key(): string
    {
        return 'contact';
    }

    public function label(): string
    {
        return 'Contact via WhatsApp, mail en Instagram';
    }

    public function description(): string
    {
        return 'Band met kop, tekst en briefjes naar WhatsApp, e-mail en Instagram.';
    }

    public function fields(): array
    {
        return [
            Velden::kopMetAccent()
                ->required()
                ->default('Liever eerst *even appen?*'),
            LongText::make('tekst')
                ->default('Dat mag natuurlijk ook. Ik reageer meestal binnen een werkdag.'),
            Text::make('app_tekst')
                ->label('Tekst op het WhatsApp-briefje')
                ->required()
                ->default('"Hoi Romy, wij gaan trouwen..."'),
            Text::make('insta_tekst')
                ->label('Tekst op het Instagram-briefje')
                ->required()
                ->default('Stuur een DM naar @vertrouwdag'),
        ];
    }
}
