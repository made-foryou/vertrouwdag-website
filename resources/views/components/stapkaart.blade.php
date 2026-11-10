{{--
    Een genummerde stap als gekleurde kaart, voor in een <ol class="steps">.

    kleur: lila, mos, zand of groen. De kleuren komen uit het ontwerp.
    De slots titel en tekst geven hun attributen door aan hun element,
    bijvoorbeeld data-cms-field.
--}}
@props([
    'nummer',
    'kleur',
    'titel',
    'tekst',
])
@php
    $stijl = match ($kleur) {
        'lila' => ['kaart' => 'background:#E0D0E0;color:#401838', 'nummer' => null, 'tekst' => 'color:#2d331f'],
        'mos' => ['kaart' => 'background:#E3E6D8', 'nummer' => null, 'tekst' => null],
        'zand' => ['kaart' => 'background:#EFE9DC', 'nummer' => null, 'tekst' => null],
        'groen' => ['kaart' => 'background:#404830;color:#F6F6F0', 'nummer' => 'color:#E0D0E0', 'tekst' => null],
    };
@endphp
<li {{ $attributes->class(['step'])->merge(['style' => $stijl['kaart']]) }}><span class="step__nr" @if ($stijl['nummer']) style="{{ $stijl['nummer'] }}" @endif>{{ $nummer }}</span><h3 {{ $titel->attributes }}>{{ $titel }}</h3><p {{ $stijl['tekst'] ? $tekst->attributes->merge(['style' => $stijl['tekst']]) : $tekst->attributes }}>{{ $tekst }}</p></li>
