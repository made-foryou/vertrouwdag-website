{{--
    Strook: reviews op papier onder de kop "Wat anderen zeggen". De eerste
    review krijgt het monogram.

    Velden: kop (Kop met nadruk) en reviews (lijst met label, citaat en naam;
    label en naam zijn optioneel).
--}}
<section class="reviews" id="reviews" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec stack" style="align-items:center;gap:40px">
        <div class="pill" data-reveal="text"><x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" /></div>
        <div class="review-list" data-reveal-group data-cms-items="reviews">
            @foreach ($strip->reviews as $review)
                <x-review :zegel="$loop->first">
                    @if (filled($review['label']))
                        <x-slot:label data-cms-field="label">{{ $review['label'] }}</x-slot:label>
                    @endif
                    <x-slot:citaat data-cms-field="citaat"><x-regels :tekst="$review['citaat']" /></x-slot:citaat>
                    @if (filled($review['naam']))
                        <x-slot:naam data-cms-field="naam">{{ $review['naam'] }}</x-slot:naam>
                    @endif
                </x-review>
            @endforeach
        </div>
    </div>
</section>
