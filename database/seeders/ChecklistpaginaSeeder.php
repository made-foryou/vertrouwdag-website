<?php

namespace Database\Seeders;

/**
 * Zet /checklist in het CMS (#18), met de stroken uit het ontwerp (#10).
 * De pagina staat niet in het menu; de footer linkt ernaar.
 */
class ChecklistpaginaSeeder extends PaginaSeeder
{
    protected function adres(): string
    {
        return '/checklist';
    }

    protected function titel(): string
    {
        return 'Checklist';
    }

    protected function metaTitle(): ?string
    {
        return 'Gratis trouwplanning-checklist | vertrouwdag';
    }

    protected function metaDescription(): ?string
    {
        return 'Wat regel je wanneer? Download de gratis trouwplanning-checklist van weddingplanner Romy: maand voor maand, van twaalf maanden ervoor tot de week zelf.';
    }

    protected function shareImage(): ?string
    {
        return 'ringen-pioenen.jpg';
    }

    protected function inMenu(): bool
    {
        return false;
    }

    protected function stroken(): array
    {
        return [
            ['hero-checklist'],
            ['checklist-bedankt'],
        ];
    }
}
