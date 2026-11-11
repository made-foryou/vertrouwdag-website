{{--
    Het blogarchief, opgebouwd uit stroken (resources/views/strips). De
    inhoud staat hier vast in de vorm die het CMS straks levert: per
    strook een Strip met zijn velden. De blogs komen uit
    App\Support\Blogs en staan als extra data op hun strook, zoals with()
    van een striptype dat later doet. In #20 komen de blogs uit het CMS
    en verdwijnt dit blok.
--}}
@use('App\Support\Blogs')
@use('App\Support\PaginaSeo')
@use('App\Support\Strip')
@php
    $strips = [
        new Strip('hero-archief', 'hero-archief', [
            'label' => 'Blogarchief',
            'kop' => 'Alle *blogs*',
            'tekst' => 'Alles wat ik schreef over rustig plannen, mooie plekken in Zuid-Holland en het werk achter de schermen.',
        ]),
        new Strip('blog-archief', 'blog-archief', [
            'kop' => 'Alle artikelen',
            'leeg' => 'Over dit onderwerp heb ik nog geen blog geschreven. Binnenkort meer!',
            'knop' => ['text' => 'Terug naar de blog', 'url' => route('blog')],
            'blogs' => Blogs::gepubliceerd(),
        ]),
        new Strip('cta', 'cta', [
            'kop' => 'Liever direct *persoonlijk advies?*',
            'variant' => 'compact',
        ]),
    ];
@endphp
<x-layouts::site :seo="PaginaSeo::voorAdres('/blog/archief')">
    <x-strips :strips="$strips" />
</x-layouts::site>
