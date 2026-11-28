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
            ['verhaal', [
                'kop' => ['text' => 'Van stagiaire *tot planner*'],
                'tekst' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante venenatis dapibus. Curabitur blandit tempus porttitor, vestibulum id ligula porta felis euismod semper. Nullam quis risus eget urna mollis ornare vel eu leo.</p><p>Donec ullamcorper nulla non metus auctor fringilla. Maecenas faucibus mollis interdum, cras mattis consectetur purus sit amet fermentum. Aenean lacinia bibendum nulla sed consectetur, sed posuere consectetur est at lobortis.</p>',
            ]],
            ['kijkje'],
            ['citaat'],
            ['herkenning', [
                'variant' => 'kwaliteiten',
                'label' => 'Waarom vertrouwdag',
                'kop' => ['text' => 'Vertrouwd, *zoals een zus* of goede vriendin'],
                'tekst' => 'Iemand die jullie wensen kent, eerlijk meedenkt en op de dag zelf alles opvangt. Zodat jullie, en jullie familie, er gewoon kunnen zijn.',
                'citaten' => [
                    ['titel' => 'Punctueel', 'tekst' => 'Afspraak is afspraak, ook met leveranciers.'],
                    ['titel' => 'Oog voor detail', 'tekst' => 'Van naamkaartjes tot de looproute van de ceremonie.'],
                    ['titel' => 'Lokaal', 'tekst' => 'Thuis in Zuid-Holland, van Rotterdam tot de Hoeksche Waard.'],
                ],
                'slotzin' => null,
            ]],
            ['cta', [
                'kop' => ['text' => 'Ik maak graag *kennis*'],
                'tekst' => 'Vertel me over jullie plannen. Een half uur, online en vrijblijvend.',
                'variant' => 'eenvoudig',
            ]],
        ];
    }
}
