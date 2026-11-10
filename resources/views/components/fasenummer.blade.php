{{--
    Het nummer van een fase in de werkwijze: een boogje met een groot
    schuin cijfer, op een tijdlijnkaart of los op de band.

    nummer: 1 of hoger. De kleur hoort bij het nummer (lila, groen, zand,
    mos) en begint na de vierde opnieuw, zodat de fases over meerdere
    stroken heen doorkleuren. groot: het grotere boogje van de band.
--}}
@props([
    'nummer',
    'groot' => false,
])
@php
    $nummer = (int) $nummer;

    if ($nummer < 1) {
        throw new \InvalidArgumentException("Het fasenummer moet 1 of hoger zijn, niet [{$nummer}].");
    }

    $kleuren = [
        'background:#E0D0E0;color:#401838',
        'background:#404830;color:#E0D0E0',
        'background:#EFE9DC;color:#401838',
        'background:#E3E6D8;color:#401838',
    ];
    $stijl = $kleuren[($nummer - 1) % count($kleuren)].($groot ? ';width:100px;height:124px' : '');
@endphp
<div {{ $attributes->class(['tl__badge'])->merge(['style' => $stijl]) }}><span @if ($groot) style="font-size:54px" @endif>{{ $nummer }}</span></div>
