{{--
    Strook: de hero van /werkwijze met kop, tekst en twee knoppen, en
    rechts twee polaroids: het paar in het landschap en de gouden ringen.

    Velden: label, kop (met accent), tekst, knop (tekst en url, op deze
    pagina naar /diensten), foto_boven en foto_onder (op de polaroids),
    bijschrift_boven (onder de bovenste polaroid) en bijschrift_onder
    (onder de onderste). De eerste knop is de kennismaking uit de config.
    Zonder foto staat de foto uit het ontwerp (<x-foto>).
--}}
<section class="wrap hero" data-cms-strip="{{ $strip->id }}">
    <div class="hero__txt" data-reveal style="gap:24px">
        <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
        <h1 style="font-size:clamp(46px,6.4vw,88px)" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h1>
        <p class="lead" style="max-width:34ch" data-cms-field="tekst">{{ $strip->tekst }}</p>
        <div class="row">
            <x-knop :href="url(config('vertrouwdag.cta.pad'))">{{ config('vertrouwdag.cta.label') }}</x-knop>
            @if ($strip->knop)
                <x-knop :href="$strip->knop['url']" variant="secundair" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop>
            @endif
        </div>
    </div>
    <div class="hero__media hero__media--d ww-media" data-reveal style="aspect-ratio:1/.86">
        <div class="polaroid tilt" style="left:8%;top:4%;width:54%;padding:10px 10px 42px;transform:rotate(-5deg)">
            <x-foto :foto="$strip->foto_boven" terugval="resources/images/paar-landschap.jpg" alt="Bruidspaar wandelt door een weids landschap" :breedte="1100" :hoogte="733" veld="foto_boven" fetchpriority="high" />
            <span style="font-size:19px;bottom:10px" data-cms-field="bijschrift_boven">{{ $strip->bijschrift_boven }}</span>
        </div>
        <div class="polaroid tilt" style="right:8%;bottom:2%;width:48%;padding:10px 10px 42px;transform:rotate(6deg)">
            <x-foto :foto="$strip->foto_onder" terugval="resources/images/ringen-goud.jpg" alt="Twee gouden trouwringen" :breedte="1100" :hoogte="733" veld="foto_onder" />
            <span style="font-size:19px;bottom:10px" data-cms-field="bijschrift_onder">{{ $strip->bijschrift_onder }}</span>
        </div>
    </div>
</section>
