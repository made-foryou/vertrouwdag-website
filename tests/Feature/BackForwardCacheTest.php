<?php

use Database\Seeders\HomepaginaSeeder;

/**
 * "Back" returns to the same place only when the browser may keep the page in its back/forward cache (#38). A response
 * with Cache-Control: no-store rules that out, so no middleware may add it to a public page.
 */
test('a public page may use the back/forward cache, so it is never sent as no-store', function () {
    $this->withoutVite();
    $this->seed(HomepaginaSeeder::class);

    $response = $this->get('/');

    $response->assertOk();
    expect((string) $response->headers->get('Cache-Control'))->not->toContain('no-store');
});
