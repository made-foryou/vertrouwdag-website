{{--
    Strook: een korte kennismaking met Romy, met haar foto in een boog,
    een polaroid en een knop naar haar verhaal.

    Velden: label, kop (Kop met nadruk), tekst (opgemaakte tekst, een of meer
    alinea's), knop (tekst en url), foto_boog, foto_polaroid en
    bijschrift (onder de polaroid). De tekst staat in een element met
    display:contents, zodat de alinea's de ruimte van .about__txt houden.
    Zonder foto staat de foto uit het ontwerp (<x-foto>).
--}}
<section class="band" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec about">
        <div class="about__media" data-reveal="photo">
            <div class="arch"><x-foto :foto="$strip->foto_boog" terugval="resources/images/romy.jpg" alt="Romy, weddingplanner van vertrouwdag in Zuid-Holland" :breedte="1100" :hoogte="1571" veld="foto_boog" loading="lazy" /></div>
            <div class="polaroid tilt" style="right:0;bottom:8%;width:44%;padding:10px 10px 40px;transform:rotate(6deg)">
                <x-foto :foto="$strip->foto_polaroid" terugval="resources/images/ringen-pioenen.jpg" alt="Trouwringen tussen roze pioenrozen" :breedte="1100" :hoogte="733" veld="foto_polaroid" loading="lazy" />
                <span style="font-size:18px" data-cms-field="bijschrift">{{ $strip->bijschrift }}</span>
            </div>
        </div>
        <div class="about__txt" data-reveal="text">
            <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
            <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" />
            <div style="display:contents" data-cms-field="tekst">{!! $strip->tekst !!}</div>
            @if ($strip->knop)
                <div class="row" style="padding-top:6px"><x-knop :href="$strip->knop['url']" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop></div>
            @endif
        </div>
    </div>
</section>
