{{--
    Strook: de hero van de homepage met kop, tekst en twee knoppen, en
    rechts het portret van Romy, de polaroid met de ringen, het zegel en
    de sticker.

    Velden: label, kop (Kop met nadruk), tekst, eerste_knop en knop (tekst en
    url), foto_boog (het portret), foto_polaroid, bijschrift (onder de
    polaroid) en sticker (meerdere regels). Zonder eerste knop staat de
    knop naar de kennismaking uit de config, zoals in de header. Zonder
    foto staat de foto uit het ontwerp (<x-foto>); het portret dan met het
    gezicht van Romy in beeld. Het zegel ligt vast.

    Het laatste woord van het label breekt niet af, zodat "Zuid-Holland"
    op mobiel niet bij het streepje wordt gesplitst. Daardoor staat er een
    element in het label, en herlaadt het frame van het CMS na het opslaan
    in plaats van direct mee te kijken.
--}}
@php
    $label = (string) $strip->label;
    $labelBegin = str_contains($label, ' ') ? Str::beforeLast($label, ' ').' ' : '';
    $labelEind = Str::afterLast($label, ' ');
    $eersteKnop = $strip->eerste_knop ?? ['text' => config('vertrouwdag.cta.label'), 'url' => config('vertrouwdag.cta.pad')];
@endphp
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap hero">
        <div class="hero__txt" data-reveal>
            <p class="eb" style="display:flex;align-items:center;gap:10px"><svg width="14" height="13" fill="#C8AEC6" aria-hidden="true"><use href="#heart"/></svg><span data-cms-field="label">{{ $labelBegin }}<span style="white-space:nowrap">{{ $labelEind }}</span></span></p>
            <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h1" />
            <p class="lead" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
            <div class="row">
                <x-knop :href="$eersteKnop['url']" data-cms-field="eerste_knop">{{ $eersteKnop['text'] }}</x-knop>
                @if ($strip->knop)
                    <x-knop :href="$strip->knop['url']" variant="secundair" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop>
                @endif
            </div>
        </div>
        <div class="hero__media" data-reveal>
            <div class="arch" style="right:0;top:0;width:72%;height:92%"><x-foto :foto="$strip->foto_boog" terugval="resources/images/romy-portret.jpg" alt="Romy, weddingplanner van vertrouwdag in Zuid-Holland" :breedte="1100" :hoogte="1527" positie="50% 18%" veld="foto_boog" fetchpriority="high" /></div>
            <div class="polaroid tilt" style="left:0;bottom:2%;width:46%;transform:rotate(-7deg)">
                <x-foto :foto="$strip->foto_polaroid" terugval="resources/images/ringen-boeket.jpg" alt="Handen met trouwringen en een boeket" :breedte="1100" :hoogte="733" veld="foto_polaroid" />
                <span data-cms-field="bijschrift">{{ $strip->bijschrift }}</span>
            </div>
            <div class="stamp tilt" style="left:10%;top:4%;transform:rotate(9deg)"><img src="{{ Vite::asset('resources/images/monogram-groen.png') }}" alt=""></div>
            <div class="sticker" style="right:-6px;bottom:16%;transform:rotate(-12deg)" data-cms-field="sticker"><x-regels :tekst="$strip->sticker" /></div>
        </div>
    </div>
</section>
