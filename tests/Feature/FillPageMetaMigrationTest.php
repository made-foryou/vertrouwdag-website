<?php

use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\Page;

/**
 * The migration that moves the fixed list of titles, descriptions and share photos into the cms (#23).
 */
function runFillPageMetaMigration(): void
{
    $migration = require database_path('migrations/2026_11_19_193451_fill_page_meta_from_fixed_seo_list.php');
    $migration->up();
}

test('the migration gives the existing pages the title and description they had', function () {
    $werkwijze = Page::factory()->published()->at('/werkwijze')->create(['title' => 'Werkwijze']);

    runFillPageMetaMigration();

    expect($werkwijze->fresh()->meta_title)->toBe('Werkwijze: zo plan ik jullie bruiloft | vertrouwdag')
        ->and($werkwijze->fresh()->meta_description)->toBe('Van het eerste kennismakingsgesprek tot de dag na de bruiloft: zo werk ik als weddingplanner in Zuid-Holland, stap voor stap.');
});

test('the migration keeps what the editor already typed', function () {
    $overMij = Page::factory()->published()->at('/over-mij')->withMeta('Mijn eigen titel')->create(['title' => 'Over mij']);

    runFillPageMetaMigration();

    expect($overMij->fresh()->meta_title)->toBe('Mijn eigen titel')
        ->and($overMij->fresh()->meta_description)->toStartWith('Ik ben Romy, de weddingplanner achter vertrouwdag.');
});

test('the migration leaves the description of the services page to the site, which knows the prices', function () {
    $diensten = Page::factory()->published()->at('/diensten')->create(['title' => 'Diensten']);

    runFillPageMetaMigration();

    expect($diensten->fresh()->meta_title)->toBe('Diensten en prijzen | vertrouwdag weddingplanner')
        ->and($diensten->fresh()->meta_description)->toBeNull();
});

test('the migration gives a page its share photo from the media library, when the photo is there', function () {
    $romy = Media::factory()->create(['name' => 'romy']);
    Media::factory()->create(['name' => 'romy']);
    $overMij = Page::factory()->published()->at('/over-mij')->create(['title' => 'Over mij']);
    $checklist = Page::factory()->published()->at('/checklist')->create(['title' => 'Checklist']);
    $werkwijze = Page::factory()->published()->at('/werkwijze')->create(['title' => 'Werkwijze']);

    runFillPageMetaMigration();

    expect($overMij->fresh()->share_image_id)->toBe($romy->id)
        ->and($checklist->fresh()->share_image_id)->toBeNull()
        ->and($werkwijze->fresh()->share_image_id)->toBeNull();
});

test('the migration does nothing on a database without pages', function () {
    runFillPageMetaMigration();

    expect(Page::query()->count())->toBe(0);
});
