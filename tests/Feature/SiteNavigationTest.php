<?php

use Database\Seeders\HomepaginaSeeder;
use Database\Seeders\PaginasSeeder;
use Dom\HTMLDocument;
use Dom\Node;
use Made\CmsPackage\Content\Actions\CreatePage;
use Made\CmsPackage\Content\Actions\DeletePage;
use Made\CmsPackage\Content\Actions\MovePage;
use Made\CmsPackage\Content\Actions\UpdatePage;
use Made\CmsPackage\Content\Data\NewPage;
use Made\CmsPackage\Content\Data\PageChanges;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Models\Page;

/**
 * De labels van het hoofdmenu, van links naar rechts, zonder de knop.
 *
 * @return list<string>
 */
function menulabels(HTMLDocument $document): array
{
    return tekstenVan($document->querySelectorAll('#site-nav a:not(.btn)'));
}

/**
 * De adressen van de links in het hoofdmenu die als huidige pagina
 * gemarkeerd zijn.
 *
 * @return list<string>
 */
function actieveMenulinks(HTMLDocument $document): array
{
    return attributenVan($document->querySelectorAll('#site-nav [aria-current="page"]'), 'href');
}

/**
 * Zet een pagina in of uit het menu, zoals een redacteur dat met de
 * schakelaar in het pagina's-scherm doet.
 */
function zetInMenu(string $adres, bool $inMenu): void
{
    $pagina = Page::query()->atPath(PagePath::fromString($adres))->sole();

    UpdatePage::run($pagina, new PageChanges(title: $pagina->title, slug: $pagina->slug, parentId: $pagina->parent_id, inMenu: $inMenu));
}

beforeEach(function () {
    $this->withoutVite();
});

test('every page shows the main menu with the right active link', function (string $adres, ?string $actiefPad) {
    $this->seed(PaginasSeeder::class);

    $document = documentVoor($adres);

    $links = $document->querySelectorAll('#site-nav a:not(.btn)');

    expect(attributenVan($links, 'href'))->toBe([url('/diensten'), url('/werkwijze'), url('/over-mij'), url('/blog'), url('/veelgestelde-vragen')])
        ->and(tekstenVan($links))->toBe(['Diensten', 'Werkwijze', 'Over mij', 'Blog', 'Veelgestelde vragen']);

    $current = $document->querySelectorAll('#site-nav [aria-current="page"]');

    if ($actiefPad === null) {
        expect($current)->toHaveCount(0);
    } else {
        expect($current)->toHaveCount(1)
            ->and($current->item(0)->getAttribute('href'))->toBe(url($actiefPad));
    }

    expect($document->querySelectorAll('header [aria-current]')->length)->toBeLessThanOrEqual(1);
})->with([
    'home' => ['/', null],
    'diensten' => ['/diensten', '/diensten'],
    'werkwijze' => ['/werkwijze', '/werkwijze'],
    'over mij' => ['/over-mij', '/over-mij'],
    'blog' => ['/blog', '/blog'],
    'blogarchief' => ['/blog/archief', '/blog'],
    'blogartikel' => ['/blog/wanneer-weddingplanner-inschakelen', '/blog'],
    'veelgestelde vragen' => ['/veelgestelde-vragen', '/veelgestelde-vragen'],
    'checklist' => ['/checklist', null],
    'kennismaken' => ['/kennismaken', null],
    'privacyverklaring' => ['/privacyverklaring', null],
    'cookiebeleid' => ['/cookiebeleid', null],
    'algemene voorwaarden' => ['/algemene-voorwaarden', null],
]);

test('a menu link is not active on another page that starts with the same letters', function () {
    Page::factory()->published()->at('/blog')->create(['title' => 'Blog']);
    Page::factory()->published()->inMenu(false)->at('/blogger')->create(['title' => 'Blogger']);

    $document = documentVoor('/blogger');

    expect(menulabels($document))->toBe(['Blog'])
        ->and(actieveMenulinks($document))->toBe([]);
});

test('a page an editor takes out of the menu disappears from it', function () {
    $this->seed(PaginasSeeder::class);

    zetInMenu('/werkwijze', false);

    expect(menulabels(documentVoor('/')))->toBe(['Diensten', 'Over mij', 'Blog', 'Veelgestelde vragen']);
});

test('the menu follows the order of the pages in the cms', function () {
    $this->seed(PaginasSeeder::class);

    MovePage::run(Page::query()->atPath(PagePath::fromString('/veelgestelde-vragen'))->sole(), parentId: null, position: 1);

    expect(menulabels(documentVoor('/')))->toBe(['Veelgestelde vragen', 'Diensten', 'Werkwijze', 'Over mij', 'Blog']);
});

test('a draft in the menu stays out of the menu until it is published', function () {
    $this->seed(PaginasSeeder::class);

    CreatePage::run(new NewPage(title: 'Trouwlocaties', slug: null, parentId: null, inMenu: true));

    expect(menulabels(documentVoor('/')))->toBe(['Diensten', 'Werkwijze', 'Over mij', 'Blog', 'Veelgestelde vragen']);
});

test('a deleted page disappears from the menu', function () {
    $this->seed(PaginasSeeder::class);

    DeletePage::run(Page::query()->atPath(PagePath::fromString('/werkwijze'))->sole());

    expect(menulabels(documentVoor('/')))->toBe(['Diensten', 'Over mij', 'Blog', 'Veelgestelde vragen']);
});

test('a published page with changes in its draft stays in the menu', function () {
    $this->seed(PaginasSeeder::class);

    wijzigStrook('/werkwijze', 'cta', fn (array $waarden): array => ['kop' => 'Nog niet *af*'], publiceren: false);

    expect(menulabels(documentVoor('/')))->toBe(['Diensten', 'Werkwijze', 'Over mij', 'Blog', 'Veelgestelde vragen']);
});

test('a page under a menu item stays out of the menu and keeps that menu item active', function () {
    $this->seed(PaginasSeeder::class);
    $diensten = Page::query()->atPath(PagePath::fromString('/diensten'))->sole();
    Page::factory()->published()->under($diensten)->at('/diensten/bruiloft-in-het-buitenland')->create(['title' => 'Bruiloft in het buitenland']);

    $document = documentVoor('/diensten/bruiloft-in-het-buitenland');

    expect(menulabels($document))->toBe(['Diensten', 'Werkwijze', 'Over mij', 'Blog', 'Veelgestelde vragen'])
        ->and(actieveMenulinks($document))->toBe([url('/diensten')]);
});

test('the homepage in the menu is only active on the homepage itself', function (string $adres, string $actiefPad) {
    $this->seed(PaginasSeeder::class);
    zetInMenu('/', true);

    $document = documentVoor($adres);

    expect(menulabels($document))->toBe(['Home', 'Diensten', 'Werkwijze', 'Over mij', 'Blog', 'Veelgestelde vragen'])
        ->and(actieveMenulinks($document))->toBe([url($actiefPad)]);
})->with([
    'on the homepage' => ['/', '/'],
    'on another page' => ['/diensten', '/diensten'],
]);

test('the header links the logo home and the call to action to the configured route', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');
    $cta = config('vertrouwdag.cta');

    expect($document->querySelector('.site-header__logo')?->getAttribute('href'))->toBe(url('/'));

    foreach (['#site-nav a.btn', '.site-header__cta'] as $selector) {
        $button = $document->querySelector($selector);

        expect($button?->getAttribute('href'))->toBe(url($cta['pad']))
            ->and(trim($button->textContent))->toBe($cta['label']);
    }
});

test('the header has a closed menu toggle for the mobile menu', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    $toggle = $document->querySelector('button.nav-toggle');

    expect($toggle)->not->toBeNull()
        ->and($toggle->getAttribute('type'))->toBe('button')
        ->and($toggle->getAttribute('aria-controls'))->toBe('site-nav')
        ->and($toggle->getAttribute('aria-expanded'))->toBe('false')
        ->and($toggle->getAttribute('aria-label'))->not->toBeEmpty()
        ->and($document->getElementById('site-nav'))->not->toBeNull();
});

test('the menu follows its toggle so the tab key continues into the opened menu', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    $toggle = $document->querySelector('button.nav-toggle');
    $menu = $document->getElementById('site-nav');

    expect($toggle->compareDocumentPosition($menu) & Node::DOCUMENT_POSITION_FOLLOWING)->toBeGreaterThan(0);
});

test('the footer and the floating button take the contact details from the config', function () {
    config([
        'vertrouwdag.email' => 'test@voorbeeld.nl',
        'vertrouwdag.email_onderwerp' => 'Vraag & antwoord',
        'vertrouwdag.whatsapp.nummer' => '31600000000',
        'vertrouwdag.whatsapp.tekst' => 'Hoi! Een vraag?',
        'vertrouwdag.instagram' => 'https://www.instagram.com/test/',
    ]);
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');
    $whatsappUrl = 'https://wa.me/31600000000?text=Hoi%21%20Een%20vraag%3F';

    $mail = $document->querySelector('.site-footer a[href^="mailto:"]');

    expect($mail?->getAttribute('href'))->toBe('mailto:test@voorbeeld.nl?subject=Vraag%20%26%20antwoord')
        ->and(trim($mail->textContent))->toBe('test@voorbeeld.nl');

    $whatsappLinks = $document->querySelectorAll('.site-footer a[href^="https://wa.me/"], a.wa');

    expect($whatsappLinks)->toHaveCount(2)
        ->and(attributenVan($whatsappLinks, 'href'))->each->toBe($whatsappUrl)
        ->and(attributenVan($whatsappLinks, 'target'))->each->toBe('_blank')
        ->and(attributenVan($whatsappLinks, 'rel'))->each->toBe('noopener');

    $instagram = $document->querySelector('.site-footer a[href^="https://www.instagram.com/"]');

    expect($instagram?->getAttribute('href'))->toBe('https://www.instagram.com/test/')
        ->and($instagram->getAttribute('target'))->toBe('_blank')
        ->and($instagram->getAttribute('rel'))->toBe('noopener');
});

test('the footer links to the configured pages and legal pages and opens the cookie settings', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');
    $footer = config('vertrouwdag.footer');

    $pageLinks = $document->querySelectorAll('.site-footer nav a');

    expect(attributenVan($pageLinks, 'href'))->toBe(array_map(fn (string $pad): string => url($pad), array_keys($footer['paginas'])))
        ->and(tekstenVan($pageLinks))->toBe(array_values($footer['paginas']));

    foreach ($footer['juridisch'] as $pad => $label) {
        $link = $document->querySelector('.site-footer a[href="'.url($pad).'"]');

        expect($link)->not->toBeNull()
            ->and(trim($link->textContent))->toBe($label);
    }

    $cookieButton = $document->querySelector('.site-footer button[data-cookie-open]');

    expect($cookieButton)->not->toBeNull()
        ->and($cookieButton->getAttribute('type'))->toBe('button');
});

test('the footer shows no chamber of commerce line while the number is empty', function () {
    config(['vertrouwdag.kvk' => null]);
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    expect($document->querySelector('.site-footer__bottom')?->textContent)->not->toContain('KvK');
});

test('the footer shows the chamber of commerce number once it is filled in', function () {
    config(['vertrouwdag.kvk' => '12345678']);
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    expect($document->querySelector('.site-footer__bottom')?->textContent)->toContain('KvK 12345678');
});

test('the footer shows the current year', function () {
    $this->travelTo(now()->setDate(2031, 6, 1));
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    expect(preg_replace('/\s+/', ' ', $document->querySelector('.site-footer__bottom')?->textContent))
        ->toContain('© 2031 vertrouwdag');
});
