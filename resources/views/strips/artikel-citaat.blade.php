{{--
    Strook: één zin uit een artikel, groot op papier, iets scheef, op de
    gestreepte achtergrond van de reviews.

    Velden: citaat. Anders dan de strook citaat heeft dit geen kop en geen
    monogram.
--}}
<div style="padding:40px 0" data-cms-strip="{{ $strip->id }}">
    <div class="reviews">
        <div class="wrap wrap--smal" style="padding-block:80px;display:flex;justify-content:center">
            <blockquote class="review review--groot art-quote" data-reveal="note" style="transform:rotate(-1.5deg)" data-cms-field="citaat"><x-regels :tekst="$strip->citaat" /></blockquote>
        </div>
    </div>
</div>
