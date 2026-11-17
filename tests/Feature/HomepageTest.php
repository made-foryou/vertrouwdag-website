<?php

use Database\Seeders\HomepaginaSeeder;
use Database\Seeders\PaginasSeeder;
use Dom\Element;
use Dom\HTMLDocument;
use Dom\Text;
use Made\CmsPackage\Content\Actions\CreatePage;
use Made\CmsPackage\Content\Actions\PublishPage;
use Made\CmsPackage\Content\Actions\UpdateStrip;
use Made\CmsPackage\Content\Data\NewPage;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\Page;

beforeEach(function () {
    $this->withoutVite();
});

/**
 * De gepubliceerde homepage uit het CMS, zoals de seeder haar zet.
 */
function geseedeHomepagina(): Page
{
    return Page::query()->published()->atPath(PagePath::fromString('/'))->sole();
}

/**
 * Elke tekst in het element die niet in een veld van het CMS staat en ook
 * niet in een van de uitzonderingen. Een redacteur kan zo'n tekst niet
 * wijzigen in de bewerkschil.
 *
 * @return list<string>
 */
function tekstenBuitenVelden(Element $element, string $uitzonderingen): array
{
    $teksten = [];

    foreach ($element->childNodes as $node) {
        if ($node instanceof Element && ! $node->matches("[data-cms-field], script, {$uitzonderingen}")) {
            $teksten = [...$teksten, ...tekstenBuitenVelden($node, $uitzonderingen)];
        } elseif ($node instanceof Text && trim($node->textContent) !== '') {
            $teksten[] = trim($node->textContent);
        }
    }

    return $teksten;
}

test('the homepage comes from the cms with every strip in the order of the design', function () {
    $this->seed(HomepaginaSeeder::class);

    $response = $this->get('/');

    $response->assertOk()->assertViewIs('page');

    $typen = array_column(geseedeHomepagina()->strips, 'type', 'id');
    $stroken = HTMLDocument::createFromString($response->getContent(), LIBXML_NOERROR)->querySelectorAll('main > *');

    expect(array_map(fn (string $id): ?string => $typen[$id] ?? null, attributenVan($stroken, 'data-cms-strip')))->toBe([
        'hero',
        'lopende-band',
        'herkenning',
        'twee-kolommen',
        'diensten',
        'stappen',
        'over-mij-intro',
        'reviews',
        'leveranciers',
        'checklist-aanmelding',
        'cta',
    ]);
});

/**
 * De uitzonderingen staan als lijst in #16. Wat er niet onder valt, moet een redacteur kunnen wijzigen:
 * - [aria-hidden]: decoratie, zoals de checklist-visual en de hartjes. De lopende band is ook aria-hidden, maar zijn
 *   teksten zijn een lijst die het frame herlaadt;
 * - de knop naar de kennismaking: uit de config, net als in de header. In de hero is hij een veld met een relatief pad,
 *   dus valt hij niet onder de uitzondering;
 * - .price: de prijs uit de strook pakketten op /diensten (#17) met "vanaf", "incl. btw" en "excl. btw";
 * - .step__nr: het nummer van een stap volgt de volgorde;
 * - de velden, de toestemming en de knop van het checklistformulier, dat ook op /checklist staat.
 */
test('every text on the homepage is a field of the cms, except the listed exceptions', function () {
    $this->seed(HomepaginaSeeder::class);

    $main = documentVoor('/')->querySelector('main');

    $uitzonderingen = implode(', ', [
        '[aria-hidden="true"]',
        'a[href="'.url(config('vertrouwdag.cta.pad')).'"]',
        '.price',
        '.step__nr',
        'form .field',
        'form .check',
        'form [type="submit"]',
    ]);

    expect(tekstenBuitenVelden($main, $uitzonderingen))->toBe([]);
});

test('the homepage shows the three services with their price from the packages on the services page', function () {
    $this->seed(PaginasSeeder::class);

    $document = documentVoor('/');

    $diensten = $document->querySelectorAll('.folders a.folder');

    expect(tekstenVan($document->querySelectorAll('.folders a.folder h3')))->toBe(['De Dag in handen', 'Hand in hand', 'Helemaal in handen'])
        ->and(tekstenVan($document->querySelectorAll('.folders [data-prijs-incl]')))->toBe(['€1.028,50', '€2.238,50', '€4.235'])
        ->and(tekstenVan($document->querySelectorAll('.folders [data-prijs]')))->toBe(['€850', '€1.850', '€3.500'])
        ->and(attributenVan($diensten, 'href'))->toBe([
            url('/diensten').'#dagcoordinatie',
            url('/diensten').'#partial-planning',
            url('/diensten').'#full-service',
        ]);
});

test('the homepage shows its services without a price while the services page is not in the cms', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    expect($document->querySelectorAll('.folders a.folder'))->toHaveCount(3)
        ->and($document->querySelector('.folders .price'))->toBeNull();
});

test('the homepage shows two placeholder reviews until the real ones arrive', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    $reviews = $document->querySelectorAll('#reviews [data-cms-items="reviews"] > figure.review');

    expect($reviews)->toHaveCount(2)
        ->and(tekstenVan($document->querySelectorAll('#reviews blockquote')))->each->toStartWith('"[Tekst review');
});

test('the homepage describes the business as structured data from the config', function () {
    config([
        'vertrouwdag.email' => 'test@voorbeeld.nl',
        'vertrouwdag.whatsapp.nummer' => '31600000000',
        'vertrouwdag.instagram' => 'https://www.instagram.com/test/',
    ]);
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');
    $scripts = $document->querySelectorAll('script[type="application/ld+json"]');

    expect($scripts)->toHaveCount(1);

    expect(json_decode($scripts->item(0)->textContent, true, flags: JSON_THROW_ON_ERROR))
        ->toMatchArray([
            '@context' => 'https://schema.org',
            '@type' => 'ProfessionalService',
            'name' => 'vertrouwdag',
            'description' => 'Weddingplanner in Zuid-Holland',
            'url' => url('/'),
            'email' => 'test@voorbeeld.nl',
            'telephone' => '+31600000000',
            'areaServed' => 'Zuid-Holland',
            'sameAs' => ['https://www.instagram.com/test/'],
        ])
        ->toHaveKey('image');
});

test('a page of the cms other than the homepage does not describe the business', function () {
    Page::factory()->published()->at('/testpagina')->create();

    $document = documentVoor('/testpagina');

    expect($document->querySelectorAll('script[type="application/ld+json"]'))->toHaveCount(0);
});

test('suppliers can mail about working together or send a WhatsApp message', function () {
    config([
        'vertrouwdag.email' => 'test@voorbeeld.nl',
        'vertrouwdag.whatsapp.nummer' => '31600000000',
        'vertrouwdag.whatsapp.tekst' => 'Hoi!',
    ]);
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');
    $strook = $document->querySelector('#samenwerken');

    expect($strook?->querySelector('a.btn')?->getAttribute('href'))->toBe('mailto:test@voorbeeld.nl?subject=Samenwerking%20met%20vertrouwdag')
        ->and($strook?->querySelector('a.founding__alt')?->getAttribute('href'))->toBe('https://wa.me/31600000000?text=Hoi%21');
});

test('the checklist form on the homepage does not ask for the wedding date', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    $formulier = $document->querySelector('#checklist form#checklist-form');

    expect(attributenVan($formulier?->querySelectorAll('input'), 'name'))->toBe(['voornaam', 'email', 'avg'])
        ->and(platteTekst($formulier?->querySelector('h2')))->toBe('Wat regel je wanneer?');
});

test('the homepage links its buttons to the right pages', function () {
    $this->seed(HomepaginaSeeder::class);

    $document = documentVoor('/');

    expect(attributenVan($document->querySelectorAll('main > :first-child a.btn'), 'href'))->toBe(['/kennismaken', '/diensten'])
        ->and($document->querySelector('.folders ~ * a.btn')?->getAttribute('href'))->toBe('/diensten')
        ->and($document->querySelector('.steps')?->parentElement?->querySelector('a.btn')?->getAttribute('href'))->toBe('/werkwijze')
        ->and($document->querySelector('.about a.btn')?->getAttribute('href'))->toBe('/over-mij');
});

test('seeding the homepage again keeps what an editor changed', function () {
    $this->seed(HomepaginaSeeder::class);
    $homepagina = geseedeHomepagina();
    UpdateStrip::run($homepagina, $homepagina->strips[0]['id'], ['kop' => 'Onze dag in *goede* handen'], chooseLevels: false);
    PublishPage::run($homepagina);

    $this->seed(HomepaginaSeeder::class);

    expect(Page::query()->count())->toBe(1)
        ->and(geseedeHomepagina()->strips[0]['values']['kop'])->toBe('Onze dag in *goede* handen');
});

test('the seeded homepage comes first in the tree and stays out of the menu', function () {
    CreatePage::run(new NewPage(title: 'Welkom', slug: null, parentId: null));

    $this->seed(HomepaginaSeeder::class);

    expect(Page::query()->roots()->inMenuOrder()->pluck('title')->all())->toBe(['Home', 'Welkom'])
        ->and(geseedeHomepagina()->in_menu)->toBeFalse();
});

test('the seeder fills the photos of the homepage with those of the design, a column with that of its place and a service with that of its service', function () {
    $this->seed(HomepaginaSeeder::class);

    $stroken = collect(geseedeHomepagina()->strips)->keyBy('type');
    $namen = fn (array $ids): array => array_map(fn (int $id): ?string => Media::query()->whereKey($id)->value('name'), $ids);

    expect($namen([$stroken['hero']['values']['foto_boog'], $stroken['hero']['values']['foto_polaroid'], $stroken['cta']['values']['foto']]))->toBe(['romy-portret', 'ringen-boeket', 'romy-gesprek'])
        ->and($namen(array_column($stroken['twee-kolommen']['values']['kolommen'], 'foto')))->toBe(['paar-landschap', 'feesttafel'])
        ->and($namen(array_column($stroken['diensten']['values']['diensten'], 'foto')))->toBe(['schoenen-boeket', 'hand-in-hand-paar', 'sleep-jurk']);
});
