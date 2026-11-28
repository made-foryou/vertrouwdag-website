{{--
    Strook: de hero van de homepage met kop, tekst en twee knoppen, en
    rechts het portret van Romy, de polaroid met de ringen, het zegel en
    de sticker.

    Velden: variant, label, kop (Kop met nadruk), tekst, eerste_knop en knop
    (tekst en url), foto_boog (het portret), foto_polaroid, bijschrift
    (onder de polaroid) en sticker (meerdere regels). Zonder eerste knop
    staat de knop naar de kennismaking uit de config, zoals in de header.
    Zonder foto staat de foto uit het ontwerp (<x-foto>); het portret dan
    met het gezicht van Romy in beeld. Het zegel ligt vast.

    Het laatste woord van het label breekt niet af, zodat "Zuid-Holland"
    op mobiel niet bij het streepje wordt gesplitst. Daardoor staat er een
    element in het label, en herlaadt het frame van het CMS na het opslaan
    in plaats van direct mee te kijken.

    The variant gives the markup each of these had as a strip of its own; the settings below hold what differs:
    - home: the photos on the right with the stamp and the sticker, the heart in the label;
    - diensten (the old hero-diensten): the ceremony spot in the arch, the bride on the polaroid, the sticker on top
      and no stamp; the photo comes first on a phone, as on the homepage;
    - over-mij (the old hero-over-mij): Romy on the left in a bordered arch, the stamp, the black and white polaroid
      and one button, the field knop. On a phone the text stands above the photos (wrap-reverse), and the markup
      puts the stamp before the polaroid.
--}}
@php
    $variant = $strip->variant ?: 'home';
    $opmaak = match ($variant) {
        'home' => [
            'wrap' => ['klasse' => 'hero--home', 'stijl' => null],
            'tekstStijl' => null,
            'mediaEerst' => false,
            'mediaKlasse' => null,
            'boog' => ['stijl' => 'right:0;top:0;width:72%;height:92%', 'terugval' => 'resources/images/romy-portret.jpg', 'alt' => 'Romy, weddingplanner van vertrouwdag in Zuid-Holland', 'hoogte' => 1527, 'positie' => '50% 18%', 'sizes' => '(max-width:523px) 64vw, (max-width:720px) 331px, (max-width:860px) 63vw, (max-width:1240px) 31vw, 380px'],
            'polaroid' => ['stijl' => 'left:0;bottom:2%;width:46%;transform:rotate(-7deg)', 'terugval' => 'resources/images/ringen-boeket.jpg', 'alt' => 'Handen met trouwringen en een boeket', 'hoogte' => 733, 'sizes' => '(max-width:523px) 41vw, (max-width:720px) 218px, (max-width:860px) 42vw, (max-width:1240px) 20vw, 245px', 'bijschriftStijl' => null],
            'zegel' => ['stijl' => 'left:10%;top:4%;transform:rotate(9deg)', 'sizes' => '(max-width:720px) 50px, 78px', 'beeldStijl' => null, 'voorPolaroid' => false],
            'sticker' => 'right:-6px;bottom:16%;transform:rotate(-12deg)',
        ],
        'diensten' => [
            'wrap' => ['klasse' => 'hero--diensten', 'stijl' => 'padding-bottom:80px'],
            'tekstStijl' => 'gap:24px',
            'mediaEerst' => false,
            'mediaKlasse' => 'hero__media--d',
            'boog' => ['stijl' => 'right:4%;top:0;width:62%;height:96%', 'terugval' => 'resources/images/ceremonie-buiten.jpg', 'alt' => 'Ceremonieplek buiten met witte stoelen en bloemen', 'hoogte' => 734, 'positie' => null, 'sizes' => '(max-width:523px) 54vw, (max-width:860px) 285px, (max-width:1240px) 26vw, 327px'],
            'polaroid' => ['stijl' => 'left:0;bottom:4%;width:44%;padding:10px 10px 42px;transform:rotate(-6deg)', 'terugval' => 'resources/images/bruid-rozen.jpg', 'alt' => 'Bruid met boeket van perzikkleurige rozen', 'hoogte' => 1650, 'sizes' => '(max-width:523px) 39vw, (max-width:860px) 202px, (max-width:1240px) 19vw, 232px', 'bijschriftStijl' => 'font-size:19px'],
            'zegel' => null,
            'sticker' => 'right:0;top:6%;width:96px;height:96px;transform:rotate(12deg)',
        ],
        'over-mij' => [
            'wrap' => ['klasse' => null, 'stijl' => 'flex-wrap:wrap-reverse;padding-bottom:96px'],
            'tekstStijl' => 'gap:24px',
            'mediaEerst' => true,
            'mediaKlasse' => 'about-hero',
            'boog' => ['stijl' => 'left:6%;top:0;width:76%;height:100%;border:10px solid var(--papier);box-shadow:var(--schaduw)', 'terugval' => 'resources/images/romy.jpg', 'alt' => 'Romy, weddingplanner van vertrouwdag in Zuid-Holland', 'hoogte' => 1571, 'positie' => null, 'sizes' => '(max-width:620px) calc(67vw - 18px), (max-width:1085px) 393px, (max-width:1246px) calc(30vw - 18px), 358px'],
            'polaroid' => ['stijl' => 'right:0;bottom:6%;width:42%;padding:10px 10px 40px;transform:rotate(7deg)', 'terugval' => 'resources/images/kus-zwartwit.jpg', 'alt' => 'Zwart-witfoto van een bruidspaar', 'hoogte' => 641, 'sizes' => '(max-width:620px) 44vw, (max-width:720px) 270px, (max-width:1085px) 227px, (max-width:1290px) 17vw, 207px', 'bijschriftStijl' => 'font-size:18px'],
            'zegel' => ['stijl' => 'left:0;top:6%;width:112px;height:132px;transform:rotate(-9deg)', 'sizes' => '(max-width:720px) 54px, 72px', 'beeldStijl' => 'width:72px;height:72px', 'voorPolaroid' => true],
            'sticker' => null,
        ],
        default => throw new InvalidArgumentException("De strook hero kent geen variant [{$variant}]; kies home, diensten of over-mij."),
    };
    $label = (string) $strip->label;
    $labelBegin = str_contains($label, ' ') ? Str::beforeLast($label, ' ').' ' : '';
    $labelEind = Str::afterLast($label, ' ');
    $eersteKnop = $strip->eerste_knop ?? ['text' => config('vertrouwdag.cta.label'), 'url' => config('vertrouwdag.cta.pad')];
@endphp
<section data-cms-strip="{{ $strip->id }}">
    <div @class(array_filter(['wrap hero', $opmaak['wrap']['klasse']])) @if ($opmaak['wrap']['stijl']) style="{{ $opmaak['wrap']['stijl'] }}" @endif>
        @if ($opmaak['mediaEerst'])
            <x-hero-media :strip="$strip" :opmaak="$opmaak" />
        @endif
        <div class="hero__txt" data-entrance @if ($opmaak['tekstStijl']) style="{{ $opmaak['tekstStijl'] }}" @endif>
            @if ($variant === 'home')
                <p class="eb" style="display:flex;align-items:center;gap:10px"><svg width="14" height="13" fill="#C8AEC6" aria-hidden="true"><use href="#heart"/></svg><span data-cms-field="label">{{ $labelBegin }}<span style="white-space:nowrap">{{ $labelEind }}</span></span></p>
            @else
                <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
            @endif
            <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h1" />
            <p class="lead lead--breed" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
            @if ($variant === 'over-mij')
                @if ($strip->knop)
                    <div class="row"><x-knop :href="$strip->knop['url']" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop></div>
                @endif
            @else
                <div class="row">
                    <x-knop :href="$eersteKnop['url']" data-cms-field="eerste_knop">{{ $eersteKnop['text'] }}</x-knop>
                    @if ($strip->knop)
                        <x-knop :href="$strip->knop['url']" variant="secundair" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop>
                    @endif
                </div>
            @endif
        </div>
        @unless ($opmaak['mediaEerst'])
            <x-hero-media :strip="$strip" :opmaak="$opmaak" />
        @endunless
    </div>
</section>
