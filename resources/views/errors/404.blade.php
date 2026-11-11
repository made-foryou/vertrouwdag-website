{{--
    De 404: een adres dat niet bestaat, of een blog zonder artikel. De
    pagina staat in de layout van de site, zodat het menu er is en de
    bezoeker verder kan. Zoekmachines slaan haar over (noindex).
--}}
<x-layouts::site
    title="Pagina niet gevonden | vertrouwdag"
    description="Deze pagina bestaat niet (meer). Via de homepage of het menu vinden jullie verder de weg."
    og-title="Pagina niet gevonden · vertrouwdag"
    noindex
>
    <x-foutmelding label="Pagina niet gevonden" kop="Deze pagina is *zoek*">
        Misschien is het adres veranderd, of zit er een typfout in. Geen zorgen, de weg terug is kort.
        <x-slot:knoppen>
            <x-knop :href="url('/')">Naar de homepage</x-knop>
            <x-knop :href="route(config('vertrouwdag.cta.route'))" variant="secundair">{{ config('vertrouwdag.cta.label') }}</x-knop>
        </x-slot:knoppen>
    </x-foutmelding>
</x-layouts::site>
