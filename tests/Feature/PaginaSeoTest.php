<?php

use Database\Seeders\PaginasSeeder;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Router;
use Made\CmsPackage\Content\Models\Page;

/**
 * Per pagina: het pad, de titel en de omschrijving zoals ze voor de overstap naar het CMS waren (#23, criterium 1),
 * en de naam van de deelfoto, of null voor de standaardafbeelding van de site. Titel en omschrijving komen nu uit
 * het CMS; de seeders zetten ze er zoals de datamigratie dat op staging doet. /diensten heeft geen eigen
 * omschrijving: de site schrijft hem met de actuele prijzen.
 *
 * @var array<string, array{string, string, string, ?string}>
 */
$paginas = [
    'home' => ['/', 'Jullie dag in vertrouwde handen | vertrouwdag', 'Weddingplanner in Zuid-Holland. De Dag in handen, Hand in hand en Helemaal in handen: van dagcoördinatie tot full service, zodat jullie en jullie familie op de dag zelf alleen maar hoeven te genieten.', null],
    'diensten' => ['/diensten', 'Diensten en prijzen | vertrouwdag weddingplanner', 'De Dag in handen (dagcoördinatie) vanaf €1.028,50, Hand in hand (gedeeltelijke planning) vanaf €2.238,50 en Helemaal in handen (full service) vanaf €4.235, inclusief btw. Vaste vanaf-prijzen, zodat jullie vooraf weten waar jullie aan toe zijn.', 'ceremonie-buiten'],
    'werkwijze' => ['/werkwijze', 'Werkwijze: zo plan ik jullie bruiloft | vertrouwdag', 'Van het eerste kennismakingsgesprek tot de dag na de bruiloft: zo werk ik als weddingplanner in Zuid-Holland, stap voor stap.', null],
    'over mij' => ['/over-mij', 'Over Romy | Persoonlijke weddingplanner in Zuid-Holland', 'Ik ben Romy, de weddingplanner achter vertrouwdag. Punctueel, met oog voor detail en vanuit het hart, zodat jullie op jullie trouwdag alleen maar hoeven te genieten.', 'romy'],
    'blog' => ['/blog', 'Blog: rustig je bruiloft plannen | vertrouwdag', 'Praktische tips voor het plannen van jullie bruiloft, trouwlocaties in Zuid-Holland en een kijkje achter de schermen bij weddingplanner Romy.', null],
    'blogarchief' => ['/blog/archief', 'Blogarchief: alle artikelen | vertrouwdag', 'Alle blogs van weddingplanner Romy op een rij: tips voor het plannen van jullie bruiloft, locaties in Zuid-Holland en een kijkje achter de schermen.', null],
    'blogartikel' => ['/blog/wanneer-weddingplanner-inschakelen', 'Wanneer schakel je een weddingplanner in? | vertrouwdag', 'Wanneer schakel je een weddingplanner in? Voor full service het liefst 12 tot 18 maanden van tevoren. Waarom een vroeg begin juist rust oplevert.', null],
    'veelgestelde vragen' => ['/veelgestelde-vragen', 'Veelgestelde vragen over een weddingplanner | vertrouwdag', 'Wat kost een weddingplanner, wanneer schakel je er een in en wat als het regent? Antwoorden op de vragen die bruidsparen mij het vaakst stellen.', 'hand-in-hand'],
    'checklist' => ['/checklist', 'Gratis trouwplanning-checklist | vertrouwdag', 'Wat regel je wanneer? Download de gratis trouwplanning-checklist van weddingplanner Romy: maand voor maand, van twaalf maanden ervoor tot de week zelf.', 'ringen-pioenen'],
    'kennismaken' => ['/kennismaken', 'Plan een kennismaking | vertrouwdag weddingplanner', 'Plan een vrijblijvend kennismakingsgesprek van 30 minuten, online of op locatie. Vertel over jullie plannen, ik vertel hoe ik kan helpen.', 'romy'],
    'privacyverklaring' => ['/privacyverklaring', 'Privacyverklaring | vertrouwdag', 'Hoe vertrouwdag omgaat met jullie persoonsgegevens.', null],
    'cookiebeleid' => ['/cookiebeleid', 'Cookiebeleid | vertrouwdag', 'Welke cookies vertrouwdag.nl gebruikt, waarvoor, en hoe jullie je keuze aanpassen.', null],
    'algemene voorwaarden' => ['/algemene-voorwaarden', 'Algemene voorwaarden | vertrouwdag', 'De algemene voorwaarden van vertrouwdag weddingplanning.', null],
];

beforeEach(function () {
    $this->withoutVite();
    config(['vertrouwdag.btw_percentage' => 21]);
});

test('every page keeps its title and description, with its canonical address and open graph tags', function (string $pad, string $titel, string $omschrijving, ?string $deelfoto) {
    $this->seed(PaginasSeeder::class);

    $document = documentVoor($pad);
    $canonical = rtrim(url('/'), '/').$pad;
    $afbeelding = $document->querySelector('meta[property="og:image"]')?->getAttribute('content');

    expect(tekstenVan($document->querySelectorAll('title')))->toBe([$titel])
        ->and(attributenVan($document->querySelectorAll('meta[name="description"]'), 'content'))->toBe([$omschrijving])
        ->and(attributenVan($document->querySelectorAll('link[rel="canonical"]'), 'href'))->toBe([$canonical])
        ->and($document->querySelector('meta[name="robots"]'))->toBeNull();

    expect($document->querySelector('meta[property="og:type"]')?->getAttribute('content'))->toBe('website')
        ->and($document->querySelector('meta[property="og:title"]')?->getAttribute('content'))->toBe($titel)
        ->and(attributenVan($document->querySelectorAll('meta[property="og:description"]'), 'content'))->toBe([$omschrijving])
        ->and($document->querySelector('meta[property="og:url"]')?->getAttribute('content'))->toBe($canonical);

    expect($afbeelding)->toBeUrl()
        ->and(basename(parse_url($afbeelding, PHP_URL_PATH)))->toStartWith($deelfoto === null ? 'deelafbeelding.' : $deelfoto.'.');
})->with($paginas);

test('no two pages share a title or a description', function () use ($paginas) {
    $this->seed(PaginasSeeder::class);
    $titels = [];
    $omschrijvingen = [];

    foreach ($paginas as [$pad]) {
        $document = documentVoor($pad);
        $titels[] = trim($document->querySelector('title')?->textContent ?? '');
        $omschrijvingen[] = $document->querySelector('meta[name="description"]')?->getAttribute('content');
    }

    expect($titels)->toBe(array_unique($titels))
        ->and($omschrijvingen)->toBe(array_unique($omschrijvingen));
});

test('the pages above cover every page of the site, so a new page cannot skip these checks', function () use ($paginas) {
    $this->seed(PaginasSeeder::class);

    /**
     * Adressen zonder vaste pagina voor bezoekers: de health check, de stijlgids (alleen lokaal, noindex), de link
     * naar de checklist-pdf (#25, noindex) en de catch-all van het CMS. Die laatste serveert de pagina's uit het CMS.
     * Die tellen hieronder mee zoals de seeders ze zetten.
     */
    $geenPagina = ['up', 'componenten', 'checklist/openen/{signup}', '{path?}'];
    $adressen = [];

    foreach (Router::getRoutes()->getRoutes() as $route) {
        /** @var Route $route */
        if (! in_array('GET', $route->methods(), true) || in_array($route->uri(), $geenPagina, true)) {
            continue;
        }

        // Het beheer van het CMS, met de inlogpagina, de schermen en de launcher.
        if (str_starts_with($route->uri(), 'made-cms')) {
            continue;
        }

        // The CMS serves robots.txt and the sitemap for search engines; they are files, not pages for visitors.
        if (in_array($route->uri(), ['robots.txt', 'sitemap.xml'], true)) {
            continue;
        }

        $adressen[] = '/'.ltrim($route->uri(), '/');
    }

    // Een pagina uit het CMS op het adres van een vaste route is voor bezoekers de vaste pagina.
    foreach (Page::query()->published()->with('route')->get() as $page) {
        if (! in_array($page->route->path, $adressen, true)) {
            $adressen[] = $page->route->path;
        }
    }

    $gedekt = array_column($paginas, 0);
    sort($adressen);
    sort($gedekt);

    expect($adressen)->toBe($gedekt);
});

test('a new article without meta of its own is named after its page and described by its intro', function () {
    artikelOnderBlog('trouwen-in-de-winter', [
        'titel' => 'Trouwen in de *winter*',
        'intro' => 'Kort licht, warme kleuren en een feest binnen.',
    ])->update(['title' => 'Trouwen in de winter']);

    $document = documentVoor('/blog/trouwen-in-de-winter');

    expect(tekstenVan($document->querySelectorAll('title')))->toBe(['Trouwen in de winter · vertrouwdag'])
        ->and($document->querySelector('meta[name="description"]')?->getAttribute('content'))->toBe('Kort licht, warme kleuren en een feest binnen.')
        ->and($document->querySelector('meta[property="og:image"]')?->getAttribute('content'))->toBe(url('/deelafbeelding.jpg'));
});
