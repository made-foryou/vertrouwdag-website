{{--
    Strook: een verhaal met een grote kop en daaronder lopende tekst.

    Velden: kop (Kop met nadruk) en tekst (opgemaakte tekst, een of meer
    alinea's). De tekst wordt ongeëscapet geprint, zoals de tekst van
    over-mij-intro.
--}}
<section class="wrap sec story" data-cms-strip="{{ $strip->id }}">
    <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" data-reveal />
    <div class="prose" data-reveal data-cms-field="tekst">{!! $strip->tekst !!}</div>
</section>
