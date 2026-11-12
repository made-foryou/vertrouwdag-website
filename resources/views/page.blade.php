{{--
    Een pagina uit het CMS (Route::madeCms in routes/web.php): de layout
    van de site met de stroken van de pagina. Elke strook rendert met zijn
    view in resources/views/strips, net als op de vaste pagina's.

    Titel en omschrijving komen uit de vaste lijst in
    app/Support/PaginaSeo.php, op het adres van de pagina. Tot het CMS dat
    per pagina kan (#23), krijgt een pagina buiten die lijst haar titel uit
    het CMS en de algemene omschrijving.

    De homepage beschrijft het bedrijf voor zoekmachines (JSON-LD uit
    App\Support\BedrijfSchema). Dat hangt aan het adres en niet aan een
    strook, zodat het blijft staan als een redacteur de stroken wijzigt.

    In de bewerkmodus van het CMS (?made-cms=edit) is $page ook een
    concept, en renderen de stroken het concept.
--}}
@use('App\Support\BedrijfSchema')
@use('App\Support\PaginaSeo')
@use('Made\CmsPackage\Content\Data\PagePath')
<x-layouts::site :seo="PaginaSeo::voorPagina($page)">
    @if ($page->route?->path === PagePath::HOME)
        @push('jsonld')
            <x-json-ld :data="BedrijfSchema::uitConfig()" />
        @endpush
    @endif

    <x-made-cms::strips :page="$page" />
</x-layouts::site>
