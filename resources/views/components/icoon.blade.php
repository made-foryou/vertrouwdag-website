{{--
    Een lijnicoon uit App\Support\Icoon. Het is versiering naast een
    tekst, dus schermlezers slaan het over.

    icoon: een App\Support\Icoon. kleur: de kleur van de lijn, die hoort
    bij de achtergrond eronder. grootte in pixels.
--}}
@props([
    'icoon',
    'kleur',
    'grootte' => 24,
])
<svg width="{{ $grootte }}" height="{{ $grootte }}" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="{{ $kleur }}" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round">{!! $icoon->tekening() !!}</svg>
