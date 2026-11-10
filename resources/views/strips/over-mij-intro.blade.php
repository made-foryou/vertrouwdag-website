{{--
    Strook: een korte kennismaking met Romy, met haar foto in een boog,
    een polaroid en een knop naar haar verhaal.

    Velden: label, kop (met accent), tekst (opgemaakte tekst, een of meer
    alinea's), knop (tekst en url) en bijschrift (onder de polaroid). De
    tekst staat in een element met display:contents, zodat de alinea's
    de ruimte van .about__txt houden. De foto's liggen vast tot #21.
--}}
<section class="band" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec about">
        <div class="about__media" data-reveal>
            <div class="arch"><img src="{{ Vite::asset('resources/images/romy.jpg') }}" alt="Romy, weddingplanner van vertrouwdag in Zuid-Holland" width="1100" height="1571" loading="lazy"></div>
            <div class="polaroid tilt" style="right:0;bottom:8%;width:44%;padding:10px 10px 40px;transform:rotate(6deg)">
                <img src="{{ Vite::asset('resources/images/ringen-pioenen.jpg') }}" alt="Trouwringen tussen roze pioenrozen" width="1100" height="733" loading="lazy">
                <span style="font-size:18px;bottom:9px" data-cms-field="bijschrift">{{ $strip->bijschrift }}</span>
            </div>
        </div>
        <div class="about__txt" data-reveal>
            <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
            <h2 style="font-size:clamp(42px,5vw,68px);line-height:1.02" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h2>
            <div style="display:contents" data-cms-field="tekst">{!! $strip->tekst !!}</div>
            @if ($strip->knop)
                <div class="row" style="padding-top:6px"><x-knop :href="$strip->knop['url']" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop></div>
            @endif
        </div>
    </div>
</section>
