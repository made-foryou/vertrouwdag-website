{{--
    Over mij, opgebouwd uit stroken (resources/views/strips). De inhoud
    staat hier vast in de vorm die het CMS straks levert: per strook een
    Strip met zijn velden, en een lijst als array van items. In #17 komt
    deze inhoud uit het CMS en verdwijnt dit blok.

    De tekst van "Van stagiaire tot planner" is nog Lorem ipsum, zoals in
    het ontwerp. De echte tekst levert Romy aan in #27.
--}}
@use('App\Support\Strip')
@php
    $strips = [
        new Strip('hero-over-mij', 'hero-over-mij', [
            'label' => 'Over mij',
            'kop' => 'Hoi, ik ben *Romy*',
            'tekst' => 'De planner achter vertrouwdag. Ik woon in Zuid-Holland, houd van een goed gedekte tafel en word blij van een planning die klopt.',
            'knop' => ['text' => 'Maak kennis met mij', 'url' => route('kennismaken')],
            'bijschrift' => 'hier word ik blij van',
        ]),
        new Strip('lopende-band', 'lopende-band', [
            'teksten' => [
                ['id' => null, 'tekst' => 'Punctueel'],
                ['id' => null, 'tekst' => 'Oog voor detail'],
                ['id' => null, 'tekst' => 'Vanuit het hart'],
                ['id' => null, 'tekst' => 'Thuis in Zuid-Holland'],
            ],
        ]),
        new Strip('verhaal', 'verhaal', [
            'kop' => 'Van stagiaire *tot planner*',
            'tekst' => '<p>Lorem ipsum dolor sit amet, consectetur adipiscing elit. Integer posuere erat a ante venenatis dapibus. Curabitur blandit tempus porttitor, vestibulum id ligula porta felis euismod semper. Nullam quis risus eget urna mollis ornare vel eu leo.</p><p>Donec ullamcorper nulla non metus auctor fringilla. Maecenas faucibus mollis interdum, cras mattis consectetur purus sit amet fermentum. Aenean lacinia bibendum nulla sed consectetur, sed posuere consectetur est at lobortis.</p>',
        ]),
        new Strip('kijkje', 'kijkje', [
            'label' => 'Even voorstellen',
            'kop' => 'Een kijkje *achter de planner*',
            'tekst' => 'Bruiloften hebben me altijd al geraakt, dat zie je wel aan die sluier. En als ik niet met jullie dag bezig ben, zoek ik de rust graag op, het liefst samen aan zee.',
            'bijschrift_links' => 'al vroeg in het wit',
            'bijschrift_rechts' => 'samen aan zee',
        ]),
        new Strip('citaat', 'citaat', [
            'kop' => 'Kiezen voor wat mijn hart al lang zei',
            'citaat' => 'Na een pittige periode heb ik bewust stilgestaan bij wat ik echt wil. Het antwoord was er al heel lang: stellen helpen om hun dag zonder zorgen te beleven.',
        ]),
        new Strip('kwaliteiten', 'kwaliteiten', [
            'label' => 'Waarom vertrouwdag',
            'kop' => 'Vertrouwd, *zoals een zus* of goede vriendin',
            'tekst' => 'Iemand die jullie wensen kent, eerlijk meedenkt en op de dag zelf alles opvangt. Zodat jullie, en jullie familie, er gewoon kunnen zijn.',
            'kwaliteiten' => [
                ['id' => null, 'titel' => 'Punctueel', 'tekst' => 'Afspraak is afspraak, ook met leveranciers.'],
                ['id' => null, 'titel' => 'Oog voor detail', 'tekst' => 'Van naamkaartjes tot de looproute van de ceremonie.'],
                ['id' => null, 'titel' => 'Lokaal', 'tekst' => 'Thuis in Zuid-Holland, van Rotterdam tot de Hoeksche Waard.'],
            ],
        ]),
        new Strip('cta', 'cta', [
            'kop' => 'Ik maak graag *kennis*',
            'tekst' => 'Vertel me over jullie plannen. Een half uur, online en vrijblijvend.',
            'variant' => 'eenvoudig',
        ]),
    ];
@endphp
<x-layouts::site
    title="Over Romy | Persoonlijke weddingplanner in Zuid-Holland"
    description="Ik ben Romy, de weddingplanner achter vertrouwdag. Punctueel, met oog voor detail en vanuit het hart, zodat jullie op jullie trouwdag alleen maar hoeven te genieten."
    og-title="Over Romy · vertrouwdag"
    og-image="resources/images/romy.jpg"
>
    <x-strips :strips="$strips" />
</x-layouts::site>
