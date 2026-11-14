<?php

use Dom\HTMLDocument;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Router;
use Illuminate\Support\Str;
use Made\CmsPackage\Access\Enums\Role;
use Made\CmsPackage\Access\Models\Editor;
use Made\CmsPackage\Content\Actions\AddStrip;
use Made\CmsPackage\Content\Actions\CreatePage;
use Made\CmsPackage\Content\Actions\PublishPage;
use Made\CmsPackage\Content\Actions\UpdateStrip;
use Made\CmsPackage\Content\Data\NewPage;
use Made\CmsPackage\Content\Models\Page;

/**
 * Een strook cta zoals het CMS hem opslaat, met alleen een kop.
 *
 * @return array{id: string, type: string, visible: bool, values: array<string, mixed>}
 */
function ctaStrook(string $kop): array
{
    return ['id' => (string) Str::uuid(), 'type' => 'cta', 'visible' => true, 'values' => ['kop' => $kop]];
}

beforeEach(function () {
    $this->withoutVite();
});

test('the pages of the cms are the last route, so no route of the site is caught by them', function () {
    $routes = collect(Router::getRoutes()->getRoutes())
        ->filter(fn (Route $route) => in_array('GET', $route->methods(), true))
        ->values();

    expect($routes->last()->uri())->toBe('{path?}');
});

test('a published page of the cms shows its strips in the layout of the site', function () {
    Page::factory()->published()->at('/testpagina')->withStrips([ctaStrook('Zullen we *kennismaken?*')])->create(['title' => 'Testpagina']);

    $document = documentVoor('/testpagina');

    expect(tekstenVan($document->querySelectorAll('title')))->toBe(['Testpagina | vertrouwdag'])
        ->and($document->querySelector('meta[name="description"]')?->getAttribute('content'))->toBe('Weddingplanner in Zuid-Holland. Warm en persoonlijk, en tegelijk strak georganiseerd.')
        ->and($document->querySelector('header.site-header #site-nav'))->not->toBeNull()
        ->and($document->querySelector('footer.site-footer'))->not->toBeNull()
        ->and($document->querySelector('main [data-cms-strip] [data-cms-field="kop"] em')?->textContent)->toBe('kennismaken?');
});

test('a page of the cms at an address of the fixed list takes its title and description from that list', function () {
    $page = Page::factory()->published()->at('/werkwijze')->create(['title' => 'Een andere titel']);

    $document = HTMLDocument::createFromString((string) $this->view('page', ['page' => $page]), LIBXML_NOERROR);

    expect(tekstenVan($document->querySelectorAll('title')))->toBe(['Werkwijze: zo plan ik jullie bruiloft | vertrouwdag'])
        ->and($document->querySelector('meta[name="description"]')?->getAttribute('content'))->toBe('Van het eerste kennismakingsgesprek tot de dag na de bruiloft: zo werk ik als weddingplanner in Zuid-Holland, stap voor stap.')
        ->and($document->querySelector('meta[property="og:title"]')?->getAttribute('content'))->toBe('Werkwijze: zo plan ik jullie bruiloft · vertrouwdag');
});

test('a page made, filled and published in the cms is at its address', function () {
    $page = CreatePage::run(new NewPage(title: 'Testpagina', slug: 'testpagina', parentId: null));
    $strip = AddStrip::run($page, 'cta', 0);
    UpdateStrip::run($page, $strip->id, ['kop' => 'Tot *snel*', 'variant' => 'compact'], chooseLevels: false);

    PublishPage::run($page);

    $this->get('/testpagina')
        ->assertOk()
        ->assertSee('Tot <em>snel</em>', escape: false);
});

test('the cms opens its login page at /made-cms', function () {
    $this->get('/made-cms')->assertOk();
});

test('an admin opens the pages screen of the cms', function () {
    $editor = Editor::factory()->withRoles(Role::Admin)->create();

    $response = $this->actingAs($editor, 'made-cms')->get(route('made-cms-package.screens.pages'));

    $response->assertOk();
});

test('the edit mode of the cms shows the draft in a frame of the same origin, with the header and footer of the site', function () {
    $editor = Editor::factory()->withRoles(Role::Admin)->create();
    Page::factory()->at('/testpagina')->withDraft([ctaStrook('In *concept*')])->create();

    $response = $this->actingAs($editor, 'made-cms')->get('/testpagina?made-cms=edit');

    $response->assertOk()->assertHeader('X-Frame-Options', 'SAMEORIGIN');

    $document = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR);

    expect($document->querySelector('header.site-header'))->not->toBeNull()
        ->and($document->querySelector('footer.site-footer'))->not->toBeNull()
        ->and($document->querySelector('main [data-cms-strip] [data-cms-field="kop"] em')?->textContent)->toBe('concept');
});
