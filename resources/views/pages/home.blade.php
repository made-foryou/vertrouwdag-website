{{--
    De homepage, opgebouwd uit stroken (resources/views/strips). De inhoud
    staat hier vast in de vorm die het CMS straks levert: per strook een
    Strip met zijn velden, en een lijst als array van items. In #16 komt
    deze inhoud uit het CMS en verdwijnt dit blok.

    De reviews zijn nog placeholders. De echte inhoud komt in #27.
--}}
@use('App\Support\PaginaSeo')
@use('App\Support\Strip')
@php
    $strips = [
        new Strip('hero', 'hero', [
            'label' => 'Weddingplanner in Zuid-Holland',
            'kop' => 'Jullie dag in *vertrouwde* handen',
            'tekst' => 'Jullie werken allebei hard. Ik neem het overzicht over, zodat jullie en jullie familie op de dag zelf alleen maar hoeven te genieten.',
            'knop' => ['text' => 'Bekijk de diensten', 'url' => route('diensten')],
            'bijschrift' => 'ja, wij doen het',
            'sticker' => "met\nhart en\nhoofd",
        ]),
        new Strip('lopende-band', 'lopende-band', [
            'teksten' => [
                ['id' => null, 'tekst' => 'Jullie dag in vertrouwde handen'],
                ['id' => null, 'tekst' => 'Rust, aandacht en overzicht'],
                ['id' => null, 'tekst' => 'Ik heb het overzicht, jullie genieten'],
                ['id' => null, 'tekst' => 'Weddingplanner in Zuid-Holland'],
            ],
        ]),
        new Strip('herkenning', 'herkenning', [
            'label' => 'Herkennen jullie dit?',
            'kop' => 'Klinkt dit *een beetje* als jullie?',
            'citaten' => [
                ['id' => null, 'tekst' => '"We hebben na het werk gewoon geen tijd om alles uit te zoeken."'],
                ['id' => null, 'tekst' => '"Mijn moeder wil helpen, maar ik wil juist dat zij geniet."'],
                ['id' => null, 'tekst' => '"Drie offertes, vijf tabbladen en nog steeds geen overzicht."'],
                ['id' => null, 'tekst' => '"Wie belt de bloemist als er op de dag iets misgaat?"'],
            ],
            'slotzin' => 'Jullie hoeven op jullie dag niets meer te regelen, en jullie familie ook niet.',
        ]),
        new Strip('twee-kolommen', 'twee-kolommen', [
            'kop' => 'Hart *én* hoofd',
            'tekst' => 'Warm en persoonlijk, en tegelijk strak georganiseerd. Die combinatie geeft jullie rust, van het eerste gesprek tot de laatste dans.',
            'kolommen' => [
                ['id' => null, 'label' => 'Het hart van', 'kop' => '*een goede* vriendin', 'tekst' => 'Ik denk met jullie mee zoals een zus dat zou doen. Eerlijk, warm en met oog voor wat jullie echt belangrijk vinden.'],
                ['id' => null, 'label' => 'Het hoofd van', 'kop' => '*een* projectmanager', 'tekst' => 'Als General Manager stuur ik al jaren projecten met deadlines, budgetten en veel partijen. Punctueel, tot in de details.'],
            ],
        ]),
        new Strip('diensten', 'diensten', [
            'label' => 'Diensten',
            'kop' => 'Mijn *diensten*',
            'diensten' => [
                ['id' => null, 'dienst' => 'dag', 'soort' => 'Dagcoördinatie', 'titel' => 'De Dag in handen', 'omschrijving' => 'Jullie plannen, ik neem het zes weken ervoor over en ben op de dag het aanspreekpunt.'],
                ['id' => null, 'dienst' => 'partial', 'soort' => 'Gedeeltelijke planning', 'titel' => 'Hand in hand', 'omschrijving' => 'Locatie staat al? Ik help met leveranciers, planning en budget, en coördineer de dag.'],
                ['id' => null, 'dienst' => 'full', 'soort' => 'Full service', 'titel' => 'Helemaal in handen', 'omschrijving' => 'Van de eerste locatiebezichtiging tot de dag erna. Jullie kiezen, ik regel de rest.'],
            ],
            'knop' => ['text' => 'Alle diensten en wat erin zit', 'url' => route('diensten')],
        ]),
        new Strip('stappen', 'stappen', [
            'label' => 'Werkwijze',
            'kop' => 'Vier stappen *naar ja*',
            'knop' => ['text' => 'Zo werk ik', 'url' => route('werkwijze')],
            'stappen' => [
                ['id' => null, 'titel' => 'Kennismaken', 'tekst' => 'Een half uur online. Jullie vertellen, ik luister.'],
                ['id' => null, 'titel' => 'Voorstel', 'tekst' => 'Binnen een week, helder en met een vaste prijs.'],
                ['id' => null, 'titel' => 'Samen plannen', 'tekst' => 'Eén aanspreekpunt voor alle leveranciers.'],
                ['id' => null, 'titel' => 'Genieten', 'tekst' => 'Op jullie dag hoeft niemand iets te regelen.'],
            ],
        ]),
        new Strip('over-mij-intro', 'over-mij-intro', [
            'label' => 'Over mij',
            'kop' => 'Hoi, ik ben *Romy*',
            'tekst' => '<p>Ik begon als stagiaire bij een creatief bureau en werk daar nu als General Manager. Plannen, overzicht houden en op de details letten zit in mijn hoofd en in mijn hart.</p><p>Met vertrouwdag doe ik dat voor jullie. Zodat jullie op jullie dag alleen maar hoeven te genieten.</p>',
            'knop' => ['text' => 'Lees mijn verhaal', 'url' => route('over-mij')],
            'bijschrift' => 'oog voor detail',
        ]),
        new Strip('reviews', 'reviews', [
            'kop' => 'Wat anderen *zeggen*',
            'reviews' => [
                ['id' => null, 'label' => 'Collega uit de trouwbranche', 'citaat' => '"[Tekst review leverancier 1]"', 'naam' => '[Naam], [Bedrijf]'],
                ['id' => null, 'label' => 'Collega uit de trouwbranche', 'citaat' => '"[Tekst review leverancier 2]"', 'naam' => '[Naam], [Bedrijf]'],
            ],
        ]),
        new Strip('leveranciers', 'leveranciers', [
            'label' => 'Voor leveranciers',
            'kop' => 'Samen maken we *mooie dagen*',
            'tekst' => 'Ben jij fotograaf, bloemist, locatie of een andere trouwprofessional in Zuid-Holland? Ik bouw aan een netwerk van leveranciers die ik met een gerust hart aanbeveel aan mijn bruidsparen. Ik maak graag kennis.',
            'knop_tekst' => 'Neem contact op',
            'app_tekst' => 'of stuur een appje',
        ]),
        new Strip('checklist-aanmelding', 'checklist-aanmelding', [
            'label' => 'Gratis checklist',
            'kop' => 'Wat regel je *wanneer?*',
            'tekst' => 'De checklist die ik zelf gebruik, van twaalf maanden ervoor tot de week zelf.',
        ]),
        new Strip('cta', 'cta', [
            'kop' => 'Zullen we *kennismaken?*',
            'tekst' => 'Een half uur, online en vrijblijvend. Jullie vertellen over jullie plannen, ik vertel hoe ik kan helpen.',
        ]),
    ];

    $bedrijf = [
        '@context' => 'https://schema.org',
        '@type' => 'ProfessionalService',
        'name' => config('vertrouwdag.bedrijf.naam'),
        'description' => config('vertrouwdag.bedrijf.omschrijving'),
        'url' => route('home'),
        'email' => config('vertrouwdag.email'),
        'telephone' => '+'.config('vertrouwdag.whatsapp.nummer'),
        'areaServed' => config('vertrouwdag.bedrijf.werkgebied'),
        'image' => Vite::asset('resources/images/logo-groen.png'),
        'sameAs' => [config('vertrouwdag.instagram')],
    ];
@endphp
<x-layouts::site :seo="PaginaSeo::voorAdres('/')">
    @push('jsonld')
        <x-json-ld :data="$bedrijf" />
    @endpush

    <x-strips :strips="$strips" />
</x-layouts::site>
