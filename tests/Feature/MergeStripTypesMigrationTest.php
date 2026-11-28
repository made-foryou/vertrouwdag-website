<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Made\CmsPackage\Content\Actions\PublishPage;
use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\MediaUsage;
use Made\CmsPackage\Content\Models\Page;
use Made\CmsPackage\Content\Models\PageVersion;

/**
 * The migration that moves the strips of the merged strip types to the type that took them over (2026-11-28).
 */
function mergeStripTypesMigration(): Migration
{
    return require database_path('migrations/2026_11_28_152549_merge_strip_types.php');
}

/**
 * A strip the way the cms stores it, with the values as on staging: a heading as its text, a photo as its id.
 *
 * @param  array<string, mixed>  $values
 * @return array{id: string, type: string, visible: bool, values: array<string, mixed>}
 */
function storedStrip(string $type, array $values, bool $visible = true): array
{
    return ['id' => (string) Str::uuid(), 'type' => $type, 'visible' => $visible, 'values' => $values];
}

/**
 * One strip of each merged type, and a strip that keeps its type.
 *
 * @return array<string, array{id: string, type: string, visible: bool, values: array<string, mixed>}>
 */
function stripsOfMergedTypes(): array
{
    return [
        'hero-diensten' => storedStrip('hero-diensten', [
            'kop' => '*Ceremoniemeester inhuren* voor jullie trouwdag',
            'knop' => null,
            'label' => 'Diensten en prijzen',
            'tekst' => 'Jullie willen dat iedereen op jullie trouwdag kan genieten.',
            'sticker' => "Kies jullie\npakket",
            'foto_boog' => Media::factory()->create()->id,
            'bijschrift' => 'alles geregeld',
            'foto_polaroid' => null,
        ]),
        'hero-over-mij' => storedStrip('hero-over-mij', [
            'kop' => '*Romy, de weddingplanner* van Vertrouwdag',
            'knop' => ['text' => 'Maak kennis met mij', 'link' => ['url' => '/kennismaken']],
            'label' => 'Over mij',
            'tekst' => 'Wat leuk dat jullie me beter willen leren kennen.',
            'foto_boog' => null,
            'bijschrift' => 'hier word ik blij van',
            'foto_polaroid' => null,
        ], visible: false),
        'hero-archief' => storedStrip('hero-archief', [
            'kop' => 'Alle *blogs*',
            'label' => 'Blogarchief',
            'tekst' => 'Alles wat ik schreef over rustig plannen.',
        ]),
        'artikel-citaat' => storedStrip('artikel-citaat', [
            'citaat' => 'Hoe eerder jullie hulp vragen, hoe meer van de voorbereiding ook echt leuk blijft.',
        ]),
        'kennismaking' => storedStrip('kennismaking', [
            'kop' => 'Benieuwd wat het *voor jullie* kan betekenen?',
            'label' => 'Zullen we kennismaken?',
            'tekst' => 'Een half uur, online en vrijblijvend.',
        ]),
        'verhaal' => storedStrip('verhaal', [
            'kop' => 'Wat kost een *weddingplanner*?',
            'tekst' => '<p>Een alinea.</p>',
        ]),
    ];
}

/**
 * The strip after the migration: the new type, and the variant added to its values.
 *
 * @param  array{id: string, type: string, visible: bool, values: array<string, mixed>}  $strip
 * @return array{id: string, type: string, visible: bool, values: array<string, mixed>}
 */
function movedStrip(array $strip, string $type, string $variant): array
{
    return [...$strip, 'type' => $type, 'values' => [...$strip['values'], 'variant' => $variant]];
}

/**
 * Every row the migration may write, decoded, to compare before and after.
 *
 * @return array<string, mixed>
 */
function storedContent(): array
{
    $decode = fn (?string $json): mixed => $json === null ? null : json_decode($json, true);

    return [
        'pages' => DB::table('cms_pages')->orderBy('id')->get(['id', 'strips', 'draft_strips', 'updated_at'])
            ->map(fn (object $page): array => ['id' => $page->id, 'strips' => $decode($page->strips), 'draft' => $decode($page->draft_strips), 'updated_at' => $page->updated_at])->all(),
        'versions' => DB::table('cms_page_versions')->orderBy('id')->get(['id', 'strips', 'changes'])
            ->map(fn (object $version): array => ['id' => $version->id, 'strips' => $decode($version->strips), 'changes' => $decode($version->changes)])->all(),
        'usages' => DB::table('cms_media_usages')->orderBy('id')->get(['id', 'strip_id', 'strip_type', 'field'])->map(fn (object $usage): array => (array) $usage)->all(),
    ];
}

/**
 * A page with published strips and a draft, a deleted page, a version with a removed strip and two media usages.
 *
 * @param  array<string, array{id: string, type: string, visible: bool, values: array<string, mixed>}>  $strips
 * @return array{page: Page, deleted: Page, version: PageVersion}
 */
function pagesWithMergedTypes(array $strips): array
{
    $page = Page::factory()->published()->at('/diensten')
        ->withStrips([$strips['hero-diensten'], $strips['verhaal']])
        ->withDraft([$strips['hero-over-mij'], $strips['kennismaking'], $strips['verhaal']])
        ->create(['updated_at' => now()->subWeek()]);
    $deleted = Page::factory()->published()->at('/oud')->withStrips([$strips['artikel-citaat']])->deletedAt(now()->subDay())->create();
    $version = PageVersion::factory()->for($page)->withStrips([$strips['hero-archief']])->create([
        'changes' => ['first' => false, 'added' => [], 'changed' => [], 'moved' => [], 'removed' => [$strips['kennismaking']]],
    ]);

    MediaUsage::factory()->create(['page_id' => $page->id, 'strip_id' => $strips['hero-diensten']['id'], 'strip_type' => 'hero-diensten', 'field' => 'foto_boog']);
    MediaUsage::factory()->create(['page_id' => $page->id, 'strip_type' => 'cta', 'field' => 'foto']);

    return ['page' => $page, 'deleted' => $deleted, 'version' => $version];
}

test('every strip of a merged type moves to the new type with its variant, and keeps its id, visibility and values', function () {
    $strips = stripsOfMergedTypes();
    ['page' => $page, 'deleted' => $deleted, 'version' => $version] = pagesWithMergedTypes($strips);
    $updatedAt = $page->fresh()->updated_at;

    mergeStripTypesMigration()->up();

    expect($page->fresh()->strips)->toEqual([movedStrip($strips['hero-diensten'], 'hero', 'diensten'), $strips['verhaal']])
        ->and($page->fresh()->draft_strips)->toEqual([movedStrip($strips['hero-over-mij'], 'hero', 'over-mij'), movedStrip($strips['kennismaking'], 'cta', 'kaart'), $strips['verhaal']])
        ->and(Page::withTrashed()->find($deleted->id)->strips)->toEqual([movedStrip($strips['artikel-citaat'], 'citaat', 'artikel')])
        ->and($version->fresh()->strips)->toEqual([movedStrip($strips['hero-archief'], 'hero-blog', 'archief')])
        ->and($version->fresh()->changes['removed'])->toEqual([movedStrip($strips['kennismaking'], 'cta', 'kaart')])
        ->and($page->fresh()->updated_at->equalTo($updatedAt))->toBeTrue()
        ->and(MediaUsage::query()->orderBy('id')->pluck('strip_type')->all())->toBe(['hero', 'cta']);
});

test('the merge migration changes nothing the second time', function () {
    pagesWithMergedTypes(stripsOfMergedTypes());
    mergeStripTypesMigration()->up();
    $once = storedContent();

    mergeStripTypesMigration()->up();

    expect(storedContent())->toEqual($once);
});

test('rolling the merge migration back puts every strip under its old type again', function () {
    pagesWithMergedTypes(stripsOfMergedTypes());
    $before = storedContent();

    mergeStripTypesMigration()->up();
    mergeStripTypesMigration()->down();

    expect(storedContent())->toEqual($before);
});

test('a page with moved strips can be published as it is', function () {
    $strips = stripsOfMergedTypes();
    $page = Page::factory()->published()->at('/pagina')->withDraft([
        $strips['hero-diensten'], $strips['hero-over-mij'], $strips['hero-archief'], $strips['artikel-citaat'], $strips['kennismaking'], $strips['verhaal'],
    ])->create();

    mergeStripTypesMigration()->up();
    PublishPage::run($page->fresh());

    expect(array_column($page->fresh()->strips, 'type'))->toBe(['hero', 'hero', 'hero-blog', 'citaat', 'cta', 'verhaal'])
        ->and(array_column(array_column($page->fresh()->strips, 'values'), 'variant'))->toBe(['diensten', 'over-mij', 'archief', 'artikel', 'kaart']);
});
