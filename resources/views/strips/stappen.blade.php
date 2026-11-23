{{--
    Strook: de werkwijze in genummerde stappen, met een knop naar de
    pagina erover.

    Velden: label, kop (Kop met nadruk), knop (tekst en url) en stappen (lijst
    met titel en tekst). Het nummer volgt de volgorde. De kleur hoort bij
    de plek van de stap en begint na de vierde opnieuw.
--}}
@php
    $kleuren = ['lila', 'mos', 'zand', 'groen'];
@endphp
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec stack" style="gap:56px">
        <div class="werk-head" data-reveal="text">
            <div class="stack werk-head__txt"><p class="eb" data-cms-field="label">{{ $strip->label }}</p><x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" /></div>
            @if ($strip->knop)
                <x-knop :href="$strip->knop['url']" variant="secundair" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop>
            @endif
        </div>
        <ol class="steps" data-reveal-group="steps" data-cms-items="stappen">
            @foreach ($strip->stappen as $stap)
                <x-stapkaart :nummer="$loop->iteration" :kleur="$kleuren[$loop->index % count($kleuren)]"><x-slot:titel data-cms-field="titel">{{ $stap['titel'] }}</x-slot:titel><x-slot:tekst data-cms-field="tekst"><x-regels :tekst="$stap['tekst']" /></x-slot:tekst></x-stapkaart>
            @endforeach
        </ol>
    </div>
</section>
