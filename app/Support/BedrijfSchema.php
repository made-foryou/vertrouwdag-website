<?php

namespace App\Support;

use Illuminate\Support\Facades\Vite;

/**
 * Het bedrijf als ProfessionalService voor zoekmachines (schema.org), op
 * de homepage (page.blade.php). Alles komt uit config/vertrouwdag.php,
 * zodat het gelijk blijft aan de contactgegevens op de site.
 */
final class BedrijfSchema
{
    /**
     * @return array{'@context': string, '@type': string, name: string, description: string, url: string, email: string, telephone: string, areaServed: string, image: string, sameAs: list<string>}
     */
    public static function uitConfig(): array
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => config('vertrouwdag.bedrijf.naam'),
            'description' => config('vertrouwdag.bedrijf.omschrijving'),
            'url' => url('/'),
            'email' => config('vertrouwdag.email'),
            'telephone' => '+'.config('vertrouwdag.whatsapp.nummer'),
            'areaServed' => config('vertrouwdag.bedrijf.werkgebied'),
            'image' => Vite::asset('resources/images/logo-groen.png'),
            'sameAs' => [config('vertrouwdag.instagram')],
        ];
    }
}
