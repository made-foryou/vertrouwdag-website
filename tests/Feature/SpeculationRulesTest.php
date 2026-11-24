<?php

use Database\Seeders\HomepaginaSeeder;
use Dom\HTMLDocument;
use Illuminate\Testing\TestResponse;
use Made\CmsPackage\Access\Enums\Role;
use Made\CmsPackage\Access\Models\Editor;

beforeEach(function () {
    $this->withoutVite();
    $this->seed(HomepaginaSeeder::class);
});

/**
 * The speculation rules in the head of the page, decoded, or null when the page has none.
 *
 * @return array<string, mixed>|null
 */
function speculationRules(TestResponse $response): ?array
{
    $response->assertOk();

    $script = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR)->querySelector('head script[type="speculationrules"]');

    return $script === null ? null : json_decode($script->textContent, true, flags: JSON_THROW_ON_ERROR);
}

test('a visitor gets an internal link prerendered as soon as they rest on it', function () {
    $rules = speculationRules($this->get('/'));

    expect($rules['prerender'] ?? null)->toHaveCount(1)
        ->and($rules['prerender'][0]['source'])->toBe('document')
        ->and($rules['prerender'][0]['eagerness'])->toBe('moderate')
        ->and($rules['prerender'][0]['where']['and'][0])->toBe(['href_matches' => '/*']);
});

test('the cms, an edit or preview address, the checklist confirmation and a new tab are never prerendered', function () {
    $rules = speculationRules($this->get('/'));

    expect($rules['prerender'][0]['where']['and'])->toContain(
        ['not' => ['href_matches' => '/made-cms']],
        ['not' => ['href_matches' => '/made-cms/*']],
        ['not' => ['href_matches' => '/*\\?*made-cms=*']],
        ['not' => ['href_matches' => '/checklist/openen/*']],
        ['not' => ['selector_matches' => '[target=_blank], [download], [data-no-prerender]']],
    );
});

test('the edit frame of the cms loads nothing ahead', function () {
    $editor = Editor::factory()->withRoles(Role::Admin)->create();

    expect(speculationRules($this->actingAs($editor, 'made-cms')->get('/?made-cms=edit')))->toBeNull();
});
