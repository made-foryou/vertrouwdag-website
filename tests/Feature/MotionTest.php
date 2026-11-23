<?php

use Database\Seeders\HomepaginaSeeder;
use Dom\Element;
use Dom\HTMLDocument;
use Illuminate\Testing\TestResponse;
use Made\CmsPackage\Access\Enums\Role;
use Made\CmsPackage\Access\Models\Editor;

beforeEach(function () {
    $this->withoutVite();
    $this->seed(HomepaginaSeeder::class);
});

/**
 * The inline script in the head that puts the classes js and motion on <html> before the first paint.
 */
function motionScript(TestResponse $response): string
{
    $response->assertOk();

    $scripts = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR)->querySelectorAll('head script:not([src]):not([type])');

    $script = collect(iterator_to_array($scripts))
        ->first(fn (Element $script): bool => str_contains($script->textContent, "classList.add('js')"));

    return $script?->textContent ?? '';
}

test('a visitor gets the motion class unless they prefer reduced motion', function () {
    $script = motionScript($this->get('/'));

    expect($script)->toContain("classList.add('js')")
        ->toContain("'motion'")
        ->toContain('prefers-reduced-motion: reduce');
});

test('the edit mode of the cms gets no motion, so every strip is there at once', function () {
    $editor = Editor::factory()->withRoles(Role::Admin)->create();

    $script = motionScript($this->actingAs($editor, 'made-cms')->get('/?made-cms=edit'));

    expect($script)->toContain("classList.add('js')")
        ->not->toContain('motion');
});

test('the preview of the cms moves like the site does for a visitor', function () {
    $editor = Editor::factory()->withRoles(Role::Admin)->create();

    $script = motionScript($this->actingAs($editor, 'made-cms')->get('/?made-cms=preview'));

    expect($script)->toContain("'motion'");
});

test('a visitor who adds the edit parameter to the address still gets motion', function () {
    $script = motionScript($this->get('/?made-cms=edit'));

    expect($script)->toContain("'motion'");
});
