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
    <div class="wrap center">
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" data-reveal="text" />
        <p data-reveal="text" style="margin:22px auto 56px;max-width:calc(46 * var(--ch));font-size:21px" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
    </div>
    <div class="split" data-reveal-group="photo" data-cms-items="kolommen">
        @foreach ($strip->kolommen as $kolom)
            @php($terugval = $fotos[$loop->index % count($fotos)])
            <div class="split__photo">
                <x-foto :foto="$kolom['foto'] ?? null" :terugval="$terugval['src']" :alt="$terugval['alt']" :breedte="1100" :hoogte="733" veld="foto" loading="lazy" />
                <div class="card-float">
                    <p class="eb eb--grijs" data-cms-field="label">{{ $kolom['label'] }}</p>
                    <x-made-cms::heading :heading="$kolom['kop']" field="kop" emphasis class="h3" />
                    <p data-cms-field="tekst"><x-regels :tekst="$kolom['tekst']" /></p>
                </div>
            </div>
        @endforeach
    </div>
</section>
