{{--
    Het kruimelpad: links naar de bovenliggende pagina's en als laatste
    de huidige plek als tekst, gescheiden door een middenpunt.

    links: label => url, in volgorde. huidig: de tekst aan het eind.
--}}
@props([
    'links',
    'huidig',
])
<nav aria-label="Kruimelpad" {{ $attributes->class(['crumbs']) }}>@foreach ($links as $label => $url)<a href="{{ $url }}">{{ $label }}</a> · @endforeach{{ $huidig }}</nav>
