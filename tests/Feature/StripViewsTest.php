<?php

use App\Support\Blog;
use App\Support\Blogcategorie;
use App\Support\Dienst;
use App\Support\Pakket;
use App\Support\Prijs;
use App\Support\Strip;
use Dom\HTMLDocument;
use Illuminate\View\ViewException;

/**
 * Rendert een strip-view met een strook, zoals <x-strips> dat doet.
 *
 * @param  array<string, mixed>  $values
 */
function renderStrip(string $type, array $values): HTMLDocument
{
    return renderComponent("@include('strips.{$type}', ['strip' => \$strip])", ['strip' => new Strip('s1', $type, $values)]);
}

beforeEach(function () {
    $this->withoutVite();
});

test('the last word of the hero label does not wrap', function (string $label, string $laatsteWoord) {
    $document = renderStrip('hero', ['label' => $label, 'kop' => 'Kop']);

    expect(platteTekst($document->querySelector('[data-cms-field="label"]')))->toBe($label)
        ->and($document->querySelector('[data-cms-field="label"] span[style="white-space:nowrap"]')?->textContent)->toBe($laatsteWoord);
})->with([
    'meerdere woorden' => ['Weddingplanner in Zuid-Holland', 'Zuid-Holland'],
    'één woord' => ['Weddingplanner', 'Weddingplanner'],
]);

test('the first button of the hero is the one from the cms, and the button to the acquaintance when it is empty', function (?array $knop, string $tekst, string $url) {
    config(['vertrouwdag.cta' => ['pad' => '/kennismaken', 'label' => 'Plan een kennismaking']]);

    $document = renderStrip('hero', ['kop' => 'Kop', 'eerste_knop' => $knop]);

    $eersteKnop = $document->querySelector('.hero .row a.btn');

    expect($eersteKnop?->getAttribute('data-cms-field'))->toBe('eerste_knop')
        ->and(platteTekst($eersteKnop))->toBe($tekst)
        ->and($eersteKnop?->getAttribute('href'))->toBe($url);
})->with([
    'uit het cms' => [['text' => 'Bel me even', 'url' => '/contact'], 'Bel me even', '/contact'],
    'leeg' => [null, 'Plan een kennismaking', '/kennismaken'],
]);

test('the services strip rejects an unknown service and names it', function () {
    $diensten = [['id' => null, 'dienst' => 'goud', 'soort' => 'Soort', 'titel' => 'Titel', 'omschrijving' => 'Omschrijving']];

    expect(fn () => renderStrip('diensten', ['kop' => 'Mijn *diensten*', 'diensten' => $diensten]))
        ->toThrow(ViewException::class, 'De strook diensten kent geen dienst [goud]');
});

test('a review without a label or name shows only its quote', function () {
    $reviews = [['id' => null, 'label' => null, 'citaat' => '"Fijn samengewerkt."', 'naam' => '']];

    $document = renderStrip('reviews', ['kop' => 'Wat anderen *zeggen*', 'reviews' => $reviews]);
    $review = $document->querySelector('[data-cms-items="reviews"] > figure');

    expect($review?->querySelector('blockquote')?->textContent)->toBe('"Fijn samengewerkt."')
        ->and($review?->querySelector('.review__label, figcaption'))->toBeNull();
});

test('the notes start again with the first colour after the fourth quote', function () {
    $citaten = array_map(fn (int $nummer): array => ['id' => null, 'tekst' => "Citaat {$nummer}"], range(1, 5));

    $document = renderStrip('herkenning', ['kop' => 'Klinkt dit bekend?', 'citaten' => $citaten]);
    $briefjes = $document->querySelectorAll('[data-cms-items="citaten"] > .note');

    expect($briefjes)->toHaveCount(5)
        ->and($briefjes->item(4)->getAttribute('style'))->toBe($briefjes->item(0)->getAttribute('style'))
        ->and($document->querySelectorAll('.deck-dots span'))->toHaveCount(5);
});

test('the steps are numbered in their order', function () {
    $stappen = [
        ['id' => null, 'titel' => 'Kennismaken', 'tekst' => 'Een half uur online.'],
        ['id' => null, 'titel' => 'Voorstel', 'tekst' => 'Binnen een week.'],
    ];

    $document = renderStrip('stappen', ['kop' => 'Vier stappen *naar ja*', 'stappen' => $stappen]);

    expect(tekstenVan($document->querySelectorAll('[data-cms-items="stappen"] > li .step__nr')))->toBe(['1', '2'])
        ->and($document->querySelector('.werk-head .btn'))->toBeNull();
});

test('the packages strip rejects an unknown service and names it', function () {
    $diensten = [['id' => null, 'dienst' => 'goud', 'soort' => 'Soort', 'titel' => 'Titel', 'omschrijving' => 'Omschrijving', 'kenmerken' => '', 'bijschrift' => null]];

    expect(fn () => renderStrip('pakketten', ['kop' => 'Welke map?', 'diensten' => $diensten]))
        ->toThrow(ViewException::class, 'De strook pakketten kent geen dienst [goud]');
});

test('the features of a package are its lines without the empty ones', function () {
    $diensten = [['id' => null, 'dienst' => 'dag', 'soort' => 'Dagcoördinatie', 'titel' => 'De Dag in handen', 'omschrijving' => 'Voor stellen.', 'kenmerken' => "Draaiboek\n\n  Afstemming met leveranciers  \r\n", 'bijschrift' => 'op de dag zelf']];

    $document = renderStrip('pakketten', ['kop' => 'Welke map?', 'diensten' => $diensten]);

    expect(tekstenVan($document->querySelectorAll('.tf__panel ul.hl li')))->toBe(['Draaiboek', 'Afstemming met leveranciers'])
        ->and(tekstenVan($document->querySelectorAll('.tf-m .folder ul.hl li')))->toBe(['Draaiboek', 'Afstemming met leveranciers']);
});

test('a package shows the price it was given in whole euros, and no price line when it cannot be read', function () {
    $diensten = [
        ['id' => null, 'dienst' => 'dag', 'soort' => 'Dagcoördinatie', 'titel' => 'De Dag in handen', 'omschrijving' => '<p>Voor stellen.</p>', 'kenmerken' => '', 'bijschrift' => null, 'prijs' => '€ 1.000'],
        ['id' => null, 'dienst' => 'partial', 'soort' => 'Gedeeltelijke planning', 'titel' => 'Hand in hand', 'omschrijving' => '<p>Voor stellen.</p>', 'kenmerken' => '', 'bijschrift' => null, 'prijs' => 'op aanvraag'],
    ];

    $document = renderStrip('pakketten', ['kop' => 'Welke map?', 'diensten' => $diensten]);

    expect(tekstenVan($document->querySelectorAll('.tf__panel [data-prijs-incl]')))->toBe(['€1.210'])
        ->and(tekstenVan($document->querySelectorAll('.tf-m .folder [data-prijs]')))->toBe(['€1.000'])
        ->and($document->querySelector('#partial-planning .price'))->toBeNull()
        ->and($document->querySelector('.tf-m .folder[data-for="partial-planning"] .price'))->toBeNull();
});

test('the comparison has a column per package, with its title and price, and an empty cell for a package without one', function () {
    $values = [
        'kop' => 'Wat zit *waarin?*',
        'pakketten' => [
            new Pakket(Dienst::Full, 'Full service', 'Alles in handen', new Prijs(3000, 21)),
            new Pakket(Dienst::Dag, 'Dagcoördinatie', 'De Dag in handen', null),
        ],
        'onderdelen' => [['id' => null, 'onderdeel' => 'Budgetoverzicht', 'dag' => 'nee', 'partial' => 'ja', 'full' => 'ja']],
        'prijs_label' => 'Vanaf, incl. btw',
    ];

    $document = renderStrip('vergelijking', $values);
    $prijzen = $document->querySelectorAll('tbody tr:last-child td');

    expect(attributenVan($document->querySelectorAll('thead th[data-short]'), 'data-short'))->toBe(['Alles in handen', 'De Dag in handen'])
        ->and(attributenVan($document->querySelectorAll('thead th[data-short]'), 'class'))->toBe(['c-full', 'c-dag'])
        ->and(tekstenVan($document->querySelectorAll('tbody tr:first-child td')))->toBe(['Ja', 'Nee'])
        ->and(platteTekst($prijzen->item(0)))->toBe('€3.630 €3.000 excl. btw')
        ->and($prijzen->item(1)->textContent)->toBe('');
});

test('the comparison has no table without packages', function () {
    $document = renderStrip('vergelijking', ['kop' => 'Wat zit *waarin?*', 'onderdelen' => [], 'slotzin' => 'Past geen pakket precies?']);

    expect($document->querySelector('table'))->toBeNull()
        ->and($document->querySelector('.cmp-legend'))->toBeNull()
        ->and($document->querySelector('[data-cms-field="slotzin"]'))->not->toBeNull();
});

test('the comparison rejects an answer other than yes, no or partly and names the row', function () {
    $values = [
        'kop' => 'Wat zit *waarin?*',
        'pakketten' => [new Pakket(Dienst::Dag, 'Dagcoördinatie', 'De Dag in handen', new Prijs(850, 21))],
        'onderdelen' => [['id' => null, 'onderdeel' => 'Budgetoverzicht', 'dag' => 'misschien']],
    ];

    expect(fn () => renderStrip('vergelijking', $values))
        ->toThrow(ViewException::class, 'De strook vergelijking kent geen antwoord [misschien] bij [Budgetoverzicht]');
});

test('the large closing strip has a bigger heading and a taller photo but no heart', function () {
    $document = renderStrip('cta', ['kop' => 'Twijfelen jullie *welk pakket?*', 'tekst' => 'Dat zoeken we samen uit.', 'variant' => 'groot']);

    expect($document->querySelector('.cta-split__photo')?->getAttribute('style'))->toBe('min-height:480px')
        ->and($document->querySelector('.cta-split__txt h2')?->getAttribute('style'))->toBe('font-size:clamp(40px,5vw,68px)')
        ->and($document->querySelector('.cta-split__txt > svg'))->toBeNull();
});

test('the closing strip without a variant has a heart, as on the homepage', function () {
    $document = renderStrip('cta', ['kop' => 'Zullen we *kennismaken?*']);

    expect($document->querySelector('.cta-split__txt > svg'))->not->toBeNull()
        ->and($document->querySelector('.cta-split__photo')?->hasAttribute('style'))->toBeFalse();
});

test('the closing strip rejects an unknown variant', function () {
    expect(fn () => renderStrip('cta', ['kop' => 'Kop', 'variant' => 'klein']))
        ->toThrow(ViewException::class, 'De strook cta kent geen variant [klein]');
});

test('the plain closing strip has a taller photo but no heart and the normal heading', function () {
    $document = renderStrip('cta', ['kop' => 'Het begint met *een gesprek*', 'variant' => 'eenvoudig']);

    expect($document->querySelector('.cta-split__photo')?->getAttribute('style'))->toBe('min-height:480px')
        ->and($document->querySelector('.cta-split__txt > svg'))->toBeNull()
        ->and($document->querySelector('.cta-split__txt h2')?->hasAttribute('style'))->toBeFalse()
        ->and($document->querySelector('.cta-split__txt')?->hasAttribute('style'))->toBeFalse();
});

test('the timeline numbers its phases from its first number and alternates their sides', function () {
    $fases = array_map(fn (int $nummer): array => ['id' => null, 'periode' => "Periode {$nummer}", 'titel' => "Fase {$nummer}", 'tekst' => 'Tekst'], range(1, 3));

    $document = renderStrip('tijdlijn', ['eerste_nummer' => '6', 'fases' => $fases]);
    $lijst = $document->querySelector('ol.tl[data-cms-items="fases"]');

    expect($lijst?->getAttribute('start'))->toBe('6')
        ->and(tekstenVan($lijst?->querySelectorAll('.tl__badge span')))->toBe(['6', '7', '8'])
        ->and(attributenVan($document->querySelectorAll('ol.tl > li'), 'class'))->toBe(['tl__step', 'tl__step tl__step--r', 'tl__step']);
});

test('the timeline starts at one and lies on the band only with that variant', function (?string $variant, ?string $klasse) {
    $fases = [['id' => null, 'periode' => 'Periode', 'titel' => 'Fase', 'tekst' => 'Tekst']];

    $document = renderStrip('tijdlijn', ['variant' => $variant, 'fases' => $fases]);

    expect($document->querySelector('section[data-cms-strip]')?->getAttribute('class'))->toBe($klasse)
        ->and($document->querySelector('.tl__badge span')?->textContent)->toBe('1');
})->with([
    'zonder variant' => [null, null],
    'op de band' => ['band', 'band'],
]);

test('the timeline starts at one when its first number is no positive whole number', function (?string $eersteNummer) {
    $fases = [['id' => null, 'periode' => 'Periode', 'titel' => 'Fase', 'tekst' => 'Tekst']];

    $document = renderStrip('tijdlijn', ['eerste_nummer' => $eersteNummer, 'fases' => $fases]);

    expect($document->querySelector('ol.tl')?->hasAttribute('start'))->toBeFalse()
        ->and($document->querySelector('.tl__badge span')?->textContent)->toBe('1');
})->with([
    'leeg' => [''],
    'in woorden' => ['zes'],
    'nul' => ['0'],
]);

test('the timeline rejects an unknown variant', function () {
    expect(fn () => renderStrip('tijdlijn', ['variant' => 'aubergine', 'fases' => []]))
        ->toThrow(ViewException::class, 'De strook tijdlijn kent geen variant [aubergine]');
});

test('the wedding day band shows its phase number only when it has one', function (?string $nummer, array $badges) {
    $document = renderStrip('trouwdag', ['nummer' => $nummer, 'kop' => 'Kop', 'briefjes' => []]);

    expect(tekstenVan($document->querySelectorAll('.tl__badge span')))->toBe($badges);
})->with([
    'met nummer' => ['5', ['5']],
    'met witruimte' => [' 5 ', ['5']],
    'zonder nummer' => [null, []],
    'leeg' => ['', []],
    'in woorden' => ['vijf', []],
]);

test('the notes of the wedding day start again with the first colour after the fourth', function () {
    $briefjes = array_map(fn (int $nummer): array => ['id' => null, 'wie' => "Wie {$nummer}", 'tekst' => "Tekst {$nummer}"], range(1, 5));

    $document = renderStrip('trouwdag', ['kop' => 'Kop', 'briefjes' => $briefjes]);
    $briefjesOpDePagina = $document->querySelectorAll('[data-cms-items="briefjes"] > .note');

    expect($briefjesOpDePagina)->toHaveCount(5)
        ->and($briefjesOpDePagina->item(4)->getAttribute('style'))->toBe($briefjesOpDePagina->item(0)->getAttribute('style'))
        ->and($document->querySelectorAll('.deck-dots span'))->toHaveCount(5);
});

test('the qualities start again with the first colour after the third', function () {
    $kwaliteiten = array_map(fn (int $nummer): array => ['id' => null, 'titel' => "Kwaliteit {$nummer}", 'tekst' => 'Tekst'], range(1, 4));

    $document = renderStrip('kwaliteiten', ['kop' => 'Kop', 'kwaliteiten' => $kwaliteiten]);
    $briefjes = $document->querySelectorAll('[data-cms-items="kwaliteiten"] > .note');

    expect($briefjes)->toHaveCount(4)
        ->and($briefjes->item(3)->getAttribute('style'))->toBe($briefjes->item(0)->getAttribute('style'))
        ->and($briefjes->item(3)->querySelector('svg')?->outerHTML)->toBe($briefjes->item(0)->querySelector('svg')?->outerHTML)
        ->and($document->querySelectorAll('.deck-dots.deck-dots--licht span'))->toHaveCount(4);
});

test('a quote without a heading shows only the quote', function () {
    $document = renderStrip('citaat', ['citaat' => 'Het antwoord was er al heel lang.']);
    $review = $document->querySelector('section.reviews figure.review.review--groot');

    expect($review?->querySelector('blockquote[data-cms-field="citaat"]')?->textContent)->toBe('Het antwoord was er al heel lang.')
        ->and($review?->querySelector('p.eb'))->toBeNull();
});

test('the compact closing strip has a photo of at least 420px, no heart and no empty text', function () {
    $document = renderStrip('cta', ['kop' => 'Liever direct *persoonlijk advies?*', 'variant' => 'compact']);

    expect($document->querySelector('.cta-split__photo')?->getAttribute('style'))->toBe('min-height:420px')
        ->and($document->querySelector('.cta-split__txt > svg'))->toBeNull()
        ->and($document->querySelector('.cta-split__txt > p'))->toBeNull()
        ->and($document->querySelector('.cta-split__txt h2')?->hasAttribute('style'))->toBeFalse();
});

test('the article heading leaves out the reading time when its blog has none', function () {
    $blog = new Blog('Titel', 'Intro', Blogcategorie::Inspiratie, null, '/foto.jpg', 'Foto', '/blog/titel');

    $document = renderStrip('artikel-kop', ['titel' => 'Titel', 'foto' => ['url' => '/hero.jpg', 'alt' => 'Hero', 'width' => 1920, 'height' => 1280], 'sticker' => "hoe eerder,\nhoe rustiger", 'blog' => $blog]);

    expect(tekstenVan($document->querySelectorAll('.art-head .row > span')))->toBe(['Inspiratie & styling'])
        ->and($document->querySelector('.art-hero__sticker')?->innerHTML)->toBe("hoe eerder,<br>\nhoe rustiger");
});

test('the article heading shows a sticker only when it has one', function (?string $sticker, int $aantal) {
    $document = renderStrip('artikel-kop', ['titel' => 'Titel', 'foto' => null, 'sticker' => $sticker, 'blog' => null]);

    expect($document->querySelectorAll('.art-hero__sticker'))->toHaveCount($aantal);
})->with([
    'met sticker' => ["hoe eerder,\nhoe rustiger", 1],
    'zonder sticker' => [null, 0],
]);

test('the article heading keeps its photo in shape when it has no photo yet', function () {
    $document = renderStrip('artikel-kop', ['titel' => 'Titel', 'foto' => null, 'sticker' => null, 'blog' => null]);
    $img = $document->querySelector('.art-hero__img img[data-cms-field="foto"]');

    expect($img?->getAttribute('width'))->toBe('1100')
        ->and($img?->getAttribute('height'))->toBe('733')
        ->and($img?->getAttribute('alt'))->toBe('')
        ->and($document->querySelector('.art-head .row'))->toBeNull();
});

test('the article text starts with its heading and introduction only when it has them', function (?string $kop, ?string $inleiding, array $koppen, array $leads) {
    $document = renderStrip('artikel-tekst', ['kop' => $kop, 'inleiding' => $inleiding, 'tekst' => '<p>Alinea.</p>']);

    expect(tekstenVan($document->querySelectorAll('.art-prose > h2[data-cms-field="kop"]')))->toBe($koppen)
        ->and(tekstenVan($document->querySelectorAll('.art-prose .art-lead')))->toBe($leads)
        ->and(tekstenVan($document->querySelectorAll('.art-prose [data-cms-field="tekst"] p')))->toBe(['Alinea.']);
})->with([
    'met kop en inleiding' => ['Het begin *bepaalt de rest*', 'De meeste stellen zoeken pas hulp als het al druk is.', ['Het begin bepaalt de rest'], ['De meeste stellen zoeken pas hulp als het al druk is.']],
    'zonder kop en inleiding' => [null, null, [], []],
]);

test('the heading of an article text has its accent', function () {
    $document = renderStrip('artikel-tekst', ['kop' => 'Het begin *bepaalt de rest*', 'inleiding' => null, 'tekst' => '<p>Alinea.</p>']);

    expect($document->querySelector('.art-prose > h2 em')?->textContent)->toBe('bepaalt de rest');
});

test('the photos of an article tilt one way and then the other', function () {
    $fotos = array_map(fn (int $nummer): array => ['id' => "item-{$nummer}", 'foto' => ['url' => "/foto-{$nummer}.jpg", 'alt' => "Foto {$nummer}", 'width' => 1920, 'height' => 1280], 'bijschrift' => "bijschrift {$nummer}"], range(1, 3));

    $document = renderStrip('artikel-fotos', ['fotos' => $fotos]);
    $polaroids = $document->querySelectorAll('[data-cms-items="fotos"] > figure.polaroid');

    expect(attributenVan($polaroids, 'style'))->toBe(['transform:rotate(-4deg)', 'transform:rotate(3deg)', 'transform:rotate(-4deg)'])
        ->and(tekstenVan($document->querySelectorAll('figcaption')))->toBe(['bijschrift 1', 'bijschrift 2', 'bijschrift 3']);
});

test('the medium closing strip has a photo of at least 440px, no heart and the normal heading', function () {
    $document = renderStrip('cta', ['kop' => 'Nog een *vraag?*', 'tekst' => 'Die beantwoord ik graag.', 'variant' => 'middel']);

    expect($document->querySelector('.cta-split__photo')?->getAttribute('style'))->toBe('min-height:440px')
        ->and($document->querySelector('.cta-split__txt > svg'))->toBeNull()
        ->and($document->querySelector('.cta-split__txt h2')?->hasAttribute('style'))->toBeFalse();
});

test('the FAQ strip opens only its first question and prints the answer as formatted text', function () {
    $vragen = [
        ['id' => null, 'vraag' => 'Eerste vraag?', 'antwoord' => '<p>Zie <a href="/diensten">de diensten</a>.</p>'],
        ['id' => null, 'vraag' => 'Tweede vraag?', 'antwoord' => '<p>Tweede antwoord.</p>'],
    ];

    $document = renderStrip('faq', ['label' => 'Alle vragen', 'vragen' => $vragen]);
    $details = $document->querySelectorAll('.faq-box > [data-cms-items="vragen"] > details.faq');

    expect($document->querySelector('.faq-box > .faq-box__tab')?->textContent)->toBe('Alle vragen')
        ->and($document->querySelector('[data-cms-items="vragen"]')?->childElementCount)->toBe(2)
        ->and($details)->toHaveCount(2)
        ->and($details->item(0)->hasAttribute('open'))->toBeTrue()
        ->and($details->item(1)->hasAttribute('open'))->toBeFalse()
        ->and($details->item(0)->querySelector('[data-cms-field="antwoord"] > p > a')?->getAttribute('href'))->toBe('/diensten');
});

test('the FAQ strip puts the sentence with the prices above the answers that ask for it, outside the answer', function () {
    $vragen = [
        ['id' => null, 'vraag' => 'Wat kost het?', 'antwoord' => '<p>Een vaste prijs.</p>', 'prijzen' => 'noemen'],
        ['id' => null, 'vraag' => 'Wat als het regent?', 'antwoord' => '<p>Plan B.</p>', 'prijzen' => null],
    ];

    $document = renderStrip('faq', ['label' => 'Alle vragen', 'vragen' => $vragen, 'prijzenzin' => 'Dag begint bij <span data-prijs-incl="dag">€1.028,50</span>.']);
    $details = $document->querySelectorAll('details.faq');

    expect(platteTekst($details->item(0)->querySelector('summary + p')))->toBe('Dag begint bij €1.028,50.')
        ->and($details->item(0)->querySelector('summary + p')?->hasAttribute('data-cms-field'))->toBeFalse()
        ->and(platteTekst($details->item(0)->querySelector('[data-cms-field="antwoord"]')))->toBe('Een vaste prijs.')
        ->and($details->item(1)->querySelector('summary + p'))->toBeNull();
});

test('the FAQ strip shows no sentence with the prices when there is none', function () {
    $vragen = [['id' => null, 'vraag' => 'Wat kost het?', 'antwoord' => '<p>Een vaste prijs.</p>', 'prijzen' => 'noemen']];

    $document = renderStrip('faq', ['label' => 'Alle vragen', 'vragen' => $vragen, 'prijzenzin' => null]);

    expect($document->querySelector('details.faq summary + p'))->toBeNull();
});

test('the notes of the FAQ hero start again with the first colour after the third', function () {
    $briefjes = array_map(fn (int $nummer): array => ['id' => null, 'tekst' => "Vraag {$nummer}"], range(1, 4));

    $document = renderStrip('hero-faq', ['label' => 'Label', 'kop' => 'Kop', 'tekst' => 'Tekst', 'app_tekst' => 'App', 'briefjes' => $briefjes]);
    $notes = $document->querySelectorAll('.faq-notes[data-cms-items="briefjes"] > .note');

    expect($notes)->toHaveCount(4)
        ->and($notes->item(3)->getAttribute('style'))->toBe($notes->item(0)->getAttribute('style'))
        ->and($notes->item(1)->getAttribute('style'))->toContain('margin-top:36px');
});

test('a legal strip leaves out the date, sections, button and closing text it does not have', function () {
    $document = renderStrip('juridisch', ['label' => 'Goed om te weten', 'kop' => 'Algemene *voorwaarden*', 'inleiding' => '<p>Tekst.</p>']);
    $strook = $document->querySelector('section.wrap.legal');

    expect($strook?->querySelector('.legal__meta'))->toBeNull()
        ->and($strook?->querySelector('h2'))->toBeNull()
        ->and($strook?->querySelector('button'))->toBeNull()
        ->and($strook?->querySelector('[data-cms-field="afsluiting"]'))->toBeNull()
        ->and(tekstenVan($strook?->querySelectorAll('.prose p')))->toBe(['Tekst.']);
});

test('a legal strip gives every section a heading and its text in order', function () {
    $secties = [
        ['id' => null, 'kop' => 'Noodzakelijk', 'tekst' => '<p>Altijd aan.</p>'],
        ['id' => null, 'kop' => 'Statistieken', 'tekst' => '<p>Met toestemming.</p>'],
    ];

    $document = renderStrip('juridisch', ['label' => 'Label', 'kop' => 'Kop', 'secties' => $secties, 'cookieknop' => 'Instellingen']);
    $prose = $document->querySelector('.legal .prose');

    expect(tekstenVan($prose?->querySelectorAll('[data-cms-items="secties"] h2[data-cms-field="kop"]')))->toBe(['Noodzakelijk', 'Statistieken'])
        ->and(tekstenVan($prose?->querySelectorAll('[data-cms-items="secties"] [data-cms-field="tekst"] p')))->toBe(['Altijd aan.', 'Met toestemming.'])
        ->and($prose?->querySelector('button[data-cookie-open]')?->textContent)->toBe('Instellingen');
});

test('the steps of the acquaintance start again with the first colour after the third', function () {
    $stappen = array_map(fn (int $nummer): array => ['id' => null, 'tekst' => "Stap {$nummer}"], range(1, 4));

    $document = renderStrip('hero-kennismaken', ['kop' => 'Kop', 'stappen' => $stappen]);
    $nummers = $document->querySelectorAll('ol.mini-steps[data-cms-items="stappen"] > li > span');

    expect(tekstenVan($nummers))->toBe(['1', '2', '3', '4'])
        ->and($nummers->item(3)->getAttribute('style'))->toBe($nummers->item(0)->getAttribute('style'))
        ->and($nummers->item(1)->getAttribute('style'))->not->toBe($nummers->item(0)->getAttribute('style'));
});

test('the acquaintance hero leaves out the remark it does not have', function () {
    $document = renderStrip('hero-kennismaken', ['kop' => 'Kop', 'stappen' => [], 'opmerking' => '']);

    expect($document->querySelector('[data-cms-field="opmerking"]'))->toBeNull();
});

test('the thank-you block leaves out the question it does not have', function () {
    $document = renderStrip('checklist-bedankt', ['kop' => 'Dank *jullie wel*', 'tekst' => 'Tekst.']);

    expect($document->querySelector('[data-cms-field="vraag"]'))->toBeNull()
        ->and($document->querySelector('#bedankt a.btn'))->not->toBeNull();
});

test('a column without a photo from the cms gets the photo of the design for its place', function () {
    metGebouwdeFotos();
    $kolommen = [
        ['id' => 'k1', 'foto' => fotoUitCms(), 'label' => 'Het hart van', 'kop' => '*een goede* vriendin', 'tekst' => 'Tekst'],
        ['id' => 'k2', 'foto' => null, 'label' => 'Het hoofd van', 'kop' => '*een* projectmanager', 'tekst' => 'Tekst'],
    ];

    $document = renderStrip('twee-kolommen', ['kop' => 'Hart *én* hoofd', 'tekst' => 'Tekst', 'kolommen' => $kolommen]);
    $fotos = $document->querySelectorAll('[data-cms-items="kolommen"] img[data-cms-field="foto"]');

    expect(array_map(fn (string $src): string => basename($src), attributenVan($fotos, 'src')))->sequence(
        fn ($src) => $src->toBe('ringen.webp'),
        fn ($src) => $src->toStartWith('feesttafel-'),
    )
        ->and(attributenVan($fotos, 'alt'))->toBe(['Twee gouden trouwringen', 'Lange gedekte feesttafel met bloemen']);
});
