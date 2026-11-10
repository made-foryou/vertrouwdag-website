{{--
    Een mailto-link naar het adres uit config/vertrouwdag.php, met een
    onderwerp dat alvast is ingevuld. Geef een ander onderwerp mee met
    onderwerp="...", of onderwerp="" voor geen onderwerp.
--}}
@props([
    'onderwerp' => config('vertrouwdag.email_onderwerp'),
])
@php
    $href = 'mailto:'.config('vertrouwdag.email');

    if (filled($onderwerp)) {
        $href .= '?subject='.rawurlencode($onderwerp);
    }
@endphp
<a href="{{ $href }}" {{ $attributes }}>{{ $slot }}</a>
