{{--
    Strook: de drie diensten als mapjes met prijs, die elk de eigen map op
    /diensten openen. Op mobiel zijn de mapjes een swipe-stapel.

    Velden: label, kop (met accent), knop (tekst en url) en diensten
    (lijst met dienst, soort, titel en omschrijving). dienst is de
    sleutel van de prijs in config/vertrouwdag.php (dag, partial of full)
    en bepaalt de kleur, de foto en het anker op /diensten.
--}}
@php
    $kaarten = [
        'dag' => ['kleur' => 'lila', 'anker' => 'dagcoordinatie', 'foto' => 'resources/images/schoenen-boeket.jpg', 'alt' => 'Bruidsschoenen en boeket op de vensterbank', 'draaiing' => -4, 'gespiegeld' => false],
        'partial' => ['kleur' => 'cream', 'anker' => 'partial-planning', 'foto' => 'resources/images/hand-in-hand-paar.jpg', 'alt' => 'Bruid en bruidegom hand in hand', 'draaiing' => 4, 'gespiegeld' => true],
        'full' => ['kleur' => 'groen', 'anker' => 'full-service', 'foto' => 'resources/images/sleep-jurk.jpg', 'alt' => 'Bruidegom draagt de sleep van de trouwjurk', 'draaiing' => -3, 'gespiegeld' => false],
    ];
@endphp
<section class="band" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec stack" style="gap:32px">
        <div class="sec-head" data-reveal>
            <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
            <h2 class="h-xl" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h2>
        </div>
        <div class="folders" data-deck data-reveal-group data-cms-items="diensten">
            @foreach ($strip->diensten as $dienst)
                @php($kaart = $kaarten[$dienst['dienst']] ?? throw new InvalidArgumentException("De strook diensten kent geen dienst [{$dienst['dienst']}]; kies dag, partial of full."))
                <x-dienstkaart :kleur="$kaart['kleur']" :dienst="$dienst['dienst']" :href="route('diensten').'#'.$kaart['anker']" :foto="Vite::asset($kaart['foto'])" :alt="$kaart['alt']" :draaiing="$kaart['draaiing']" :gespiegeld="$kaart['gespiegeld']">
                    <x-slot:soort data-cms-field="soort">{{ $dienst['soort'] }}</x-slot:soort>
                    <x-slot:titel data-cms-field="titel">{{ $dienst['titel'] }}</x-slot:titel>
                    <x-slot:omschrijving data-cms-field="omschrijving">{{ $dienst['omschrijving'] }}</x-slot:omschrijving>
                </x-dienstkaart>
            @endforeach
        </div>
        <x-stapel-bolletjes :aantal="count($strip->diensten)" licht />
        @if ($strip->knop)
            <div class="center" style="padding-top:8px"><x-knop :href="$strip->knop['url']" variant="secundair" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop></div>
        @endif
    </div>
</section>
