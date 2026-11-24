{{--
    Strook: de hero van /diensten met kop, tekst en twee knoppen, en
    rechts een boog met de ceremonieplek, de polaroid met de bruid en de
    sticker.

    Velden: label, kop (Kop met nadruk), tekst, knop (tekst en url, op deze
    pagina naar #vergelijk), foto_boog, foto_polaroid, bijschrift (onder
    de polaroid) en sticker (meerdere regels). De eerste knop is de
    kennismaking uit de config. Zonder foto staat de foto uit het ontwerp
    (<x-foto>). Zoals in het ontwerp schuift deze hero niet in.
--}}
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap hero" style="padding-bottom:80px">
        <div class="hero__txt" data-entrance style="gap:24px">
            <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
            <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h1" />
            <p class="lead" style="max-width:calc(34 * var(--ch))" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
            <div class="row">
                <x-knop :href="url(config('vertrouwdag.cta.pad'))">{{ config('vertrouwdag.cta.label') }}</x-knop>
                @if ($strip->knop)
                    <x-knop :href="$strip->knop['url']" variant="secundair" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop>
                @endif
            </div>
        </div>
        <div class="hero__media hero__media--d">
            <div class="hero__collage" data-entrance>
                <div class="arch" style="right:4%;top:0;width:62%;height:96%"><x-foto :foto="$strip->foto_boog" terugval="resources/images/ceremonie-buiten.jpg" alt="Ceremonieplek buiten met witte stoelen en bloemen" :breedte="1100" :hoogte="734" sizes="(max-width:1030px) 56vw, 340px" veld="foto_boog" fetchpriority="high" /></div>
                <div class="polaroid tilt" style="left:0;bottom:4%;width:44%;padding:10px 10px 42px;transform:rotate(-6deg)">
                    <x-foto :foto="$strip->foto_polaroid" terugval="resources/images/bruid-rozen.jpg" alt="Bruid met boeket van perzikkleurige rozen" :breedte="1100" :hoogte="1650" sizes="(max-width:1030px) 45vw, 240px" veld="foto_polaroid" />
                    <span style="font-size:19px" data-cms-field="bijschrift">{{ $strip->bijschrift }}</span>
                </div>
                <div class="sticker" style="right:0;top:6%;width:96px;height:96px;transform:rotate(12deg)" data-cms-field="sticker"><x-regels :tekst="$strip->sticker" /></div>
            </div>
        </div>
    </div>
</section>
