<?php

use Illuminate\Support\Str;
use Made\CmsPackage\Content\Models\Page;
use Made\CmsPackage\Content\Strips\StripType;

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

test('every strip type of the cms renders its view with its default values', function (string $striptype) {
    /** @var StripType $type */
    $type = new $striptype;
    $id = (string) Str::uuid();
    Page::factory()->published()->at('/strook')->withStrips([['id' => $id, 'type' => $type->key(), 'visible' => true, 'values' => []]])->create();

    $document = documentVoor('/strook');

    expect($document->querySelector("main [data-cms-strip=\"{$id}\"]"))->not->toBeNull();
})->with('striptypes');
