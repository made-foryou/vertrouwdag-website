{{--
    Een pagina uit het CMS (Route::madeCms in routes/web.php): de layout
    van de site met de stroken van de pagina. Elke strook rendert met zijn
    view in resources/views/strips.

    Titel, omschrijving, Open Graph en noindex komen uit het CMS: de
    layout krijgt de pagina mee (#23).

    De homepage beschrijft het bedrijf voor zoekmachines (JSON-LD uit
    App\Support\BedrijfSchema). Dat hangt aan het adres en niet aan een
    strook, zodat het blijft staan als een redacteur de stroken wijzigt.

    Een artikel (een pagina onder /blog met een artikelkop, zie
    App\Support\Blogs) staat in een <article>.

    In de bewerkmodus van het CMS (?made-cms=edit) is $page ook een
    concept, en renderen de stroken het concept.
--}}
@use('App\Support\BedrijfSchema')
@use('App\Support\Blogs')
@use('Made\CmsPackage\Content\Data\PagePath')
<x-layouts::site :page="$page">
    @if ($page->route?->path === PagePath::HOME)
        @push('jsonld')
            <x-json-ld :data="BedrijfSchema::uitConfig()" />
        @endpush
    @endif

    @if (app(Blogs::class)->isArtikel($page))
        <article>
            <x-made-cms::strips :page="$page" />
        </article>
    @else
        <x-made-cms::strips :page="$page" />
    @endif
</x-layouts::site>
