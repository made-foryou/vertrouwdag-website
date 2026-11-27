<?php

use App\Cms\Strips\ChecklistAanmelding;
use App\Cms\Strips\Diensten;
use App\Cms\Strips\Herkenning;
use App\Cms\Strips\HeroChecklist;
use App\Cms\Strips\Pakketten;
use App\Support\Icoon;
use Database\Seeders\DienstenpaginaSeeder;
use Dom\Element;
use Illuminate\Support\Str;
use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\Page;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\Field;
use Made\CmsPackage\Content\Strips\Fields\Image;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\LongText;
use Made\CmsPackage\Content\Strips\StripType;
use Made\CmsPackage\Content\Strips\StripTypes;

beforeEach(function () {
    $this->withoutVite();
});

/**
 * Elk striptype in app/Cms/Strips, zoals de catalogus van het CMS ze vindt. Een nieuw striptype (#15) valt er vanzelf
 * onder.
 */
dataset('striptypes', fn (): array => array_map(
    fn (string $bestand): string => 'App\\Cms\\Strips\\'.basename($bestand, '.php'),
    glob(__DIR__.'/../../app/Cms/Strips/*.php'),
));

/**
 * Zet een pagina met één strook van het type in het CMS, met zijn standaardwaarden, en geeft de strook zoals de
 * bezoeker hem krijgt.
 */
function strookMetStandaardwaarden(StripType $type): ?Element
{
    $id = (string) Str::uuid();
    Page::factory()->published()->at('/strook')->withStrips([['id' => $id, 'type' => $type->key(), 'visible' => true, 'values' => []]])->create();

    return documentVoor('/strook')->querySelector("main [data-cms-strip=\"{$id}\"]");
}

/**
 * Elk element in de strook dat bij de selector past en niet in een lijst van de strook ligt: daar zoekt het frame een
 * veld van de strook zelf, en elke lijst met dezelfde key (zoals de tabjes, mappen en mapjes van pakketten).
 *
 * @return list<Element>
 */
function buitenLijsten(Element $strook, string $selector): array
{
    $elementen = [];

    foreach ($strook->querySelectorAll($selector) as $element) {
        if ($element->parentElement?->closest('[data-cms-items]') === null) {
            $elementen[] = $element;
        }
    }

    return $elementen;
}

test('the catalogue of the cms builds with the strip types of the pages in the cms', function (string $key) {
    expect(app(StripTypes::class)->has($key))->toBeTrue();
})->with([
    'artikel-citaat',
    'artikel-fotos',
    'artikel-kop',
    'artikel-tekst',
    'auteur',
    'blog-archief',
    'blog-laatste',
    'blog-uitgelicht',
    'checklist-aanmelding',
    'checklist-bedankt',
    'citaat',
    'contact',
    'cta',
    'diensten',
    'faq',
    'herkenning',
    'hero',
    'hero-archief',
    'hero-blog',
    'hero-checklist',
    'hero-diensten',
    'hero-faq',
    'hero-kennismaken',
    'hero-over-mij',
    'hero-werkwijze',
    'juridisch',
    'kennismaking',
    'kijkje',
    'kwaliteiten',
    'leveranciers',
    'lopende-band',
    'over-mij-intro',
    'pakketten',
    'reviews',
    'stappen',
    'tekst',
    'tekst-met-afbeelding',
    'tijdlijn',
    'trouwdag',
    'twee-kolommen',
    'vergelijking',
    'verhaal',
]);

test('every strip type of the cms renders its view with its default values', function (string $striptype) {
    $strook = strookMetStandaardwaarden(new $striptype);

    expect($strook)->not->toBeNull();
})->with('striptypes');

/**
 * De klassen die het stylesheet in een regel van alleen die klasse een breedte of een marge geeft, zoals .wrap.
 *
 * @return list<string>
 */
function klassenMetBreedteOfMarge(): array
{
    $css = preg_replace('#/\*.*?\*/#s', '', (string) file_get_contents(resource_path('css/style.css')));
    preg_match_all('/([^{}]+)\{([^{}]*)\}/', (string) $css, $regels, PREG_SET_ORDER);

    $klassen = [];

    foreach ($regels as [, $selectors, $declaraties]) {
        if (preg_match('/(?:^|;)\s*(?:max-width|width|margin)/', $declaraties) !== 1) {
            continue;
        }

        foreach (explode(',', $selectors) as $selector) {
            if (preg_match('/^\.([\w-]+)$/', trim($selector), $klasse) === 1) {
                $klassen[] = $klasse[1];
            }
        }
    }

    return array_values(array_unique($klassen));
}

/**
 * Every strip moves the way #37 gives its kind (data-reveal), and a hero enters with the page (data-entrance, #36), so
 * a strip an editor adds moves along by itself. A hero never waits for scrolling: its heading and photo are the LCP.
 * The legal strip holds the heading of its page, so it enters like a hero. The marquee moves by itself, and the
 * thank-you block stays hidden until the form is sent (#40).
 */
test('every strip type comes in when it scrolls into view, and a hero enters with the page', function (string $striptype) {
    $type = new $striptype;
    $strook = strookMetStandaardwaarden($type);
    $isHero = str_starts_with($type->key(), 'hero') || in_array($type->key(), ['artikel-kop', 'juridisch'], true);

    if (in_array($type->key(), ['lopende-band', 'checklist-bedankt'], true)) {
        expect($strook?->querySelector('[data-reveal], [data-reveal-group], [data-entrance]'))->toBeNull();

        return;
    }

    expect($strook?->querySelector($isHero ? '[data-entrance]' : '[data-reveal], [data-reveal-group]'))->not->toBeNull()
        ->and($isHero ? $strook?->querySelector('[data-reveal], [data-reveal-group]') : null)->toBeNull();
})->with('striptypes');

/**
 * De bewerkschil legt een kader over het element met data-cms-strip. Dat element loopt daarom over de volle breedte
 * en sluit zonder marge aan op de strook erboven en eronder; de breedte van de inhoud zit op een .wrap in de strook.
 */
test('every strip type spans the full width, so the edit frame outlines it from edge to edge', function (string $striptype) {
    $strook = strookMetStandaardwaarden(new $striptype);
    $klassen = array_filter(explode(' ', $strook?->getAttribute('class') ?? ''));

    expect(array_values(array_intersect($klassen, klassenMetBreedteOfMarge())))->toBe([])
        ->and($strook?->getAttribute('style') ?? '')->not->toMatch('/width|margin/');
})->with('striptypes');

/**
 * Het frame van de bewerkschil zoekt een veld op zijn data-cms-field, en een veld in een lijst binnen het item: elk
 * direct kind van data-cms-items is één item, en een lijst mag op meer plekken staan (resources/launcher/edit.js in het
 * package). Een keuze herlaadt het frame altijd, en de velden hieronder bewust: daar staat in de view waarom. Een veld
 * in een lijst heet hier lijst.veld.
 *
 * De vergelijking toont haar tabel alleen met de pakketten van /diensten, en de blog het nieuwste artikel alleen met
 * een artikel onder /blog, dus die staan er eerst.
 */
test('the edit frame finds every field of a strip type in its view', function (string $striptype) {
    /** @var StripType $type */
    $type = new $striptype;
    $herlaadt = [
        'artikel-kop' => ['titel', 'intro', 'sticker'],
        'artikel-tekst' => ['kop', 'inleiding'],
        'blog-laatste' => ['binnenkort'],
        'faq' => ['label_2', 'vragen_2', 'label_3', 'vragen_3'],
        'lopende-band' => ['teksten'],
        'pakketten' => ['diensten.kenmerken', 'diensten.prijs'],
        'tijdlijn' => ['eerste_nummer'],
        'trouwdag' => ['nummer'],
        'vergelijking' => ['onderdelen'],
    ][$type->key()] ?? [];

    $this->seed(DienstenpaginaSeeder::class);
    artikelOnderBlog('een-blog');
    $strook = strookMetStandaardwaarden($type);

    foreach ($type->fields() as $veld) {
        if ($veld instanceof Choice || in_array($veld->getKey(), $herlaadt, true)) {
            continue;
        }

        if (! $veld instanceof Items) {
            expect(buitenLijsten($strook, "[data-cms-field=\"{$veld->getKey()}\"]"))->not->toBeEmpty("Het veld {$veld->getKey()} heeft geen data-cms-field.");

            continue;
        }

        $lijsten = buitenLijsten($strook, "[data-cms-items=\"{$veld->getKey()}\"]");

        expect($lijsten)->not->toBeEmpty("De lijst {$veld->getKey()} heeft geen data-cms-items.");

        foreach ($lijsten as $lijst) {
            expect($lijst->childElementCount)->toBe(count($type->defaults()[$veld->getKey()]), "Niet elk kind van de lijst {$veld->getKey()} is een item.");
        }

        foreach ($veld->getFields() as $itemveld) {
            if ($itemveld instanceof Choice || in_array("{$veld->getKey()}.{$itemveld->getKey()}", $herlaadt, true)) {
                continue;
            }

            $selector = "[data-cms-field=\"{$itemveld->getKey()}\"]";

            foreach (range(0, count($type->defaults()[$veld->getKey()]) - 1) as $index) {
                $gevonden = array_filter($lijsten, fn (Element $lijst): bool => $lijst->children->item($index)->matches($selector) || $lijst->children->item($index)->querySelector($selector) !== null);

                expect($gevonden)->not->toBeEmpty("Het veld {$itemveld->getKey()} in item ".($index + 1)." van de lijst {$veld->getKey()} heeft geen data-cms-field.");
            }
        }
    }
})->with('striptypes');

/**
 * De fotovelden van een striptype: per veld de lijst waar het in staat (null voor een veld van de strook zelf) en zijn
 * key.
 *
 * @return list<array{0: ?string, 1: string}>
 */
function fotoveldenVan(StripType $type): array
{
    $fotovelden = [];

    foreach ($type->fields() as $veld) {
        if ($veld instanceof Image) {
            $fotovelden[] = [null, $veld->getKey()];
        }

        if ($veld instanceof Items) {
            foreach ($veld->getFields() as $itemveld) {
                if ($itemveld instanceof Image) {
                    $fotovelden[] = [$veld->getKey(), $itemveld->getKey()];
                }
            }
        }
    }

    return $fotovelden;
}

/**
 * Elk striptype in app/Cms/Strips met een fotoveld (#21). De dataset leest de broncode, want hij ontstaat voordat de app
 * er is, en de velden van sommige types lezen de config.
 *
 * Niet blog-laatste: de blogs die binnenkort komen zijn kaarten (x-blogkaart) zonder data-cms-field, dus het frame
 * herlaadt na een wijziging (zie de test van het frame hierboven).
 */
dataset('striptypes met fotos', fn (): array => array_values(array_map(
    fn (string $bestand): string => 'App\\Cms\\Strips\\'.basename($bestand, '.php'),
    array_filter(glob(__DIR__.'/../../app/Cms/Strips/*.php'), fn (string $bestand): bool => basename($bestand) !== 'BlogLaatste.php' && preg_match('/Image::make\(|Velden::foto\(/', file_get_contents($bestand)) === 1),
)));

/**
 * Zet een pagina met één strook van het type in het CMS, met zijn standaardwaarden en in elk fotoveld de foto, ook in
 * elk item van een lijst, en geeft elke <img> van een fotoveld zoals de bezoeker hem krijgt, per veld.
 *
 * @return array<string, list<Element>>
 */
function fotosMetGekozenFoto(StripType $type, Media $foto): array
{
    $waarden = $type->defaults();

    foreach (fotoveldenVan($type) as [$lijst, $key]) {
        if ($lijst === null) {
            $waarden[$key] = $foto->id;
        } else {
            $waarden[$lijst] = array_map(fn (array $item): array => [...$item, $key => $foto->id], $waarden[$lijst]);
        }
    }

    $id = (string) Str::uuid();
    Page::factory()->published()->at('/strook')->withStrips([['id' => $id, 'type' => $type->key(), 'visible' => true, 'values' => $type->normalize($waarden)]])->create();
    $strook = documentVoor('/strook')->querySelector("main [data-cms-strip=\"{$id}\"]");

    $fotos = [];

    foreach (fotoveldenVan($type) as [$lijst, $key]) {
        $fotos[$lijst === null ? $key : "{$lijst}.{$key}"] = $lijst === null
            ? buitenLijsten($strook, "img[data-cms-field=\"{$key}\"]")
            : iterator_to_array($strook->querySelectorAll("[data-cms-items=\"{$lijst}\"] img[data-cms-field=\"{$key}\"]"));
    }

    return $fotos;
}

test('every photo field of a strip type shows the photo chosen in the cms', function (string $striptype) {
    $foto = Media::factory()->withAlt('Twee gouden trouwringen')->create();

    $fotos = fotosMetGekozenFoto(new $striptype, $foto);

    foreach ($fotos as $veld => $elementen) {
        expect($elementen)->not->toBeEmpty("Het fotoveld {$veld} staat niet als <img> in de view.");

        foreach ($elementen as $element) {
            expect($element->getAttribute('src'))->toEndWith("/storage/{$foto->path}", "Het fotoveld {$veld} toont de gekozen foto niet.");
        }
    }
})->with('striptypes met fotos');

test('every photo field lets the browser choose a narrower size of its photo, by the width the page shows it at', function (string $striptype) {
    $foto = Media::factory()->withVariants([320, 960])->create();

    $fotos = fotosMetGekozenFoto(new $striptype, $foto);

    foreach ($fotos as $veld => $elementen) {
        foreach ($elementen as $element) {
            expect($element->getAttribute('srcset'))->toMatch('#/320w\.webp 320w#', "Het fotoveld {$veld} geeft de browser geen smallere formaten.")
                ->and($element->getAttribute('sizes'))->not->toBeEmpty("Het fotoveld {$veld} zegt niet hoe breed de foto staat.");
        }
    }
})->with('striptypes met fotos');

test('a decorative photo has an empty alt text, also when its file has one', function (string $striptype) {
    $foto = Media::factory()->withAlt('Twee gouden trouwringen')->create();

    $fotos = fotosMetGekozenFoto(new $striptype, $foto);

    $alts = array_map(fn (Element $foto): string => $foto->getAttribute('alt'), array_merge(...array_values($fotos)));

    expect($alts)->not->toBeEmpty()->each->toBe('');
})->with([
    'diensten' => Diensten::class,
    'pakketten' => Pakketten::class,
    'hero-checklist' => HeroChecklist::class,
    'checklist-aanmelding' => ChecklistAanmelding::class,
]);

/**
 * Elk striptype met een tekstvak (LongText), ook in een lijst. Gefilterd op de broncode, want een dataset draait
 * voordat de app opstart en fields() van een type kan config() lezen.
 *
 * Niet blog-laatste: de intro van een blog die binnenkort komt staat op de blogkaart (zie de test daarvan). En niet
 * pakketten: de kenmerken van een pakket zijn een lijstje met één kenmerk per regel.
 */
dataset('striptypes met tekstvakken', fn (): array => array_values(array_map(
    fn (string $bestand): string => 'App\\Cms\\Strips\\'.basename($bestand, '.php'),
    array_filter(glob(__DIR__.'/../../app/Cms/Strips/*.php'), fn (string $bestand): bool => ! in_array(basename($bestand), ['BlogLaatste.php', 'Pakketten.php'], true) && str_contains(file_get_contents($bestand), 'LongText::make(')),
)));

/**
 * De tekstvakken van een striptype: per veld de lijst waar het in staat (null voor een veld van de strook zelf) en zijn
 * key.
 *
 * @return list<array{0: ?string, 1: string}>
 */
function tekstvakkenVan(StripType $type): array
{
    $tekstvakken = [];

    foreach ($type->fields() as $veld) {
        if ($veld instanceof LongText) {
            $tekstvakken[] = [null, $veld->getKey()];
        }

        if ($veld instanceof Items) {
            foreach ($veld->getFields() as $itemveld) {
                if ($itemveld instanceof LongText) {
                    $tekstvakken[] = [$veld->getKey(), $itemveld->getKey()];
                }
            }
        }
    }

    return $tekstvakken;
}

/**
 * Een enter in een tekstvak is op de site een nieuwe regel, ook in elk item van een lijst. Niet hier de intro van een
 * artikel: die staat op de blogkaart (zie de test daarvan).
 */
test('every text area of a strip type shows its line breaks on the site', function (string $striptype) {
    /** @var StripType $type */
    $type = new $striptype;
    $tekstvakken = array_values(array_filter(tekstvakkenVan($type), fn (array $tekstvak): bool => $tekstvak !== [null, 'intro'] || $type->key() !== 'artikel-kop'));
    $waarden = $type->defaults();

    foreach ($tekstvakken as [$lijst, $key]) {
        if ($lijst === null) {
            $waarden[$key] = "Eerste regel\nTweede regel";
        } else {
            $waarden[$lijst] = array_map(fn (array $item): array => [...$item, $key => "Eerste regel\nTweede regel"], $waarden[$lijst]);
        }
    }

    $id = (string) Str::uuid();
    Page::factory()->published()->at('/strook')->withStrips([['id' => $id, 'type' => $type->key(), 'visible' => true, 'values' => $type->normalize($waarden)]])->create();
    $strook = documentVoor('/strook')->querySelector("main [data-cms-strip=\"{$id}\"]");

    expect($tekstvakken)->not->toBeEmpty();

    foreach ($tekstvakken as [$lijst, $key]) {
        $veld = $lijst === null ? $key : "{$lijst}.{$key}";
        $elementen = $lijst === null
            ? buitenLijsten($strook, "[data-cms-field=\"{$key}\"]")
            : iterator_to_array($strook->querySelectorAll("[data-cms-items=\"{$lijst}\"] [data-cms-field=\"{$key}\"]"));

        expect($elementen)->not->toBeEmpty("Het tekstvak {$veld} staat niet in de view.");

        foreach ($elementen as $element) {
            expect($element->querySelectorAll('br'))->toHaveCount(1, "Het tekstvak {$veld} toont zijn enter niet.")
                ->and($element->textContent)->toContain('Tweede regel');
        }
    }
})->with('striptypes met tekstvakken');

test('an editor chooses the icon of a quote out of every icon, or none for the icon of its place', function () {
    $citaten = collect((new Herkenning)->fields())->first(fn (Field $veld): bool => $veld->getKey() === 'citaten');
    $icoon = collect($citaten->getFields())->first(fn (Field $veld): bool => $veld->getKey() === 'icoon');

    expect($icoon)->toBeInstanceOf(Choice::class)
        ->and(array_keys($icoon->getOptions()))->toBe(array_column(Icoon::cases(), 'value'))
        ->and($icoon->isRequired())->toBeFalse();
});
