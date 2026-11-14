{{--
    De stroken van een pagina, na elkaar. Elke strook rendert met de view
    resources/views/strips/{type}.blade.php, die hem als $strip krijgt.

    Dit doet hetzelfde als <x-made-cms::strips> uit het package, dat de
    pagina's uit het CMS rendert (page.blade.php). Sinds de blog in het
    CMS staat (#20), komt elke pagina van de site uit het CMS. Dit
    component rendert alleen nog losse stroken in de stijlgids en de tests.
--}}
@props([
    'strips',
])
@foreach ($strips as $strip)
    @include('strips.'.$strip->type, ['strip' => $strip])
@endforeach
