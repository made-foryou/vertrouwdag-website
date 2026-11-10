<?php

use Dom\Element;
use Dom\NodeList;
use Illuminate\View\ViewException;

/**
 * De tekst van een element met de witruimte samengevoegd tot één spatie.
 */
function platteTekst(?Element $element): ?string
{
    return $element === null ? null : trim(preg_replace('/\s+/', ' ', $element->textContent));
}

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

    test('a button rejects an unknown variant', function () {
        $this->blade('<x-knop variant="roze">Bekijk</x-knop>');
    })->throws(ViewException::class, 'roze');
});

describe('dienstkaart', function () {
    test('a service card with a href is a link with the description, price and icon in that order', function () {
        $document = renderComponent(<<<'BLADE'
            <x-dienstkaart kleur="lila" dienst="dag" href="/diensten#dagcoordinatie" foto="/foto.jpg" alt="Schoenen" :draaiing="-4">
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
            <x-dienstkaart kleur="lila" dienst="dag" foto="/foto.jpg" alt="Schoenen" data-for="dagcoordinatie" :kenmerken="['Draaiboek', 'Afstemming met leveranciers']">
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
            <x-dienstkaart :kleur="$kleur" dienst="dag" foto="/foto.jpg" alt="" :kenmerken="['Draaiboek']">
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
            <x-dienstkaart kleur="cream" dienst="partial" href="/diensten" foto="/foto.jpg" alt="" gespiegeld>
                <x-slot:soort>Soort</x-slot:soort>
                <x-slot:titel>Titel</x-slot:titel>
                <x-slot:omschrijving>Omschrijving</x-slot:omschrijving>
            </x-dienstkaart>
            BLADE);

        expect($document->querySelector('a')?->getAttribute('class'))->toBe('folder folder--r f-cream');
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
                <x-slot:antwoord>Bekijk <a href="/diensten">de diensten</a>.</x-slot:antwoord>
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
