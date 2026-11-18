{{--
    Strook: een juridische pagina (privacyverklaring, cookiebeleid,
    algemene voorwaarden) in een smalle kolom met lopende tekst.

    Velden: label, kop (met accent), bijgewerkt (de datum achter "Laatst
    bijgewerkt", optioneel), inleiding (opgemaakte tekst, optioneel),
    secties (lijst met kop en tekst, optioneel), cookieknop (de tekst van
    een knop die de cookie-instellingen opent, optioneel) en afsluiting
    (opgemaakte tekst, optioneel). De opgemaakte tekst wordt ongeëscapet
    geprint, zoals de tekst van artikel-tekst.

    RichText van het package kent geen tussenkoppen (een <h2> wordt daar
    gewone tekst), daarom is een tussenkop het veld kop van een sectie en
    wordt hij een echte <h2>, met de maat van een tussenkop (.h3).
    De veldwrappers hebben display:contents, zodat de alinea's en koppen
    de opmaak van .legal .prose houden.
--}}
<section class="wrap legal" data-cms-strip="{{ $strip->id }}">
    <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
    <h1 class="h1" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h1>
    @if (filled($strip->bijgewerkt))
        <p class="legal__meta">Laatst bijgewerkt: <span data-cms-field="bijgewerkt">{{ $strip->bijgewerkt }}</span></p>
    @endif
    <div class="prose">
        @if (filled($strip->inleiding))
            <div style="display:contents" data-cms-field="inleiding">{!! $strip->inleiding !!}</div>
        @endif
        @if (filled($strip->secties))
            <div style="display:contents" data-cms-items="secties">
                @foreach ($strip->secties as $sectie)
                    <div style="display:contents">
                        <h2 class="h3" data-cms-field="kop">{{ $sectie['kop'] }}</h2>
                        <div style="display:contents" data-cms-field="tekst">{!! $sectie['tekst'] !!}</div>
                    </div>
                @endforeach
            </div>
        @endif
        @if (filled($strip->cookieknop))
            <p><x-knop variant="secundair" data-cookie-open data-cms-field="cookieknop">{{ $strip->cookieknop }}</x-knop></p>
        @endif
        @if (filled($strip->afsluiting))
            <div style="display:contents" data-cms-field="afsluiting">{!! $strip->afsluiting !!}</div>
        @endif
    </div>
</section>
