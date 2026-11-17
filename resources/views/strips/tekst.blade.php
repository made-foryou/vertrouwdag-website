{{--
    Strook: een kop met daaronder tekst, in één kolom.

    Velden: kop (optioneel, met accent) en tekst (opgemaakte tekst:
    alinea's, lijstjes en links). De kop staat er alleen als hij gevuld
    is; vult een redacteur hem later, dan herlaadt het frame na het
    opslaan.

    De tekst wordt ongeëscapet geprint, zoals de tekst van verhaal: het
    package laat er alleen veilige opmaak in.
--}}
<section class="wrap sec tekst" data-cms-strip="{{ $strip->id }}">
    @if (filled($strip->kop))
        <h2 data-reveal data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h2>
    @endif
    <div class="prose" data-reveal data-cms-field="tekst">{!! $strip->tekst !!}</div>
</section>
