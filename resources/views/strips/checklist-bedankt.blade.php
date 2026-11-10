{{--
    Strook: het bedankblok van /checklist, met de knop naar de
    kennismaking uit de config. Het blok is verborgen tot het formulier
    is verstuurd; dan toont resources/js/modules/checklist-formulier.js
    het en zet de focus op de kop, zodat ook een schermlezer het hoort.

    Velden: kop (met accent), tekst en vraag (de regel boven de knop).
--}}
<section class="reviews" id="bedankt" hidden data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec" style="display:flex;justify-content:center">
        <div class="review review--groot" style="transform:rotate(-1.5deg);align-items:center">
            <svg width="30" height="27" fill="#C8AEC6" aria-hidden="true"><use href="#heart"/></svg>
            <h2 style="font-size:clamp(34px,4.2vw,52px)" tabindex="-1" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h2>
            <p style="font-size:20px;max-width:40ch" data-cms-field="tekst">{{ $strip->tekst }}</p>
            @if (filled($strip->vraag))
                <p style="font-size:22px;font-style:italic;color:var(--aubergine)" data-cms-field="vraag">{{ $strip->vraag }}</p>
            @endif
            <div class="row" style="justify-content:center"><x-knop :href="route(config('vertrouwdag.cta.route'))">{{ config('vertrouwdag.cta.label') }}</x-knop></div>
            <img class="review__seal" src="{{ Vite::asset('resources/images/monogram-groen.png') }}" alt="">
        </div>
    </div>
</section>
