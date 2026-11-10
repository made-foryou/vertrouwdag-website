{{--
    De lopende band: korte teksten met hartjes ertussen. Elke tekst staat
    er één keer; resources/js/modules/lopende-band.js vult de band aan
    tot hij vol is en laat hem naadloos doorlopen.
--}}
@props([
    'teksten',
])
<div {{ $attributes->class(['marquee']) }} aria-hidden="true">
    <div class="marquee__track">
        @foreach ($teksten as $tekst)<span>{{ $tekst }}</span><svg width="14" height="13" fill="#401838"><use href="#heart"/></svg>@endforeach
    </div>
</div>
