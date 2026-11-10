{{--
    De bolletjes onder een swipe-stapel (data-deck). Ze zijn alleen op
    mobiel zichtbaar; resources/js/modules/swipe-stapel.js laat het
    bolletje van de voorste kaart oplichten. Zet ze direct na de stapel.

    aantal: evenveel als de kaarten in de stapel.
    licht: voor op een lichte achtergrond.
--}}
@props([
    'aantal',
    'licht' => false,
])
<div {{ $attributes->class(['deck-dots', 'deck-dots--licht' => $licht]) }} aria-hidden="true">@for ($i = 0; $i < $aantal; $i++)<span @if ($i === 0) class="is-on" @endif></span>@endfor</div>
