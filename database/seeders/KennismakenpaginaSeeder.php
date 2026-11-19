<?php

namespace Database\Seeders;

/**
 * Zet /kennismaken in het CMS (#18), met de stroken uit het ontwerp (#10).
 * De pagina staat niet in het menu: de knop in de header linkt ernaar.
 */
class KennismakenpaginaSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return '/kennismaken';
    }

    protected function titel(): string
    {
        return 'Kennismaken';
    }

    protected function metaTitle(): ?string
    {
        return 'Plan een kennismaking | vertrouwdag weddingplanner';
    }

    protected function metaDescription(): ?string
    {
        return 'Plan een vrijblijvend kennismakingsgesprek van 30 minuten, online of op locatie. Vertel over jullie plannen, ik vertel hoe ik kan helpen.';
    }

    protected function shareImage(): ?string
    {
        return 'romy.jpg';
    }

    protected function inMenu(): bool
    {
        return false;
    }

    protected function stroken(): array
    {
        return [
            ['hero-kennismaken'],
            ['contact'],
        ];
    }
}
