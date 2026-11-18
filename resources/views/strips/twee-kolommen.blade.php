{{--
    Strook: "Hart én hoofd", een grote kop met een inleiding en twee
    kolommen met een foto en een zwevende kaart erop.

    Velden: kop (Kop met nadruk), tekst en kolommen (lijst met foto, label,
    Kop met nadruk en tekst). Een kolom zonder foto krijgt de foto uit het
    ontwerp die bij zijn plek hoort (<x-foto>).
--}}
@php
    $fotos = [
        ['src' => 'resources/images/paar-landschap.jpg', 'alt' => 'Bruidspaar wandelt door een weids landschap'],
        ['src' => 'resources/images/feesttafel.jpg', 'alt' => 'Lange gedekte feesttafel met bloemen'],
    ];
@endphp
<section class="sec-top" data-cms-strip="{{ $strip->id }}">
    <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2 center wrap" data-reveal />
    <p class="center" data-reveal style="margin:22px auto 56px;max-width:46ch;padding:0 24px;font-size:21px" data-cms-field="tekst">{{ $strip->tekst }}</p>
    <div class="split" data-reveal-group data-cms-items="kolommen">
        @foreach ($strip->kolommen as $kolom)
            @php($terugval = $fotos[$loop->index % count($fotos)])
            <div class="split__photo">
                <x-foto :foto="$kolom['foto'] ?? null" :terugval="$terugval['src']" :alt="$terugval['alt']" :breedte="1100" :hoogte="733" veld="foto" loading="lazy" />
                <div class="card-float">
                    <p class="eb eb--grijs" data-cms-field="label">{{ $kolom['label'] }}</p>
                    <x-made-cms::heading :heading="$kolom['kop']" field="kop" emphasis class="h3" />
                    <p data-cms-field="tekst">{{ $kolom['tekst'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>
