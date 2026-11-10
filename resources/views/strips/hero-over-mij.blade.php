{{--
    Strook: de hero van /over-mij met links Romy in een boog, het zegel en
    de zwart-witpolaroid, en rechts de kop, tekst en één knop. Op mobiel
    staat de tekst boven de foto's.

    Velden: label, kop (met accent), tekst, knop (tekst en url) en
    bijschrift (onder de polaroid). De foto's liggen vast tot #21.
--}}
<section class="wrap hero" style="flex-wrap:wrap-reverse;padding-bottom:96px" data-cms-strip="{{ $strip->id }}">
    <div class="hero__media about-hero" data-reveal>
        <div class="arch" style="left:6%;top:0;width:76%;height:100%;border:10px solid var(--papier);box-shadow:var(--schaduw)"><img src="{{ Vite::asset('resources/images/romy.jpg') }}" alt="Romy, weddingplanner van vertrouwdag in Zuid-Holland" width="1100" height="1571" fetchpriority="high"></div>
        <div class="stamp tilt" style="left:0;top:6%;width:112px;height:132px;transform:rotate(-9deg)"><img src="{{ Vite::asset('resources/images/monogram-groen.png') }}" alt="" style="width:72px;height:72px"></div>
        <div class="polaroid tilt" style="right:0;bottom:6%;width:42%;padding:10px 10px 40px;transform:rotate(7deg)">
            <img src="{{ Vite::asset('resources/images/kus-zwartwit.jpg') }}" alt="Zwart-witfoto van een bruidspaar" width="1100" height="641">
            <span style="font-size:18px;bottom:9px" data-cms-field="bijschrift">{{ $strip->bijschrift }}</span>
        </div>
    </div>
    <div class="hero__txt" data-reveal style="gap:24px">
        <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
        <h1 style="font-size:clamp(52px,7vw,100px);line-height:.96" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h1>
        <p class="lead" style="max-width:34ch" data-cms-field="tekst">{{ $strip->tekst }}</p>
        @if ($strip->knop)
            <div class="row"><x-knop :href="$strip->knop['url']" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop></div>
        @endif
    </div>
</section>
