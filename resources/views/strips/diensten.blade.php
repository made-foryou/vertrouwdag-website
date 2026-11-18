{{--
    Strook: de drie diensten als mapjes met prijs, die elk de eigen map op
    /diensten openen. Op mobiel zijn de mapjes een swipe-stapel.

    Velden: label, kop (met accent), knop (tekst en url) en diensten
    (lijst met dienst, soort, titel, omschrijving en foto). dienst (dag,
    partial of full) bepaalt via App\Support\Dienst de kleur en het anker
    op /diensten, en de foto zolang er geen gekozen is (x-dienstkaart). prijzen (van App\Cms\Strips\Diensten::with()) geeft per
    dienst de prijs uit de strook pakketten op /diensten; een dienst
    zonder prijs heeft geen prijsregel. Hoe de foto draait en of de kaart
    gespiegeld is, hoort bij deze stapel.
--}}
@use('App\Support\Dienst')
@php
    $plaatsing = [
        'dag' => ['draaiing' => -4, 'gespiegeld' => false],
        'partial' => ['draaiing' => 4, 'gespiegeld' => true],
        'full' => ['draaiing' => -3, 'gespiegeld' => false],
    ];
@endphp
<section class="band" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec stack" style="gap:32px">
        <div class="sec-head" data-reveal>
            <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
            <h2 class="h2" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h2>
        </div>
        <div class="folders" data-deck data-reveal-group data-cms-items="diensten">
            @foreach ($strip->diensten as $dienst)
                @php($welkeDienst = Dienst::tryFrom($dienst['dienst']) ?? throw new InvalidArgumentException("De strook diensten kent geen dienst [{$dienst['dienst']}]; kies dag, partial of full."))
                <x-dienstkaart :kleur="$welkeDienst->kleur()->value" :dienst="$welkeDienst->value" :prijs="$strip->prijzen[$welkeDienst->value] ?? null" :href="url(\App\Support\Aanbod::ADRES).'#'.$welkeDienst->anker()" :foto="$dienst['foto'] ?? null" :draaiing="$plaatsing[$welkeDienst->value]['draaiing']" :gespiegeld="$plaatsing[$welkeDienst->value]['gespiegeld']">
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
