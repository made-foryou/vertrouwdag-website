{{--
    Strook: de hero van de homepage met kop, tekst en twee knoppen, en
    rechts het portret van Romy, de polaroid met de ringen, het zegel en
    de sticker.

    Velden: label, kop (met accent), tekst, knop (tekst en url),
    bijschrift (onder de polaroid) en sticker (meerdere regels). De
    eerste knop is de kennismaking uit de config. De foto's liggen vast
    tot #21.

    Het laatste woord van het label breekt niet af, zodat "Zuid-Holland"
    op mobiel niet bij het streepje wordt gesplitst. Daardoor staat er een
    element in het label, en herlaadt het frame van het CMS na het opslaan
    in plaats van direct mee te kijken.
--}}
@php
    $label = (string) $strip->label;
    $labelBegin = str_contains($label, ' ') ? Str::beforeLast($label, ' ').' ' : '';
    $labelEind = Str::afterLast($label, ' ');
@endphp
<section class="wrap hero" data-cms-strip="{{ $strip->id }}">
    <div class="hero__txt" data-reveal>
        <p class="eb" style="display:flex;align-items:center;gap:10px"><svg width="14" height="13" fill="#C8AEC6" aria-hidden="true"><use href="#heart"/></svg><span data-cms-field="label">{{ $labelBegin }}<span style="white-space:nowrap">{{ $labelEind }}</span></span></p>
        <h1 data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h1>
        <p class="lead" data-cms-field="tekst">{{ $strip->tekst }}</p>
        <div class="row">
            <x-knop :href="url(config('vertrouwdag.cta.pad'))">{{ config('vertrouwdag.cta.label') }}</x-knop>
            @if ($strip->knop)
                <x-knop :href="$strip->knop['url']" variant="secundair" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop>
            @endif
        </div>
    </div>
    <div class="hero__media" data-reveal>
        <div class="arch" style="right:0;top:0;width:72%;height:92%"><img src="{{ Vite::asset('resources/images/romy-portret.jpg') }}" alt="Romy, weddingplanner van vertrouwdag in Zuid-Holland" width="1100" height="1527" fetchpriority="high" style="object-position:50% 18%"></div>
        <div class="polaroid tilt" style="left:0;bottom:2%;width:46%;transform:rotate(-7deg)">
            <img src="{{ Vite::asset('resources/images/ringen-boeket.jpg') }}" alt="Handen met trouwringen en een boeket" width="1100" height="733">
            <span data-cms-field="bijschrift">{{ $strip->bijschrift }}</span>
        </div>
        <div class="stamp tilt" style="left:10%;top:4%;transform:rotate(9deg)"><img src="{{ Vite::asset('resources/images/monogram-groen.png') }}" alt=""></div>
        <div class="sticker" style="right:-6px;bottom:16%;transform:rotate(-12deg)" data-cms-field="sticker">{!! nl2br(e($strip->sticker)) !!}</div>
    </div>
</section>
