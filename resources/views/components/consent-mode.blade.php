{{--
    Google Consent Mode v2, als eerste script in <head>.

    Het eerste script zet alle vier de signalen op denied, vóór elke
    andere gtag-aanroep. De rest is type="text/plain" en draait pas als
    de bezoeker toestemming geeft voor de categorie in data-toestemming:
    resources/js/modules/cookiebanner.js maakt er dan een echt script van,
    en zet data-src om in src. Zo vraagt geen browser iets op bij Google
    zonder toestemming.

    Google Analytics 4 staat alleen in de pagina als vertrouwdag.ga_id
    gevuld is. Wat er gemeten wordt, regelt #26.
--}}
@php
    $gaId = config('vertrouwdag.ga_id');
@endphp
<script>
    window.dataLayer = window.dataLayer || [];
    function gtag() { dataLayer.push(arguments); }
    gtag('consent', 'default', { analytics_storage: 'denied', ad_storage: 'denied', ad_user_data: 'denied', ad_personalization: 'denied' });
</script>
<script type="text/plain" data-toestemming="statistieken">gtag('consent', 'update', { analytics_storage: 'granted' });</script>
<script type="text/plain" data-toestemming="marketing">gtag('consent', 'update', { ad_storage: 'granted', ad_user_data: 'granted', ad_personalization: 'granted' });</script>
@if (filled($gaId))
    <script type="text/plain" data-toestemming="statistieken" data-src="https://www.googletagmanager.com/gtag/js?id={{ urlencode($gaId) }}" async></script>
    <script type="text/plain" data-toestemming="statistieken">gtag('js', new Date()); gtag('config', @js($gaId));</script>
@endif
