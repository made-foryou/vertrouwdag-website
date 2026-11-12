<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Zet elke pagina die al naar het CMS verhuisde in het CMS, in de volgorde
 * van het menu. Een pagina die er al staat, blijft zoals ze is.
 */
class PaginasSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            HomepaginaSeeder::class,
            DienstenpaginaSeeder::class,
            WerkwijzepaginaSeeder::class,
            OverMijPaginaSeeder::class,
        ]);
    }
}
