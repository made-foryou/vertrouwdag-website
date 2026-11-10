{{--
    Strook: de hero van /diensten met kop, tekst en twee knoppen, en
    rechts een boog met de ceremonieplek, de polaroid met de bruid en de
    sticker.

    Velden: label, kop (met accent), tekst, knop (tekst en url, op deze
    pagina naar #vergelijk), bijschrift (onder de polaroid) en sticker
    (meerdere regels). De eerste knop is de kennismaking uit de config.
    De foto's liggen vast tot #21. Zoals in het ontwerp schuift deze
    hero niet in.
--}}
<section class="wrap hero" style="padding-bottom:80px" data-cms-strip="{{ $strip->id }}">
    <div class="hero__txt" style="gap:24px">
        <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
        <h1 style="font-size:clamp(46px,6.4vw,88px)" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h1>
        <p class="lead" style="max-width:34ch" data-cms-field="tekst">{{ $strip->tekst }}</p>
        <div class="row">
            <x-knop :href="route(config('vertrouwdag.cta.route'))">{{ config('vertrouwdag.cta.label') }}</x-knop>
            @if ($strip->knop)
                <x-knop :href="$strip->knop['url']" variant="secundair" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop>
            @endif
        </div>
    </div>
    <div class="hero__media hero__media--d">
        <div class="arch" style="right:4%;top:0;width:62%;height:96%"><img src="{{ Vite::asset('resources/images/ceremonie-buiten.jpg') }}" alt="Ceremonieplek buiten met witte stoelen en bloemen" width="1100" height="734" fetchpriority="high"></div>
        <div class="polaroid tilt" style="left:0;bottom:4%;width:44%;padding:10px 10px 42px;transform:rotate(-6deg)">
            <img src="{{ Vite::asset('resources/images/bruid-rozen.jpg') }}" alt="Bruid met boeket van perzikkleurige rozen" width="1100" height="1650">
            <span style="font-size:19px;bottom:10px" data-cms-field="bijschrift">{{ $strip->bijschrift }}</span>
        </div>
        <div class="sticker" style="right:0;top:6%;width:96px;height:96px;transform:rotate(12deg)" data-cms-field="sticker">{!! nl2br(e($strip->sticker)) !!}</div>
    </div>
</section>
