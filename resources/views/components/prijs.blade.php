{{--
    Het bedrag van een prijs (App\Support\Prijs), standaard inclusief btw
    (€1.028,50). Met excl het bedrag exclusief btw (€850). dienst (dag,
    partial of full) komt in het data-attribuut, dat de cijferstijl uit de
    stylesheet houdt.
--}}
@props([
    'prijs',
    'dienst',
    'excl' => false,
])
@if ($excl)
<span data-prijs="{{ $dienst }}" {{ $attributes }}>{{ $prijs->exclBtw() }}</span>
@else
<span data-prijs-incl="{{ $dienst }}" {{ $attributes }}>{{ $prijs->inclBtw() }}</span>
@endif
