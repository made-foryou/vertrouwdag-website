{{--
    Een link die WhatsApp opent met het nummer uit config/vertrouwdag.php
    en een bericht dat alvast klaarstaat. Geef een ander bericht mee met
    tekst="...".
--}}
@props([
    'tekst' => config('vertrouwdag.whatsapp.tekst'),
])
<a href="https://wa.me/{{ config('vertrouwdag.whatsapp.nummer') }}?text={{ rawurlencode($tekst) }}" target="_blank" rel="noopener" {{ $attributes }}>{{ $slot }}</a>
