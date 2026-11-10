{{--
    De vanaf-prijs van een dienst: groot inclusief btw, met eronder het
    bedrag exclusief btw. Met gedempt worden de kleine regels grijs of
    lila, zoals op de crème en groene dienstkaarten.
--}}
@props([
    'dienst',
    'gedempt' => false,
])
<p {{ $attributes->class(['price']) }}>vanaf <x-prijs :dienst="$dienst" /> <small @if ($gedempt) class="muted" @endif>incl. btw</small><span class="incl{{ $gedempt ? ' muted' : '' }}"><x-prijs :dienst="$dienst" excl /> excl. btw</span></p>
