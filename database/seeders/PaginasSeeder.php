<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Zet elke pagina van de site in het CMS: eerst die uit het menu in zijn
 * volgorde, dan de rest. Het archief en het artikel staan direct na /blog,
 * want ze staan eronder (#20). Een pagina die er al staat, blijft zoals ze
 * is; alleen een lege pagina krijgt haar stroken alsnog.
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
            BlogpaginaSeeder::class,
            BlogarchiefpaginaSeeder::class,
            BlogartikelSeeder::class,
            VeelgesteldeVragenpaginaSeeder::class,
            ChecklistpaginaSeeder::class,
            KennismakenpaginaSeeder::class,
            PrivacyverklaringSeeder::class,
            CookiebeleidSeeder::class,
            AlgemeneVoorwaardenSeeder::class,
        ]);
    }
}
