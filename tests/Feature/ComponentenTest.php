<?php

use App\Support\Blog;
use App\Support\Blogcategorie;
use App\Support\Strip;
use Dom\NodeList;
use Illuminate\View\ViewException;

/**
 * @return list<string>
 */
function klassenVan(NodeList $elements): array
{
    $values = [];

    foreach ($elements as $element) {
        $values[] = $element->getAttribute('class');
    }

    return $values;
}

beforeEach(function () {
    $this->withoutVite();
});

describe('knop', function () {
    test('a button with a href is a link with the class of its variant', function (string $variant, string $klassen) {
        $document = renderComponent('<x-knop href="/diensten" :variant="$variant">Bekijk</x-knop>', ['variant' => $variant]);
        $knop = $document->querySelector('a');

        expect($knop?->getAttribute('href'))->toBe('/diensten')
            ->and($knop?->getAttribute('class'))->toBe($klassen);
    })->with([
        ['primair', 'btn'],
        ['secundair', 'btn btn--o'],
        ['lila', 'btn btn--l'],
        ['aubergine', 'btn btn--a'],
    ]);

    test('a button without a href is a button of type button unless another type is given', function () {
        $document = renderComponent('<x-knop klein>Agenda tonen</x-knop><x-knop type="submit">Versturen</x-knop>');
        $knoppen = $document->querySelectorAll('button');

        expect($knoppen->item(0)->getAttribute('type'))->toBe('button')
            ->and($knoppen->item(0)->getAttribute('class'))->toBe('btn btn--sm')
            ->and($knoppen->item(1)->getAttribute('type'))->toBe('submit');
    });
});

describe('dienstkaart', function () {
    test('a service card with a href is a link with the description, price and icon in that order', function () {
        $document = renderComponent(<<<'BLADE'
            <x-dienstkaart kleur="lila" dienst="dag" href="/diensten#dagcoordinatie" foto="/foto.jpg" :draaiing="-4">
                <x-slot:soort>Dagcoördinatie</x-slot:soort>
                <x-slot:titel data-cms-field="titel">De Dag in handen</x-slot:titel>
                <x-slot:omschrijving>Ik neem het zes weken ervoor over.</x-slot:omschrijving>
            </x-dienstkaart>
            BLADE);
        $kaart = $document->querySelector('a.folder');
        $body = $kaart?->querySelector('.folder__body');

        expect($kaart?->getAttribute('href'))->toBe('/diensten#dagcoordinatie')
            ->and($kaart?->querySelector('img.folder__peek')?->getAttribute('style'))->toBe('transform:rotate(-4deg)')
            ->and($body?->querySelector('h3[data-cms-field="titel"]')?->textContent)->toBe('De Dag in handen')
            ->and(klassenVan($document->querySelectorAll('.folder__body > p, .folder__body > svg')))->toBe(['eb eb--sm', 'folder__desc', 'price', 'folder__icon'])
            ->and($body->querySelector('.btn'))->toBeNull();
    });

    test('a service card without a href is an article with the price before the description, the features and a button', function () {
        $document = renderComponent(<<<'BLADE'
            <x-dienstkaart kleur="lila" dienst="dag" foto="/foto.jpg" data-for="dagcoordinatie" :kenmerken="['Draaiboek', 'Afstemming met leveranciers']">
                <x-slot:soort>Dagcoördinatie</x-slot:soort>
                <x-slot:titel>De Dag in handen</x-slot:titel>
                <x-slot:omschrijving><strong>Voor stellen</strong> die zelf plannen.</x-slot:omschrijving>
            </x-dienstkaart>
            BLADE);
        $kaart = $document->querySelector('article.folder[data-for="dagcoordinatie"]');
        $body = $kaart?->querySelector('.folder__body');
        $knop = $body?->querySelector('a.btn');

        expect(klassenVan($document->querySelectorAll('.folder__body > p')))->toBe(['eb eb--sm', 'price', 'folder__desc'])
            ->and(platteTekst($body->querySelector('ul.hl li:last-child')))->toBe('Afstemming met leveranciers')
            ->and($body->querySelector('.folder__icon'))->toBeNull()
            ->and($knop?->getAttribute('href'))->toBe(route(config('vertrouwdag.cta.route')))
            ->and(platteTekst($knop))->toBe(config('vertrouwdag.cta.label'));
    });

    test('the colour of a service card sets the eyebrow, the dimmed price and the button', function (string $kleur, string $eyebrow, int $gedempt, string $knop, string $hart) {
        $document = renderComponent(<<<'BLADE'
            <x-dienstkaart :kleur="$kleur" dienst="dag" foto="/foto.jpg" :kenmerken="['Draaiboek']">
                <x-slot:soort>Soort</x-slot:soort>
                <x-slot:titel>Titel</x-slot:titel>
                <x-slot:omschrijving>Omschrijving</x-slot:omschrijving>
            </x-dienstkaart>
            BLADE, ['kleur' => $kleur]);

        expect($document->querySelector('article')?->getAttribute('class'))->toBe("folder f-{$kleur}")
            ->and($document->querySelector('.folder__body > p')?->getAttribute('class'))->toBe($eyebrow)
            ->and($document->querySelectorAll('.price .muted'))->toHaveCount($gedempt)
            ->and($document->querySelector('ul.hl svg')?->getAttribute('fill'))->toBe($hart)
            ->and($document->querySelector('a.btn')?->getAttribute('class'))->toBe($knop);
    })->with([
        'lila' => ['lila', 'eb eb--sm', 0, 'btn btn--a', '#401838'],
        'cream' => ['cream', 'eb eb--sm eb--grijs', 2, 'btn', '#C8AEC6'],
        'groen' => ['groen', 'eb eb--sm eb--lila', 2, 'btn btn--l', '#E0D0E0'],
    ]);

    test('a mirrored service card has its tab and photo on the right', function () {
        $document = renderComponent(<<<'BLADE'
            <x-dienstkaart kleur="cream" dienst="partial" href="/diensten" foto="/foto.jpg" gespiegeld>
                <x-slot:soort>Soort</x-slot:soort>
                <x-slot:titel>Titel</x-slot:titel>
                <x-slot:omschrijving>Omschrijving</x-slot:omschrijving>
            </x-dienstkaart>
            BLADE);

        expect($document->querySelector('a')?->getAttribute('class'))->toBe('folder folder--r f-cream');
    });

    test('the photo of a service card is decorative, so the link is named by its text only', function () {
        $document = renderComponent(<<<'BLADE'
            <x-dienstkaart kleur="lila" dienst="dag" href="/diensten" foto="/foto.jpg">
                <x-slot:soort>Soort</x-slot:soort>
                <x-slot:titel>Titel</x-slot:titel>
                <x-slot:omschrijving>Omschrijving</x-slot:omschrijving>
            </x-dienstkaart>
            BLADE);

        expect($document->querySelector('a.folder img')?->getAttribute('alt'))->toBe('');
    });
});

describe('stapkaart', function () {
    test('a step card shows its number, title and text in the colours of its kleur', function (string $kleur, string $stijl) {
        $document = renderComponent(<<<'BLADE'
            <x-stapkaart nummer="2" :kleur="$kleur">
                <x-slot:titel data-cms-field="titel">Voorstel</x-slot:titel>
                <x-slot:tekst>Binnen een week.</x-slot:tekst>
            </x-stapkaart>
            BLADE, ['kleur' => $kleur]);
        $stap = $document->querySelector('li.step');

        expect($stap?->getAttribute('style'))->toBe($stijl)
            ->and($stap?->querySelector('.step__nr')?->textContent)->toBe('2')
            ->and($stap?->querySelector('h3[data-cms-field="titel"]')?->textContent)->toBe('Voorstel')
            ->and($stap?->querySelector('p')?->textContent)->toBe('Binnen een week.');
    })->with([
        ['lila', 'background:#E0D0E0;color:#401838'],
        ['mos', 'background:#E3E6D8'],
        ['zand', 'background:#EFE9DC'],
        ['groen', 'background:#404830;color:#F6F6F0'],
    ]);
});

describe('fasenummer', function () {
    test('a phase number takes the colour of its place and starts again after the fourth', function (int $nummer, string $stijl) {
        $document = renderComponent('<x-fasenummer :nummer="$nummer" data-reveal />', ['nummer' => $nummer]);
        $badge = $document->querySelector('div.tl__badge');

        expect($badge?->getAttribute('style'))->toBe($stijl)
            ->and($badge?->hasAttribute('data-reveal'))->toBeTrue();

        expect($badge?->querySelector('span')?->textContent)->toBe((string) $nummer)
            ->and($badge?->querySelector('span')?->hasAttribute('style'))->toBeFalse();
    })->with([
        'lila' => [1, 'background:#E0D0E0;color:#401838'],
        'groen' => [2, 'background:#404830;color:#E0D0E0'],
        'zand' => [3, 'background:#EFE9DC;color:#401838'],
        'mos' => [4, 'background:#E3E6D8;color:#401838'],
        'weer lila' => [5, 'background:#E0D0E0;color:#401838'],
        'weer groen' => [6, 'background:#404830;color:#E0D0E0'],
    ]);

    test('a large phase number is bigger, as on the wedding day band', function () {
        $document = renderComponent('<x-fasenummer :nummer="5" groot />');
        $badge = $document->querySelector('div.tl__badge');

        expect($badge?->getAttribute('style'))->toBe('background:#E0D0E0;color:#401838;width:100px;height:124px')
            ->and($badge?->querySelector('span')?->getAttribute('style'))->toBe('font-size:54px');
    });

    test('a phase number below one is rejected', function () {
        expect(fn () => $this->blade('<x-fasenummer :nummer="0" />'))
            ->toThrow(ViewException::class, 'Het fasenummer moet 1 of hoger zijn, niet [0]');
    });
});

describe('briefje', function () {
    test('a note is tilted by its rotation and keeps extra styles after its own', function () {
        $document = renderComponent('<x-briefje kleur="mos" :draaiing="3" style="min-height:190px"><p>Tekst</p></x-briefje>');
        $briefje = $document->querySelector('div');

        expect($briefje?->getAttribute('class'))->toBe('note tilt')
            ->and($briefje?->getAttribute('style'))->toBe('--r:3deg;background:#E3E6D8;transform:rotate(3deg); min-height:190px;');
    });

    test('a note with a href is a link that does not straighten on hover', function () {
        $document = renderComponent('<x-briefje kleur="lila" :draaiing="-4" href="mailto:romy@vertrouwdag.nl">Mail</x-briefje>');
        $briefje = $document->querySelector('a');

        expect($briefje?->getAttribute('class'))->toBe('note')
            ->and($briefje?->getAttribute('style'))->toBe('background:#E0D0E0;color:#401838;transform:rotate(-4deg)');
    });
});

describe('review', function () {
    test('a review shows only the parts it is given', function () {
        $document = renderComponent(<<<'BLADE'
            <x-review>
                <x-slot:label>Collega uit de trouwbranche</x-slot:label>
                <x-slot:citaat data-cms-field="citaat">"Fijn samengewerkt."</x-slot:citaat>
                <x-slot:naam>Sanne, Bloemenwinkel</x-slot:naam>
            </x-review>
            BLADE);
        $review = $document->querySelector('figure');

        expect($review?->getAttribute('class'))->toBe('review')
            ->and($review?->querySelector('.review__label')?->textContent)->toBe('Collega uit de trouwbranche')
            ->and($review?->querySelector('blockquote[data-cms-field="citaat"]')?->textContent)->toBe('"Fijn samengewerkt."')
            ->and($review?->querySelector('figcaption')?->textContent)->toBe('Sanne, Bloemenwinkel')
            ->and($review?->querySelector('p.eb, img'))->toBeNull();
    });

    test('a large review is tilted and can carry the monogram', function () {
        $document = renderComponent(<<<'BLADE'
            <x-review groot zegel>
                <x-slot:kop>Kiezen voor wat mijn hart al lang zei</x-slot:kop>
                <x-slot:citaat>Het antwoord was er al heel lang.</x-slot:citaat>
            </x-review>
            BLADE);
        $review = $document->querySelector('figure');

        expect($review?->getAttribute('class'))->toBe('review review--groot')
            ->and($review?->getAttribute('style'))->toBe('transform:rotate(-1.5deg)')
            ->and($review?->querySelector('p.eb')?->textContent)->toBe('Kiezen voor wat mijn hart al lang zei')
            ->and($review?->querySelector('img.review__seal')?->getAttribute('alt'))->toBe('')
            ->and($review?->querySelector('figcaption'))->toBeNull();
    });
});

describe('faq-item', function () {
    test('a question is only open when asked', function (bool $open) {
        $document = renderComponent(<<<'BLADE'
            <x-faq-item :open="$open">
                <x-slot:vraag>Wat kost een weddingplanner?</x-slot:vraag>
                <x-slot:antwoord><p>Bekijk <a href="/diensten">de diensten</a>.</p></x-slot:antwoord>
            </x-faq-item>
            BLADE, ['open' => $open]);
        $vraag = $document->querySelector('details.faq');

        expect($vraag?->hasAttribute('open'))->toBe($open)
            ->and($vraag?->querySelector('summary')?->textContent)->toBe('Wat kost een weddingplanner?')
            ->and($vraag?->querySelector('p a')?->getAttribute('href'))->toBe('/diensten');
    })->with([
        'open' => true,
        'dicht' => false,
    ]);
});

describe('kruimelpad', function () {
    test('the breadcrumb links to the pages above in order and ends with the current place as text', function () {
        $document = renderComponent('<x-kruimelpad :links="[\'Home\' => \'/\', \'Blog\' => \'/blog\']" huidig="Archief" data-reveal />');
        $kruimelpad = $document->querySelector('nav.crumbs[aria-label="Kruimelpad"]');
        $links = $kruimelpad?->querySelectorAll('a');

        expect(platteTekst($kruimelpad))->toBe('Home · Blog · Archief')
            ->and($links)->toHaveCount(2)
            ->and($links->item(1)->getAttribute('href'))->toBe('/blog')
            ->and($kruimelpad?->hasAttribute('data-reveal'))->toBeTrue();
    });
});

describe('chip', function () {
    test('a chip with a href is a link without a pressed state', function () {
        $document = renderComponent('<x-chip href="/blog/archief#planning">Planning</x-chip>');
        $chip = $document->querySelector('a.chip');

        expect($chip?->getAttribute('href'))->toBe('/blog/archief#planning')
            ->and($chip?->hasAttribute('aria-pressed'))->toBeFalse();
    });

    test('a chip without a href is a filter button that is pressed when active', function () {
        $document = renderComponent('<x-chip actief data-filter="alles">Alles</x-chip><x-chip data-filter="budget">Budget</x-chip>');
        $chips = $document->querySelectorAll('button.chip[type="button"]');

        expect($chips->item(0)->getAttribute('aria-pressed'))->toBe('true')
            ->and($chips->item(0)->getAttribute('data-filter'))->toBe('alles')
            ->and($chips->item(1)->getAttribute('aria-pressed'))->toBe('false');
    });
});

describe('blogkaart', function () {
    test('a published blog is a link to its article with its topic, title and intro', function () {
        $blog = new Blog('Wanneer *begin* je?', 'Een vroeg begin geeft rust.', Blogcategorie::Planning, 6, '/foto.jpg', 'Bruidspaar bij een meer', 'wanneer');

        $document = renderComponent('<x-blogkaart :blog="$blog" />', ['blog' => $blog]);
        $kaart = $document->querySelector('a.pol');

        expect($kaart?->getAttribute('href'))->toBe(route('blog.artikel', 'wanneer'))
            ->and($kaart?->getAttribute('data-cat'))->toBe('planning')
            ->and($kaart?->hasAttribute('data-post'))->toBeTrue()
            ->and($kaart?->classList->contains('pol--soon'))->toBeFalse()
            ->and($kaart?->querySelector('img')?->getAttribute('alt'))->toBe('Bruidspaar bij een meer')
            ->and($kaart?->querySelector('.pol__txt .eb')?->textContent)->toBe('Planning · 6 min')
            ->and($kaart?->querySelector('h3 em')?->textContent)->toBe('begin')
            ->and($kaart?->querySelector('.pol__txt p')?->textContent)->toBe('Een vroeg begin geeft rust.');
    });

    test('a blog to come is a card without a link and with a duller photo', function () {
        $blog = new Blog('Locaties', 'Binnenkort.', Blogcategorie::Locaties, null, '/foto.jpg', 'Foto', 'locaties', binnenkort: true);

        $document = renderComponent('<x-blogkaart :blog="$blog" />', ['blog' => $blog]);

        expect($document->querySelector('a'))->toBeNull()
            ->and($document->querySelector('article.pol.pol--soon')?->getAttribute('data-cat'))->toBe('locaties');
    });

    test('the cards tilt in turn and the middle one of every three hangs lower', function (int $positie, string $stijl) {
        $blog = new Blog('Titel', 'Intro', Blogcategorie::Budget, null, '/foto.jpg', 'Foto');

        $document = renderComponent('<x-blogkaart :blog="$blog" :positie="$positie" />', ['blog' => $blog, 'positie' => $positie]);

        expect($document->querySelector('.pol')?->getAttribute('style'))->toBe($stijl);
    })->with([
        [0, 'transform:rotate(-2.5deg)'],
        [1, 'transform:rotate(2deg);margin-top:28px'],
        [2, 'transform:rotate(-1.5deg)'],
        [3, 'transform:rotate(-2.5deg)'],
    ]);
});

describe('cta-split', function () {
    test('the closing block links to the meeting from the config and slides in', function () {
        $document = renderComponent(<<<'BLADE'
            <x-cta-split data-cms-strip="12">
                <x-slot:kop data-cms-field="kop">Nog een <em>vraag?</em></x-slot:kop>
            </x-cta-split>
            BLADE);
        $blok = $document->querySelector('section.cta-split[data-cms-strip="12"]');
        $knop = $blok?->querySelector('.cta-split__txt a.btn.btn--l');

        expect($blok?->querySelector('.cta-split__txt')?->hasAttribute('data-reveal-inner'))->toBeTrue()
            ->and($blok?->querySelector('h2[data-cms-field="kop"] em')?->textContent)->toBe('vraag?')
            ->and($blok?->querySelector('.cta-split__txt p, .cta-split__txt svg'))->toBeNull()
            ->and($blok?->querySelector('.cta-split__photo')?->hasAttribute('style'))->toBeFalse()
            ->and($knop?->getAttribute('href'))->toBe(route(config('vertrouwdag.cta.route')))
            ->and(platteTekst($knop))->toBe(config('vertrouwdag.cta.label'));
    });

    test('the large closing block has a taller photo, a heart, a text and a bigger heading', function () {
        $document = renderComponent(<<<'BLADE'
            <x-cta-split :foto-hoogte="480" hart groot>
                <x-slot:kop>Twijfelen jullie <em>welk pakket?</em></x-slot:kop>
                <x-slot:tekst>Dat zoeken we samen uit.</x-slot:tekst>
            </x-cta-split>
            BLADE);

        expect($document->querySelector('.cta-split__photo')?->getAttribute('style'))->toBe('min-height:480px')
            ->and($document->querySelector('.cta-split__txt > svg'))->not->toBeNull()
            ->and($document->querySelector('.cta-split__txt > p')?->textContent)->toBe('Dat zoeken we samen uit.')
            ->and($document->querySelector('.cta-split__txt h2')?->getAttribute('style'))->toBe('font-size:clamp(40px,5vw,68px)');
    });
});

describe('lopende-band', function () {
    test('the marquee shows every text once, hidden from screen readers', function () {
        $document = renderComponent('<x-lopende-band :teksten="[\'Punctueel\', \'Oog voor detail\']" />');
        $band = $document->querySelector('.marquee[aria-hidden="true"] .marquee__track');

        expect($band?->querySelectorAll('span'))->toHaveCount(2)
            ->and($band?->querySelector('span:last-of-type')?->textContent)->toBe('Oog voor detail')
            ->and($band?->querySelectorAll('svg'))->toHaveCount(2);
    });
});

describe('stapel-bolletjes', function () {
    test('the deck dots have one dot per card with the first one on', function () {
        $document = renderComponent('<x-stapel-bolletjes :aantal="3" licht />');
        $bolletjes = $document->querySelector('.deck-dots.deck-dots--licht[aria-hidden="true"]');

        expect($bolletjes?->querySelectorAll('span'))->toHaveCount(3)
            ->and($bolletjes?->querySelectorAll('span.is-on'))->toHaveCount(1)
            ->and($bolletjes?->firstElementChild?->getAttribute('class'))->toBe('is-on');
    });
});

describe('accent', function () {
    test('the words between asterisks become the accent of a heading', function () {
        $document = renderComponent('<h1><x-accent tekst="Jullie dag in *vertrouwde* handen" /></h1>');
        $kop = $document->querySelector('h1');

        expect($kop?->innerHTML)->toBe('Jullie dag in <em>vertrouwde</em> handen');
    });

    test('a heading without asterisks stays plain text and html in it is escaped', function () {
        $document = renderComponent('<h2><x-accent tekst="Hart & <b>hoofd</b>" /></h2>');
        $kop = $document->querySelector('h2');

        expect($kop?->textContent)->toBe('Hart & <b>hoofd</b>')
            ->and($kop?->querySelector('b, em'))->toBeNull();
    });

    test('a heading can have more than one accent', function () {
        $document = renderComponent('<h2><x-accent tekst="*Hart* én *hoofd*" /></h2>');

        expect(tekstenVan($document->querySelectorAll('h2 em')))->toBe(['Hart', 'hoofd']);
    });
});

describe('strips', function () {
    test('every strip is rendered with the view of its type, in order', function () {
        $strips = [
            new Strip('a', 'cta', ['kop' => 'Zullen we *kennismaken?*', 'tekst' => 'Een half uur.']),
            new Strip('b', 'lopende-band', ['teksten' => [['id' => null, 'tekst' => 'Punctueel']]]),
        ];

        $document = renderComponent('<x-strips :strips="$strips" />', ['strips' => $strips]);

        expect(attributenVan($document->querySelectorAll('[data-cms-strip]'), 'data-cms-strip'))->toBe(['a', 'b'])
            ->and($document->querySelector('[data-cms-strip="a"]')?->getAttribute('class'))->toBe('cta-split')
            ->and($document->querySelector('[data-cms-strip="b"]')?->getAttribute('class'))->toBe('marquee');
    });
});

describe('json-ld', function () {
    test('the structured data is valid json in a script that a value cannot close', function () {
        $data = ['@type' => 'ProfessionalService', 'name' => 'vertrouwdag</script><script>alert(1)</script>', 'url' => 'https://vertrouwdag.nl/'];

        $document = renderComponent('<x-json-ld :data="$data" />', ['data' => $data]);
        $scripts = $document->querySelectorAll('script');

        expect($scripts)->toHaveCount(1)
            ->and($scripts->item(0)->getAttribute('type'))->toBe('application/ld+json')
            ->and(json_decode($scripts->item(0)->textContent, true))->toBe($data);
    });
});

describe('checklist-formulier', function () {
    test('the form asks for a first name, an email address and consent, and shows a thank you note', function () {
        $document = renderComponent('<x-checklist-formulier />');
        $formulier = $document->querySelector('form#checklist-form[method="post"]');

        expect(attributenVan($formulier?->querySelectorAll('input[required]'), 'name'))->toBe(['voornaam', 'email', 'avg'])
            ->and($formulier?->querySelector('input[name="email"]')?->getAttribute('type'))->toBe('email')
            ->and($formulier?->querySelector('input[name="trouwdatum"]'))->toBeNull()
            ->and($formulier?->querySelector('.check a')?->getAttribute('href'))->toBe(route('privacyverklaring'))
            ->and(platteTekst($formulier?->querySelector('.check')))->toBe('Ik ontvang de checklist en af en toe een mail van vertrouwdag. Afmelden kan altijd. Lees de privacyverklaring.')
            ->and($formulier?->querySelector('button[type="submit"]')?->textContent)->toBe('Stuur mij de checklist')
            ->and($formulier?->getAttribute('data-bedankt'))->not->toBeEmpty()
            ->and($formulier?->querySelector('.form-msg[role="status"]')?->hasAttribute('hidden'))->toBeTrue();
    });

    test('the form can ask for an optional wedding date', function () {
        $document = renderComponent('<x-checklist-formulier trouwdatum />');
        $datum = $document->querySelector('input[name="trouwdatum"]');

        expect($datum?->getAttribute('type'))->toBe('date')
            ->and($datum?->hasAttribute('required'))->toBeFalse();
    });

    test('the form starts with the introduction it is given and keeps extra attributes', function () {
        $document = renderComponent('<x-checklist-formulier class="cl-form"><p class="eb">Gratis checklist</p></x-checklist-formulier>');
        $formulier = $document->querySelector('form');

        expect($formulier?->getAttribute('class'))->toBe('cl-form')
            ->and($formulier?->firstElementChild?->textContent)->toBe('Gratis checklist');
    });
});

test('a component rejects an unknown variant or colour and names it', function (string $template, string $melding) {
    expect(fn () => $this->blade($template))->toThrow(ViewException::class, $melding);
})->with([
    'knop' => ['<x-knop variant="roze">Bekijk</x-knop>', 'De knop kent geen variant [roze]'],
    'dienstkaart' => ['<x-dienstkaart kleur="roze" dienst="dag" foto="/foto.jpg" />', 'De dienstkaart kent geen kleur [roze]'],
    'stapkaart' => ['<x-stapkaart nummer="1" kleur="roze" />', 'De stapkaart kent geen kleur [roze]'],
    'briefje' => ['<x-briefje kleur="roze">Tekst</x-briefje>', 'Het briefje kent geen kleur [roze]'],
]);
