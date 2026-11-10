{{--
    Strook: "Hart én hoofd", een grote kop met een inleiding en twee
    kolommen met een foto en een zwevende kaart erop.

    Velden: kop (met accent), tekst en kolommen (lijst met label, kop met
    accent en tekst). De foto hoort bij de plek van de kolom en ligt vast
    tot #21.
--}}
@php
    $fotos = [
        ['src' => 'resources/images/paar-landschap.jpg', 'alt' => 'Bruidspaar wandelt door een weids landschap'],
        ['src' => 'resources/images/feesttafel.jpg', 'alt' => 'Lange gedekte feesttafel met bloemen'],
    ];
@endphp
<section class="sec-top" data-cms-strip="{{ $strip->id }}">
    <h2 class="display" data-reveal data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h2>
    <p class="center" data-reveal style="margin:22px auto 56px;max-width:46ch;padding:0 24px;font-size:21px" data-cms-field="tekst">{{ $strip->tekst }}</p>
    <div class="split" data-reveal-group data-cms-items="kolommen">
        @foreach ($strip->kolommen as $kolom)
            @php($foto = $fotos[$loop->index % count($fotos)])
            <div class="split__photo">
                <img src="{{ Vite::asset($foto['src']) }}" alt="{{ $foto['alt'] }}" width="1100" height="733" loading="lazy">
                <div class="card-float">
                    <p class="eb eb--grijs" data-cms-field="label">{{ $kolom['label'] }}</p>
                    <h3 data-cms-field="kop"><x-accent :tekst="$kolom['kop']" /></h3>
                    <p data-cms-field="tekst">{{ $kolom['tekst'] }}</p>
                </div>
            </div>
        @endforeach
    </div>
</section>
