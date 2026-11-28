<?php

use Database\Seeders\PaginasSeeder;
use Dom\Element;
use Made\CmsPackage\Content\Models\Page;

beforeEach(function () {
    $this->withoutVite();
});

test('every page has exactly one h1 with the default levels', function () {
    $this->seed(PaginasSeeder::class);

    $paden = Page::query()->published()->with('route')->get()
        ->map(fn (Page $pagina): string => (string) $pagina->route?->path)
        ->sort()
        ->values();

    $aantalH1PerPad = $paden->mapWithKeys(fn (string $pad): array => [$pad => documentVoor($pad)->querySelectorAll('h1')->length]);

    expect($paden)->not->toBeEmpty()
        ->and($aantalH1PerPad->filter(fn (int $aantal): bool => $aantal !== 1)->all())->toBe([]);
});

test('choosing another level changes the element of a heading, not its look', function (string $type, array $values, string $selector, string $element, string $opmaak) {
    $kop = renderStrip($type, $values)->querySelector($selector);

    expect($kop)->toBeInstanceOf(Element::class)
        ->and($kop?->localName)->toBe($element)
        ->and($kop?->classList->contains($opmaak))->toBeTrue()
        ->and(platteTekst($kop))->toBe('Een kop met nadruk')
        ->and($kop?->querySelector('em')?->textContent)->toBe('nadruk');
})->with([
    'hero als h3' => ['hero', ['kop' => kop('Een kop met *nadruk*', 'h3')], '[data-cms-field="kop"]', 'h3', 'h1'],
    'hero als span' => ['hero', ['kop' => kop('Een kop met *nadruk*', 'span')], '[data-cms-field="kop"]', 'span', 'h1'],
    'tekst als h1' => ['verhaal', ['kop' => kop('Een kop met *nadruk*', 'h1'), 'tekst' => '<p>Alinea.</p>'], '[data-cms-field="kop"]', 'h1', 'h2'],
    'kolom van twee-kolommen als h2' => ['twee-kolommen', [
        'kop' => kop('Hart *én* hoofd'),
        'tekst' => 'Tekst',
        'kolommen' => [['id' => 'k1', 'foto' => null, 'label' => 'Label', 'kop' => kop('Een kop met *nadruk*', 'h2'), 'tekst' => 'Tekst']],
    ], '[data-cms-items="kolommen"] [data-cms-field="kop"]', 'h2', 'h3'],
    'cta als h3' => ['cta', ['kop' => kop('Een kop met *nadruk*', 'h3')], '[data-cms-field="kop"]', 'h3', 'h2'],
    'artikeltekst als h4' => ['artikel-tekst', ['kop' => kop('Een kop met *nadruk*', 'h4'), 'tekst' => '<p>Alinea.</p>'], '[data-cms-field="kop"]', 'h4', 'h3'],
]);
