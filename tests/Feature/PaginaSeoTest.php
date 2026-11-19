<?php

use Database\Seeders\PaginasSeeder;
use Illuminate\Routing\Route;
use Illuminate\Support\Facades\Route as Router;
use Illuminate\Support\Facades\Vite;
use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\Page;

/**
 * Per pagina: het pad, de titel, de omschrijving, de Open Graph-titel,
 * een afwijkende Open Graph-omschrijving (null is gelijk aan de
 * omschrijving), de foto voor Open Graph en het type. De waarden komen
 * uit het ontwerp, op de geaccepteerde afwijkingen na (zie #12).
 *
 * @var array<string, array{string, string, string, string, ?string, string, string}>
 */
$paginas = [
    'home' => ['/', 'Jullie dag in vertrouwde handen | vertrouwdag', 'Weddingplanner in Zuid-Holland. De Dag in handen, Hand in hand en Helemaal in handen: van dagcoördinatie tot full service, zodat jullie en jullie familie op de dag zelf alleen maar hoeven te genieten.', 'vertrouwdag · Jullie dag in vertrouwde handen', 'Weddingplanner in Zuid-Holland. Warm en persoonlijk, en tegelijk strak georganiseerd.', 'paar-landschap.jpg', 'website'],
    'diensten' => ['/diensten', 'Diensten en prijzen | vertrouwdag weddingplanner', 'De Dag in handen (dagcoördinatie) vanaf €1.028,50, Hand in hand (gedeeltelijke planning) vanaf €2.238,50 en Helemaal in handen (full service) vanaf €4.235, inclusief btw. Vaste vanaf-prijzen, zodat jullie vooraf weten waar jullie aan toe zijn.', 'Diensten en prijzen · vertrouwdag', 'Weddingplanner in Zuid-Holland. Warm en persoonlijk, en tegelijk strak georganiseerd.', 'ceremonie-buiten.jpg', 'website'],
    'werkwijze' => ['/werkwijze', 'Werkwijze: zo plan ik jullie bruiloft | vertrouwdag', 'Van het eerste kennismakingsgesprek tot de dag na de bruiloft: zo werk ik als weddingplanner in Zuid-Holland, stap voor stap.', 'Werkwijze: zo plan ik jullie bruiloft · vertrouwdag', null, 'paar-landschap.jpg', 'website'],
    'over mij' => ['/over-mij', 'Over Romy | Persoonlijke weddingplanner in Zuid-Holland', 'Ik ben Romy, de weddingplanner achter vertrouwdag. Punctueel, met oog voor detail en vanuit het hart, zodat jullie op jullie trouwdag alleen maar hoeven te genieten.', 'Over Romy · vertrouwdag', null, 'romy.jpg', 'website'],
    'blog' => ['/blog', 'Blog: rustig je bruiloft plannen | vertrouwdag', 'Praktische tips voor het plannen van jullie bruiloft, trouwlocaties in Zuid-Holland en een kijkje achter de schermen bij weddingplanner Romy.', 'Blog: rustig je bruiloft plannen · vertrouwdag', null, 'paar-meer-herfst.jpg', 'website'],
    'blogarchief' => ['/blog/archief', 'Blogarchief: alle artikelen | vertrouwdag', 'Alle blogs van weddingplanner Romy op een rij: tips voor het plannen van jullie bruiloft, locaties in Zuid-Holland en een kijkje achter de schermen.', 'Blogarchief: alle artikelen · vertrouwdag', null, 'paar-meer-herfst.jpg', 'website'],
    'blogartikel' => ['/blog/wanneer-weddingplanner-inschakelen', 'Wanneer schakel je een weddingplanner in? | vertrouwdag', 'Wanneer schakel je een weddingplanner in? Voor full service het liefst 12 tot 18 maanden van tevoren. Waarom een vroeg begin juist rust oplevert.', 'Wanneer schakel je een weddingplanner in? · vertrouwdag', null, 'paar-landschap.jpg', 'article'],
    'veelgestelde vragen' => ['/veelgestelde-vragen', 'Veelgestelde vragen over een weddingplanner | vertrouwdag', 'Wat kost een weddingplanner, wanneer schakel je er een in en wat als het regent? Antwoorden op de vragen die bruidsparen mij het vaakst stellen.', 'Veelgestelde vragen over een weddingplanner · vertrouwdag', null, 'hand-in-hand.jpg', 'website'],
    'checklist' => ['/checklist', 'Gratis trouwplanning-checklist | vertrouwdag', 'Wat regel je wanneer? Download de gratis trouwplanning-checklist van weddingplanner Romy: maand voor maand, van twaalf maanden ervoor tot de week zelf.', 'Gratis trouwplanning-checklist · vertrouwdag', null, 'ringen-pioenen.jpg', 'website'],
    'kennismaken' => ['/kennismaken', 'Plan een kennismaking | vertrouwdag weddingplanner', 'Plan een vrijblijvend kennismakingsgesprek van 30 minuten, online of op locatie. Vertel over jullie plannen, ik vertel hoe ik kan helpen.', 'Plan een kennismaking · vertrouwdag', null, 'romy.jpg', 'website'],
    'privacyverklaring' => ['/privacyverklaring', 'Privacyverklaring | vertrouwdag', 'Hoe vertrouwdag omgaat met jullie persoonsgegevens.', 'Privacyverklaring · vertrouwdag', null, 'paar-landschap.jpg', 'website'],
    'cookiebeleid' => ['/cookiebeleid', 'Cookiebeleid | vertrouwdag', 'Welke cookies vertrouwdag.nl gebruikt, waarvoor, en hoe jullie je keuze aanpassen.', 'Cookiebeleid · vertrouwdag', null, 'paar-landschap.jpg', 'website'],
    'algemene voorwaarden' => ['/algemene-voorwaarden', 'Algemene voorwaarden | vertrouwdag', 'De algemene voorwaarden van vertrouwdag weddingplanning.', 'Algemene voorwaarden · vertrouwdag', null, 'paar-landschap.jpg', 'website'],
];

beforeEach(function () {
    // Forceer de gebouwde manifest, zodat og:image naar de echte foto wijst, ook als de Vite-devserver lokaal draait.
    Vite::useHotFile(storage_path('framework/testing/geen-vite-devserver'));

    config([
        'vertrouwdag.canonieke_url' => 'https://vertrouwdag.nl',
        'vertrouwdag.btw_percentage' => 21,
    ]);
});

test('every page has its own title, description, canonical and open graph tags', function (string $pad, string $titel, string $omschrijving, string $ogTitel, ?string $ogOmschrijving, string $ogAfbeelding, string $ogType) {
    $this->seed(PaginasSeeder::class);

    $document = documentVoor($pad);

    $canonical = 'https://vertrouwdag.nl'.$pad;
    $afbeelding = $document->querySelector('meta[property="og:image"]')?->getAttribute('content');

    expect(tekstenVan($document->querySelectorAll('title')))->toBe([$titel])
        ->and(attributenVan($document->querySelectorAll('meta[name="description"]'), 'content'))->toBe([$omschrijving])
        ->and(attributenVan($document->querySelectorAll('link[rel="canonical"]'), 'href'))->toBe([$canonical])
        ->and($document->querySelector('meta[name="robots"]'))->toBeNull();

    expect($document->querySelector('meta[property="og:type"]')?->getAttribute('content'))->toBe($ogType)
        ->and($document->querySelector('meta[property="og:title"]')?->getAttribute('content'))->toBe($ogTitel)
        ->and($document->querySelector('meta[property="og:description"]')?->getAttribute('content'))->toBe($ogOmschrijving ?? $omschrijving)
        ->and($document->querySelector('meta[property="og:url"]')?->getAttribute('content'))->toBe($canonical);

    [$naam, $extensie] = explode('.', $ogAfbeelding);

    expect($afbeelding)->toBeUrl()
        ->and(basename(parse_url($afbeelding, PHP_URL_PATH)))->toStartWith($naam.'-')->toEndWith('.'.$extensie);
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
     * Die tellen hieronder mee zoals de seeders ze zetten; hun SEO komt uit dezelfde lijst (App\Support\PaginaSeo).
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

test('an article outside the list above takes its title, description and photo from its article heading', function () {
    $foto = Media::factory()->create();
    artikelOnderBlog('trouwen-in-de-winter', [
        'titel' => 'Trouwen in de *winter*',
        'intro' => 'Kort licht, warme kleuren en een feest binnen.',
        'foto' => $foto->id,
    ]);

    $document = documentVoor('/blog/trouwen-in-de-winter');

    expect(tekstenVan($document->querySelectorAll('title')))->toBe(['Trouwen in de winter | vertrouwdag'])
        ->and($document->querySelector('meta[name="description"]')?->getAttribute('content'))->toBe('Kort licht, warme kleuren en een feest binnen.')
        ->and($document->querySelector('meta[property="og:title"]')?->getAttribute('content'))->toBe('Trouwen in de winter · vertrouwdag')
        ->and($document->querySelector('meta[property="og:type"]')?->getAttribute('content'))->toBe('article')
        ->and($document->querySelector('meta[property="og:image"]')?->getAttribute('content'))->toEndWith("/storage/{$foto->path}");
});
