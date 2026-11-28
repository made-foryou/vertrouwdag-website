{{--
    Een briefje: een gekleurd, scheef papiertje met een citaat of korte
    tekst. De inhoud (icoon, label, kop, tekst) verschilt per pagina en
    gaat in de slot.

    kleur: lila, cream, mos of zand. draaiing in graden.
    Met href is het een link zonder .tilt (hij gaat bij hover niet
    rechtop staan), zoals de contactbriefjes op de kennismaakpagina.
--}}
@props([
    'kleur',
    'draaiing' => 0,
    'href' => null,
])
@php
    // A strip that passes no style of its own for some notes passes null; merged as is, that would add "; ;".
    $attributes = $attributes->filter(fn (mixed $value, string $key): bool => $key !== 'style' || filled($value));
    $achtergrond = match ($kleur) {
        'lila' => 'background:#E0D0E0;color:#401838',
        'cream' => 'background:#F6F6F0',
        'mos' => 'background:#E3E6D8',
        'zand' => 'background:#EFE9DC',
        default => throw new \InvalidArgumentException("Het briefje kent geen kleur [{$kleur}]; kies lila, cream, mos of zand."),
    };
@endphp
@if ($href !== null)
<a href="{{ $href }}" {{ $attributes->class(['note'])->merge(['style' => "{$achtergrond};transform:rotate({$draaiing}deg)"]) }}>{{ $slot }}</a>
@else
<div {{ $attributes->class(['note', 'tilt'])->merge(['style' => "--r:{$draaiing}deg;{$achtergrond};transform:rotate({$draaiing}deg)"]) }}>{{ $slot }}</div>
@endif
