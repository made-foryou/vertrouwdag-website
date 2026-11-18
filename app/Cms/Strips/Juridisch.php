<?php

namespace App\Cms\Strips;

use App\Cms\Velden;
use App\Support\Contact;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\RichText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook juridisch in het CMS: een juridische pagina in een smalle
 * kolom, zoals de privacyverklaring, het cookiebeleid en de algemene
 * voorwaarden. Hij rendert met de view
 * resources/views/strips/juridisch.blade.php.
 *
 * Opgemaakte tekst (RichText) kent geen tussenkoppen: een <h2> wordt daar
 * gewone tekst. Daarom is de tekst verdeeld in secties, en wordt de kop
 * van een sectie een echte <h2> (#18). Ook een class of <code> houdt
 * RichText niet.
 *
 * De standaardwaarden zijn die van het cookiebeleid, de enige pagina waar
 * elk veld gevuld is. De privacyverklaring en de algemene voorwaarden
 * krijgen hun eigen tekst uit hun seeder. De definitieve teksten levert
 * Romy aan in #27.
 */
final class Juridisch extends StripType
{
    public function key(): string
    {
        return 'juridisch';
    }

    public function label(): string
    {
        return 'Juridische tekst';
    }

    public function description(): string
    {
        return 'Lopende tekst met secties onder een eigen kop, zoals een privacyverklaring of voorwaarden.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Goed om te weten')
                ->hint('Korte regel boven de kop.'),
            Velden::kopMetAccent()
                ->tag('h1')
                ->required()
                ->default('Cookie*beleid*'),
            Text::make('bijgewerkt')
                ->label('Laatst bijgewerkt')
                ->default('[datum]')
                ->hint('De datum, zoals 1 december 2026. Leeg: geen datum.'),
            RichText::make('inleiding')
                ->default('<p>Op vertrouwdag.nl gebruik ik zo min mogelijk cookies. Alleen wat nodig is om de site te laten werken staat altijd aan. Voor al het andere vraag ik eerst jullie toestemming.</p>')
                ->hint('De tekst vóór de eerste sectie. Mag leeg blijven.'),
            Items::make('secties', [
                Text::make('kop')
                    ->required()
                    ->hint('Wordt een tussenkop.'),
                RichText::make('tekst')->required(),
            ])
                ->itemLabel('Sectie')
                ->default([
                    [
                        'kop' => 'Noodzakelijk',
                        'tekst' => '<p>Ik onthoud jullie cookiekeuze in de browser (vd-cookies), zodat de melding niet bij elke pagina terugkomt. Dit bewaar ik op jullie eigen apparaat en het wordt niet met mij gedeeld.</p>',
                    ],
                    [
                        'kop' => 'Statistieken',
                        'tekst' => '<p>Met toestemming gebruik ik Google Analytics om te zien hoe de site gebruikt wordt, bijvoorbeeld welke pagina\'s het meest bekeken worden. IP-adressen worden geanonimiseerd. Zonder toestemming wordt Google Analytics niet geladen.</p>',
                    ],
                    [
                        'kop' => 'Marketing',
                        'tekst' => '<p>De afsprakenplanner op de pagina Kennismaken komt van Calendly. Calendly plaatst eigen cookies, daarom laad ik de agenda pas nadat jullie op "Agenda tonen" klikken of marketingcookies toestaan. Later gebruik ik mogelijk Google Ads; dat valt ook onder deze toestemming.</p>',
                    ],
                    [
                        'kop' => 'Je keuze aanpassen',
                        'tekst' => '<p>Jullie kunnen je keuze altijd wijzigen.</p>',
                    ],
                ])
                ->hint('Elke sectie heeft een eigen tussenkop. Mag leeg blijven.'),
            Text::make('cookieknop')
                ->label('Knop naar de cookie-instellingen')
                ->default('Cookie-instellingen openen')
                ->hint('De knop opent de cookie-instellingen. Leeg: geen knop.'),
            RichText::make('afsluiting')
                ->default('<p>Vragen? Mail naar '.self::mailLink().'. Meer over hoe ik met persoonsgegevens omga staat in de <a href="/privacyverklaring">privacyverklaring</a>.</p>')
                ->hint('De tekst na de secties. Mag leeg blijven.'),
        ];
    }

    /**
     * Een link naar het e-mailadres uit config/vertrouwdag.php, met het
     * adres als tekst, zoals <x-mail-link> hem maakt. Opgeslagen in een
     * tekst volgt hij de config daarna niet meer.
     */
    public static function mailLink(): string
    {
        return '<a href="'.e(Contact::mailUrl()).'">'.e(config('vertrouwdag.email')).'</a>';
    }
}
