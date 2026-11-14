<?php

use Database\Seeders\DienstenpaginaSeeder;
use Dom\Element;
use Illuminate\Support\Str;
use Made\CmsPackage\Content\Models\Page;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\Items;
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
        'artikel-kop' => ['intro', 'sticker'],
        'artikel-tekst' => ['kop', 'inleiding'],
        'blog-laatste' => ['binnenkort'],
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
