{{--
    Het bedrag van een dienst uit config/vertrouwdag.php, standaard
    inclusief btw (€1.028,50). Met excl het bedrag exclusief btw (€850).
    De data-attributen houden de cijferstijl uit de stylesheet.
--}}
@props([
    'dienst',
    'excl' => false,
])
@php
    $prijs = \App\Support\Prijs::voorDienst($dienst);
@endphp
@if ($excl)
<span data-prijs="{{ $dienst }}" {{ $attributes }}>{{ $prijs->exclBtw() }}</span>
@else
<span data-prijs-incl="{{ $dienst }}" {{ $attributes }}>{{ $prijs->inclBtw() }}</span>
@endif
