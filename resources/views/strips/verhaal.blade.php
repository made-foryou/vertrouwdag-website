{{--
    Strook: een verhaal met een grote kop en daaronder lopende tekst.

    Velden: kop (met accent) en tekst (opgemaakte tekst, een of meer
    alinea's). De tekst wordt ongeëscapet geprint, zoals de tekst van
    over-mij-intro.
--}}
<section class="wrap sec story" data-cms-strip="{{ $strip->id }}">
    <h2 class="h2" data-reveal data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h2>
    <div class="prose" data-reveal data-cms-field="tekst">{!! $strip->tekst !!}</div>
</section>
