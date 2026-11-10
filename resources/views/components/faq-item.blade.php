{{--
    Een veelgestelde vraag die openklapt. Met open staat hij al uitgeklapt.

    Het antwoord is opgemaakte tekst met zijn eigen alinea's (<p>), zoals
    RichText van het package hem levert, en mag links en prijzen
    bevatten. Het staat in een element met display:contents, zodat de
    alinea's de opmaak van .faq p houden.
    De slots vraag en antwoord geven hun attributen door aan hun element,
    bijvoorbeeld data-cms-field.
--}}
@props([
    'open' => false,
    'vraag',
    'antwoord',
])
<details {{ $attributes->class(['faq']) }} @if ($open) open @endif><summary {{ $vraag->attributes }}>{{ $vraag }}</summary><div style="display:contents" {{ $antwoord->attributes }}>{{ $antwoord }}</div></details>
