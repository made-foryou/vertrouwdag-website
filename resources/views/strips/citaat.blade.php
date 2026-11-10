{{--
    Strook: één groot citaat op papier, iets scheef, met het monogram, op
    de gestreepte achtergrond van de reviews.

    Velden: kop (optioneel, boven het citaat) en citaat. Anders dan de
    strook reviews is dit geen lijst en heeft het geen naam: het is een
    uitspraak van Romy zelf. Beide stroken gebruiken <x-review>.
--}}
<section class="reviews" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec" style="display:flex;justify-content:center">
        <x-review groot zegel data-reveal>
            @if (filled($strip->kop))
                <x-slot:kop data-cms-field="kop">{{ $strip->kop }}</x-slot:kop>
            @endif
            <x-slot:citaat data-cms-field="citaat">{{ $strip->citaat }}</x-slot:citaat>
        </x-review>
    </div>
</section>
