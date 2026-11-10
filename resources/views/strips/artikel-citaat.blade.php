{{--
    Strook: één zin uit een artikel, groot op papier, iets scheef, op de
    gestreepte achtergrond van de reviews.

    Velden: citaat. Anders dan de strook citaat heeft dit geen kop en geen
    monogram.
--}}
<div class="reviews" style="margin:40px 0" data-cms-strip="{{ $strip->id }}">
    <div class="wrap" style="max-width:760px;padding-block:80px;display:flex;justify-content:center">
        <blockquote class="review review--groot art-quote" data-reveal style="transform:rotate(-1.5deg)" data-cms-field="citaat">{{ $strip->citaat }}</blockquote>
    </div>
</div>
