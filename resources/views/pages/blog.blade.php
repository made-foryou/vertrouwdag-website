{{--
    De blog, opgebouwd uit stroken (resources/views/strips). De inhoud
    staat hier vast in de vorm die het CMS straks levert: per strook een
    Strip met zijn velden. De blogs komen uit App\Support\Blogs en staan
    als extra data op hun strook, zoals with() van een striptype dat later
    doet. In #20 komen de blogs uit het CMS en verdwijnt dit blok.
--}}
@use('App\Support\Blogs')
@use('App\Support\PaginaSeo')
@use('App\Support\Strip')
@php
    $strips = [
        new Strip('hero-blog', 'hero-blog', [
            'label' => 'Blog',
            'kop' => 'Rustig *plannen*',
            'tekst' => 'Praktische tips, locaties in Zuid-Holland en een kijkje achter de schermen.',
        ]),
        new Strip('blog-uitgelicht', 'blog-uitgelicht', [
            'label' => 'Nieuwste blog',
            'link_tekst' => 'Lees verder',
            'blog' => Blogs::uitgelicht(),
        ]),
        new Strip('blog-laatste', 'blog-laatste', [
            'knop' => ['text' => 'Bekijk alle blogs in het archief', 'url' => route('blog.archief')],
            'blogs' => Blogs::laatste(),
        ]),
        new Strip('cta', 'cta', [
            'kop' => 'Liever direct *persoonlijk advies?*',
            'variant' => 'compact',
        ]),
    ];
@endphp
<x-layouts::site :seo="PaginaSeo::voorAdres('/blog')">
    <x-strips :strips="$strips" />
</x-layouts::site>
