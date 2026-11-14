{{--
    Een pagina uit het CMS (Route::madeCms in routes/web.php): de layout
    van de site met de stroken van de pagina. Elke strook rendert met zijn
    view in resources/views/strips.

    Titel en omschrijving komen uit app/Support/PaginaSeo.php: uit de
    vaste lijst op het adres van de pagina, van een artikel uit zijn
    artikelkop, en anders de titel uit het CMS met de algemene
    omschrijving. Kan het CMS dat per pagina, dan vervalt dit (#23).

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
@use('App\Support\PaginaSeo')
@use('Made\CmsPackage\Content\Data\PagePath')
<x-layouts::site :seo="PaginaSeo::voorPagina($page)">
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
