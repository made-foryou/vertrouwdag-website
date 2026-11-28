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
            ['kwaliteiten'],
            ['cta', [
                'kop' => ['text' => 'Ik maak graag *kennis*'],
                'tekst' => 'Vertel me over jullie plannen. Een half uur, online en vrijblijvend.',
                'variant' => 'eenvoudig',
            ]],
        ];
    }
}
