{{--
    Een link die WhatsApp opent met het nummer uit config/vertrouwdag.php
    en een bericht dat alvast klaarstaat. Geef een ander bericht mee met
    tekst="...".

    De link mag midden in een zin staan: hij eindigt zonder witruimte.
--}}
@props([
    'tekst' => config('vertrouwdag.whatsapp.tekst'),
])
<a href="https://wa.me/{{ config('vertrouwdag.whatsapp.nummer') }}?text={{ rawurlencode($tekst) }}" target="_blank" rel="noopener" {{ $attributes }}>{{ $slot }}</a>@php
    // Leeg blok: PHP slikt de regelovergang na de afsluitende tag in, zodat er geen spatie tussen de link en een leesteken erna komt.
@endphp
