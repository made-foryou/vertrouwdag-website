{{--
    Een chip voor onderwerpen. Met href een link (de chips op de blog),
    anders een filterknop met aria-pressed (het archief); actief geldt
    alleen voor een filterknop. Geef data-filter mee als attribuut.
--}}
@props([
    'href' => null,
    'actief' => false,
])
@if ($href !== null)
<a href="{{ $href }}" {{ $attributes->class(['chip']) }}>{{ $slot }}</a>
@else
<button type="button" {{ $attributes->class(['chip']) }} aria-pressed="{{ $actief ? 'true' : 'false' }}">{{ $slot }}</button>
@endif
