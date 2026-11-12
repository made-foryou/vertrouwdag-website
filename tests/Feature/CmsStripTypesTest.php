<?php

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
 * Het eerste element in de strook dat bij de selector past en niet in een lijst van de strook ligt: daar zoekt het
 * frame een veld van de strook zelf.
 */
function buitenLijsten(Element $strook, string $selector): ?Element
{
    foreach ($strook->querySelectorAll($selector) as $element) {
        if ($element->parentElement?->closest('[data-cms-items]') === null) {
            return $element;
        }
    }

    return null;
}

test('the catalogue of the cms builds with the strip types of the homepage', function (string $key) {
    expect(app(StripTypes::class)->has($key))->toBeTrue();
})->with([
    'checklist-aanmelding',
    'cta',
    'diensten',
    'herkenning',
    'hero',
    'leveranciers',
    'lopende-band',
    'over-mij-intro',
    'reviews',
    'stappen',
    'twee-kolommen',
]);

test('every strip type of the cms renders its view with its default values', function (string $striptype) {
    $strook = strookMetStandaardwaarden(new $striptype);

    expect($strook)->not->toBeNull();
})->with('striptypes');

/**
 * Het frame van de bewerkschil zoekt een veld op zijn data-cms-field, en een veld in een lijst binnen het item: elk
 * direct kind van data-cms-items is één item (resources/launcher/edit.js in het package). Een keuze herlaadt het
 * frame altijd, en de velden hieronder bewust: daar staat in de view waarom.
 */
test('the edit frame finds every field of a strip type in its view', function (string $striptype) {
    /** @var StripType $type */
    $type = new $striptype;
    $herlaadt = ['lopende-band' => ['teksten']][$type->key()] ?? [];

    $strook = strookMetStandaardwaarden($type);

    foreach ($type->fields() as $veld) {
        if ($veld instanceof Choice || in_array($veld->getKey(), $herlaadt, true)) {
            continue;
        }

        if (! $veld instanceof Items) {
            expect(buitenLijsten($strook, "[data-cms-field=\"{$veld->getKey()}\"]"))->not->toBeNull("Het veld {$veld->getKey()} heeft geen data-cms-field.");

            continue;
        }

        $lijst = buitenLijsten($strook, "[data-cms-items=\"{$veld->getKey()}\"]");

        expect($lijst)->not->toBeNull("De lijst {$veld->getKey()} heeft geen data-cms-items.")
            ->and($lijst->childElementCount)->toBe(count($type->defaults()[$veld->getKey()]), "Niet elk kind van de lijst {$veld->getKey()} is een item.");

        foreach ($lijst->children as $item) {
            foreach ($veld->getFields() as $itemveld) {
                if ($itemveld instanceof Choice) {
                    continue;
                }

                $selector = "[data-cms-field=\"{$itemveld->getKey()}\"]";

                expect($item->matches($selector) || $item->querySelector($selector) !== null)->toBeTrue("Het veld {$itemveld->getKey()} in de lijst {$veld->getKey()} heeft geen data-cms-field.");
            }
        }
    }
})->with('striptypes');
