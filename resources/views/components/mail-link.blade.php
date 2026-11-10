{{--
    Een mailto-link naar het adres uit config/vertrouwdag.php, met een
    onderwerp dat alvast is ingevuld. Geef een ander onderwerp mee met
    onderwerp="...", of onderwerp="" voor geen onderwerp.

    De link mag midden in een zin staan: hij eindigt zonder witruimte.
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
<a href="{{ $href }}" {{ $attributes }}>{{ $slot }}</a>@php
    // Leeg blok: PHP slikt de regelovergang na de afsluitende tag in, zodat er geen spatie tussen de link en een leesteken erna komt.
@endphp
