{{--
    De stroken van een pagina, na elkaar. Elke strook rendert met de view
    resources/views/strips/{type}.blade.php, die hem als $strip krijgt.

    Dit doet hetzelfde als <x-made-cms::strips> uit het package. Met de
    CMS-koppeling (#13) neemt dat component het over en blijven de
    strip-views gelijk.
--}}
@props([
    'strips',
])
@foreach ($strips as $strip)
    @include('strips.'.$strip->type, ['strip' => $strip])
@endforeach
