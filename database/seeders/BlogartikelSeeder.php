<?php

namespace Database\Seeders;

/**
 * Zet het artikel "Wanneer schakel je een weddingplanner in?" in het CMS
 * (#20), onder /blog, op hetzelfde adres als ervoor. Zo bouwt Romy ook een
 * nieuw artikel op: een artikelkop, dan stroken tekst, foto's en citaat,
 * en als afsluiter de auteur en de kennismaking.
 *
 * Opgemaakte tekst kent geen tussenkoppen, dus begint elke tussenkop een
 * nieuwe strook tekst. De foto van de kop staat ook op de kaart op /blog
 * en bij een gedeelde link.
 *
 * Het artikel is nog niet af, zoals in het ontwerp. Romy schrijft het
 * verder in #27.
 */
class BlogartikelSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return '/blog/wanneer-weddingplanner-inschakelen';
    }

    protected function titel(): string
    {
        return 'Wanneer schakel je een weddingplanner in?';
    }

    protected function metaTitle(): ?string
    {
        return 'Wanneer schakel je een weddingplanner in? | vertrouwdag';
    }

    protected function metaDescription(): ?string
    {
        return 'Wanneer schakel je een weddingplanner in? Voor full service het liefst 12 tot 18 maanden van tevoren. Waarom een vroeg begin juist rust oplevert.';
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
            ['artikel-kop', [
                'titel' => ['text' => 'Wanneer schakel je een weddingplanner in? *(En waarom eerder dan je denkt)*'],
                'intro' => 'De meeste stellen bellen pas als het al druk is. Waarom een vroeg begin juist rust oplevert.',
                'categorie' => 'planning',
                'leestijd' => '6',
                'foto' => $this->foto('paar-landschap.jpg', 'Bruidspaar wandelt door een weids landschap'),
                'sticker' => "hoe eerder,\nhoe rustiger",
            ]],
            ['artikel-tekst', [
                'inleiding' => 'De meeste stellen zoeken pas hulp als het al druk is. Dat is begrijpelijk, maar juist in het begin worden de keuzes gemaakt die later rust geven.',
                'tekst' => '<p>Jullie zijn net verloofd, de champagne is op, en dan komt de vraag: waar beginnen we? Vaak is het antwoord: met een locatie zoeken, in de avonduren, naast een volle werkweek.</p>',
            ]],
            ['artikel-tekst', [
                'kop' => ['text' => 'Het begin *bepaalt de rest*'],
                'tekst' => '<p>In de eerste maanden kiezen jullie de datum, de locatie en het budget. Dat zijn de keuzes waar alles aan vastzit. Een planner die dan al meedenkt, voorkomt dat jullie later moeten schuiven, of meer betalen dan nodig.</p>',
            ]],
            ['artikel-fotos', [
                'fotos' => [
                    ['foto' => $this->foto('ceremonie-buiten.jpg', 'Ceremonieplek buiten met witte stoelen'), 'bijschrift' => 'eerst de locatie'],
                    ['foto' => $this->foto('feesttafel.jpg', 'Gedekte feesttafel met bloemen'), 'bijschrift' => 'dan de details'],
                ],
            ]],
            ['artikel-tekst', [
                'tekst' => '<p>Voor Helemaal in handen (full service) is 12 tot 18 maanden van tevoren een fijn moment. Willen jullie alleen hulp op de dag zelf, dan is 3 tot 6 maanden ervoor vaak genoeg.</p>',
            ]],
            ['artikel-citaat', [
                'citaat' => 'Hoe eerder jullie hulp vragen, hoe meer van de voorbereiding ook echt leuk blijft.',
            ]],
            ['artikel-tekst', [
                'kop' => ['text' => 'Drie signalen *dat het tijd is*'],
                'tekst' => '<p>Jullie merken dat de bruiloft elke avond het gesprek is. Jullie familie biedt aan om dingen te regelen, en jullie weten niet goed hoe je dat vriendelijk afslaat. Of jullie hebben al drie offertes liggen en geen idee hoe je ze vergelijkt.</p><p>[Hier verder schrijven, Romy.]</p>',
            ]],
            ['auteur'],
            ['kennismaking'],
        ];
    }
}
