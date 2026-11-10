{{--
    Gestructureerde gegevens voor zoekmachines (schema.org) als JSON-LD.
    JSON_HEX_TAG schrijft < en > als < en >, zodat geen waarde
    het script kan afsluiten.
--}}
@props([
    'data',
])
<script type="application/ld+json">{!! json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_THROW_ON_ERROR) !!}</script>
