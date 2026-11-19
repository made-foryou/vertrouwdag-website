{{--
    Strook: een verhaal met een grote kop en daaronder lopende tekst.

    Velden: kop (Kop met nadruk) en tekst (opgemaakte tekst, een of meer
    alinea's). De tekst wordt ongeëscapet geprint, zoals de tekst van
    over-mij-intro.
--}}
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap wrap--smal sec story">
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" data-reveal />
        <div class="prose" data-reveal data-cms-field="tekst">{!! $strip->tekst !!}</div>
    </div>
</section>
