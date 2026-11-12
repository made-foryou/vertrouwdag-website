{{--
    De stroken van een pagina, na elkaar. Elke strook rendert met de view
    resources/views/strips/{type}.blade.php, die hem als $strip krijgt.

    Dit doet hetzelfde als <x-made-cms::strips> uit het package, dat de
    pagina's uit het CMS rendert (page.blade.php). Dit component is voor
    de vaste pagina's die nog over zijn, de blog en het artikel: verhuizen
    die naar het CMS (#20), dan neemt dat component het over en blijven de
    strip-views gelijk.
--}}
@props([
    'strips',
])
@foreach ($strips as $strip)
    @include('strips.'.$strip->type, ['strip' => $strip])
@endforeach
