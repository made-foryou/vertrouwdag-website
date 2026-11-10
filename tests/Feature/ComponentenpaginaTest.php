<?php

test('the component page does not exist outside the local environment', function () {
    $response = $this->get('/componenten');

    $response->assertNotFound();
});

test('the component page renders every shared component together', function () {
    $this->withoutVite();

    $view = $this->view('pages.componenten');

    $view->assertSee('<div class="folders" data-deck data-reveal-group>', false)
        ->assertSee('class="marquee"', false)
        ->assertSee('class="cta-split"', false);
});
