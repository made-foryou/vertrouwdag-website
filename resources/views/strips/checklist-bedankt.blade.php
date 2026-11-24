{{--
    Strook: het bedankblok van /checklist, met de knop naar de
    kennismaking uit de config. Het blok is verborgen tot het formulier
    is verstuurd; dan toont resources/js/modules/checklist-formulier.js
    het en zet de focus op de kop, zodat ook een schermlezer het hoort.
    Zonder JavaScript staat het open na een geslaagde aanmelding (#25).
    In de bewerkmodus van het CMS staat het open, zodat een redacteur het
    ziet en kan wijzigen.

    Velden: kop (Kop met nadruk), tekst en vraag (de regel boven de knop).
--}}
@use('Made\CmsPackage\Content\Data\EditMode')
<section class="reviews" id="bedankt" @unless (app(EditMode::class)->active || session('checklist.signed_up')) hidden @endunless data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec" style="display:flex;justify-content:center">
        <div class="review review--groot" style="transform:rotate(-1.5deg);align-items:center">
            <svg width="30" height="27" fill="#C8AEC6" aria-hidden="true"><use href="#heart"/></svg>
            <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" tabindex="-1" />
            <p style="font-size:20px;max-width:calc(40 * var(--ch))" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
            @if (filled($strip->vraag))
                <p style="font-size:22px;font-style:italic;color:var(--aubergine)" data-cms-field="vraag">{{ $strip->vraag }}</p>
            @endif
            <div class="row" style="justify-content:center"><x-knop :href="url(config('vertrouwdag.cta.pad'))">{{ config('vertrouwdag.cta.label') }}</x-knop></div>
            <img class="review__seal" src="{{ Vite::asset('resources/images/monogram-groen.png') }}" alt="">
        </div>
    </div>
</section>
