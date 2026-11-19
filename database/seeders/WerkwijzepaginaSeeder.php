<?php

namespace Database\Seeders;

/**
 * Zet /werkwijze in het CMS (#17), met de stroken uit het ontwerp (#7).
 *
 * De fases lopen over drie stroken: de tijdlijn met fase 1 tot en met 4,
 * de trouwdag als fase 5 en een tweede tijdlijn met "Afronden" als fase 6.
 */
class WerkwijzepaginaSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return '/werkwijze';
    }

    protected function titel(): string
    {
        return 'Werkwijze';
    }

    protected function metaTitle(): ?string
    {
        return 'Werkwijze: zo plan ik jullie bruiloft | vertrouwdag';
    }

    protected function metaDescription(): ?string
    {
        return 'Van het eerste kennismakingsgesprek tot de dag na de bruiloft: zo werk ik als weddingplanner in Zuid-Holland, stap voor stap.';
    }

    protected function stroken(): array
    {
        return [
            ['hero-werkwijze'],
            ['tijdlijn'],
            ['trouwdag'],
            ['tijdlijn', [
                'variant' => null,
                'eerste_nummer' => '6',
                'fases' => [
                    [
                        'periode' => 'De dag erna',
                        'titel' => 'Afronden',
                        'tekst' => 'Ook de dag erna is geregeld. Vooraf spreek ik af wie wat opruimt en terugbrengt: de leveranciers zelf, de locatiemanager of één vaste persoon uit jullie kring. Zo weet iedereen precies wat er van hem verwacht wordt. Het enige wat jullie nog hoeven te doen: nagenieten.',
                    ],
                ],
            ]],
            ['cta', [
                'kop' => ['text' => 'Het begint met *een gesprek*'],
                'tekst' => 'Een half uur, online en vrijblijvend.',
                'variant' => 'eenvoudig',
            ]],
        ];
    }
}
