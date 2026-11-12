<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Zet elke pagina die al naar het CMS verhuisde in het CMS: eerst die uit
 * het menu in zijn volgorde, dan de rest. Een pagina die er al staat,
 * blijft zoals ze is.
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
            VeelgesteldeVragenpaginaSeeder::class,
            ChecklistpaginaSeeder::class,
            KennismakenpaginaSeeder::class,
            PrivacyverklaringSeeder::class,
            CookiebeleidSeeder::class,
            AlgemeneVoorwaardenSeeder::class,
        ]);
    }
}
