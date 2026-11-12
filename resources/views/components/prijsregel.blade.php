{{--
    De vanaf-prijs van een dienst: groot inclusief btw, met eronder het
    bedrag exclusief btw. Met gedempt worden de kleine regels grijs of
    lila, zoals op de crème en groene dienstkaarten. Zonder prijs (null)
    rendert de regel niets, zodat een onleesbare prijs in het CMS de
    pagina niet breekt.
--}}
@props([
    'prijs' => null,
    'dienst',
    'gedempt' => false,
])
@if ($prijs !== null)
<p {{ $attributes->class(['price']) }}>vanaf <x-prijs :prijs="$prijs" :dienst="$dienst" /> <small @if ($gedempt) class="muted" @endif>incl. btw</small><span class="incl{{ $gedempt ? ' muted' : '' }}"><x-prijs :prijs="$prijs" :dienst="$dienst" excl /> excl. btw</span></p>
@endif
