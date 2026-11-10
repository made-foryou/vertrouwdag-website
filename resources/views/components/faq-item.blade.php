{{--
    Een veelgestelde vraag die openklapt. Het antwoord is één alinea en
    mag links en prijzen bevatten. Met open staat hij al uitgeklapt.
    De slots vraag en antwoord geven hun attributen door aan hun element,
    bijvoorbeeld data-cms-field.
--}}
@props([
    'open' => false,
    'vraag',
    'antwoord',
])
<details {{ $attributes->class(['faq']) }} @if ($open) open @endif><summary {{ $vraag->attributes }}>{{ $vraag }}</summary><p {{ $antwoord->attributes }}>{{ $antwoord }}</p></details>
