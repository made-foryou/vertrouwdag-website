{{--
    Strook: de hero van /over-mij met links Romy in een boog, het zegel en
    de zwart-witpolaroid, en rechts de kop, tekst en één knop. Op mobiel
    staat de tekst boven de foto's.

    Velden: label, kop (Kop met nadruk), tekst, knop (tekst en url),
    foto_boog, foto_polaroid en bijschrift (onder de polaroid). Zonder
    foto staat de foto uit het ontwerp (<x-foto>). Het zegel ligt vast.
--}}
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap hero" style="flex-wrap:wrap-reverse;padding-bottom:96px">
        <div class="hero__media about-hero" data-entrance>
            <div class="arch" style="left:6%;top:0;width:76%;height:100%;border:10px solid var(--papier);box-shadow:var(--schaduw)"><x-foto :foto="$strip->foto_boog" terugval="resources/images/romy.jpg" alt="Romy, weddingplanner van vertrouwdag in Zuid-Holland" :breedte="1100" :hoogte="1571" veld="foto_boog" fetchpriority="high" /></div>
            <div class="stamp tilt" style="left:0;top:6%;width:112px;height:132px;transform:rotate(-9deg)"><img src="{{ Vite::asset('resources/images/monogram-groen.png') }}" alt="" style="width:72px;height:72px"></div>
            <div class="polaroid tilt" style="right:0;bottom:6%;width:42%;padding:10px 10px 40px;transform:rotate(7deg)">
                <x-foto :foto="$strip->foto_polaroid" terugval="resources/images/kus-zwartwit.jpg" alt="Zwart-witfoto van een bruidspaar" :breedte="1100" :hoogte="641" veld="foto_polaroid" />
                <span style="font-size:18px" data-cms-field="bijschrift">{{ $strip->bijschrift }}</span>
            </div>
        </div>
        <div class="hero__txt" data-entrance style="gap:24px">
            <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
            <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h1" />
            <p class="lead" style="max-width:34ch" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
            @if ($strip->knop)
                <div class="row"><x-knop :href="$strip->knop['url']" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop></div>
            @endif
        </div>
    </div>
</section>
