{{--
    Strook: één groot citaat op papier, iets scheef, met het monogram, op
    de gestreepte achtergrond van de reviews.

    Velden: variant, kop (optioneel, boven het citaat) en citaat. Anders
    dan de strook reviews is dit geen lijst en heeft het geen naam: het is
    een uitspraak van Romy zelf. Beide stroken gebruiken <x-review>.

    The variant artikel is the markup of the old artikel-citaat: one line out of an article, in the narrow column on
    an inset band of stripes, without the heading or the monogram. Its root is a div, like the other strips of an
    article body.
--}}
@php
    $variant = $strip->variant ?: 'groot';

    if (! in_array($variant, ['groot', 'artikel'], true)) {
        throw new InvalidArgumentException("De strook citaat kent geen variant [{$variant}]; kies groot of artikel.");
    }
@endphp
@if ($variant === 'artikel')
    <div style="padding:40px 0" data-cms-strip="{{ $strip->id }}">
        <div class="reviews">
            <div class="wrap wrap--smal" style="padding-block:80px;display:flex;justify-content:center">
                <blockquote class="review review--groot art-quote" data-reveal="note" style="transform:rotate(-1.5deg)" data-cms-field="citaat"><x-regels :tekst="$strip->citaat" /></blockquote>
            </div>
        </div>
    </div>
@else
    <section class="reviews" data-cms-strip="{{ $strip->id }}">
        <div class="wrap sec" style="display:flex;justify-content:center">
            <x-review groot zegel data-reveal="note">
                @if (filled($strip->kop))
                    <x-slot:kop data-cms-field="kop">{{ $strip->kop }}</x-slot:kop>
                @endif
                <x-slot:citaat data-cms-field="citaat"><x-regels :tekst="$strip->citaat" /></x-slot:citaat>
            </x-review>
        </div>
    </section>
@endif
