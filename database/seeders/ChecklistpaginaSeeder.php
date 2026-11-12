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
