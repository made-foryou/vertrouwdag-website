{{--
    Een veelgestelde vraag die openklapt. Met open staat hij al uitgeklapt.
    De vraag is een h3 in de summary, met de grootte van .h4.

    Het antwoord is opgemaakte tekst met zijn eigen alinea's (<p>), zoals
    RichText van het package hem levert, en mag links bevatten. Het staat
    in een element met display:contents, zodat de alinea's de opmaak van
    .faq p houden.
    Met boven staat er een alinea vóór het antwoord, buiten het element van
    het antwoord, zoals de zin met de prijzen uit de strook faq. Die is
    opgemaakte tekst en wordt ongeëscapet geprint.
    De slots vraag en antwoord geven hun attributen door aan hun element,
    bijvoorbeeld data-cms-field.
--}}
@props([
    'open' => false,
    'boven' => null,
    'vraag',
    'antwoord',
])
<details {{ $attributes->class(['faq']) }} @if ($open) open @endif><summary><h3 {{ $vraag->attributes->class(['h4']) }}>{{ $vraag }}</h3></summary>@if (filled($boven))<p>{!! $boven !!}</p>@endif<div style="display:contents" {{ $antwoord->attributes }}>{{ $antwoord }}</div></details>
