{{--
    De kennismaakpagina, opgebouwd uit stroken (resources/views/strips).
    De inhoud staat hier vast in de vorm die het CMS straks levert: per
    strook een Strip met zijn velden, en een lijst als array van items.
    In #18 komt deze inhoud uit het CMS en verdwijnt dit blok.

    De Calendly-agenda hoort bij de strook hero-kennismaken en laadt pas
    na toestemming voor marketing. De Calendly-link staat in de config.
--}}
@use('App\Support\PaginaSeo')
@use('App\Support\Strip')
@php
    $strips = [
        new Strip('hero-kennismaken', 'hero-kennismaken', [
            'label' => 'Kennismaken',
            'kop' => 'Zullen we *kennismaken?*',
            'tekst' => 'Kies een moment dat jullie past. We spreken elkaar online, ongeveer 30 minuten. Vrijblijvend, en gewoon vanaf de bank.',
            'stappen' => [
                ['id' => null, 'tekst' => 'Jullie vertellen over jullie plannen en wat jullie belangrijk vinden.'],
                ['id' => null, 'tekst' => 'Ik stel vragen en vertel hoe ik werk.'],
                ['id' => null, 'tekst' => 'Binnen een week een voorstel. Daarna beslissen jullie, zonder haast.'],
            ],
            'opmerking' => 'Liever fysiek afspreken? Dat kan ook. Laat het weten in het bericht.',
            'bijschrift' => 'tot zo, Romy',
        ]),
        new Strip('contact', 'contact', [
            'kop' => 'Liever eerst *even appen?*',
            'tekst' => 'Dat mag natuurlijk ook. Ik reageer meestal binnen een werkdag.',
            'app_tekst' => '"Hoi Romy, wij gaan trouwen..."',
            'insta_tekst' => 'Stuur een DM naar @vertrouwdag',
        ]),
    ];
@endphp
<x-layouts::site :seo="PaginaSeo::voorAdres('/kennismaken')">
    <x-strips :strips="$strips" />
</x-layouts::site>
