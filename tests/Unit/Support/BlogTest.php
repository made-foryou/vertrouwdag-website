<?php

use App\Support\Blog;
use App\Support\Blogcategorie;

test('the line above the title names the topic with the reading time, or soon for a blog to come', function (Blogcategorie $categorie, ?int $leestijd, bool $binnenkort, string $kenmerk) {
    $blog = new Blog('Titel', 'Intro', $categorie, $leestijd, '/foto.jpg', 'Foto', binnenkort: $binnenkort);

    expect($blog->kenmerk())->toBe($kenmerk);
})->with([
    'met leestijd' => [Blogcategorie::Planning, 6, false, 'Planning · 6 min'],
    'zonder leestijd' => [Blogcategorie::AchterDeSchermen, null, false, 'Achter de schermen'],
    'binnenkort' => [Blogcategorie::Budget, null, true, 'Budget · binnenkort'],
    'binnenkort met leestijd' => [Blogcategorie::Locaties, 4, true, 'Locaties in Zuid-Holland · binnenkort'],
]);

test('a blog is only published with an address and when it is not still to come', function (?string $slug, bool $binnenkort, bool $gepubliceerd) {
    $blog = new Blog('Titel', 'Intro', Blogcategorie::Planning, 6, '/foto.jpg', 'Foto', $slug, $binnenkort);

    expect($blog->isGepubliceerd())->toBe($gepubliceerd);
})->with([
    'met adres' => ['een-blog', false, true],
    'zonder adres' => [null, false, false],
    'binnenkort met adres' => ['een-blog', true, false],
]);

test('the plain title leaves out the asterisks of the accent', function () {
    $blog = new Blog('Wanneer schakel je een weddingplanner in? *(En waarom eerder dan je denkt)*', 'Intro', Blogcategorie::Planning, 6, '/foto.jpg', 'Foto');

    expect($blog->platteTitel())->toBe('Wanneer schakel je een weddingplanner in? (En waarom eerder dan je denkt)');
});
