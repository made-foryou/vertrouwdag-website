{{--
    De kaart van een foutpagina (resources/views/errors): een label, de kop
    met nadruk tussen sterretjes, een tekst en knoppen. De kaart staat op de streepjes van
    de reviews, met het monogram als zegel. De 404 zet hem in de layout
    van de site, de 500 in een eigen, kale pagina.

    De slot is de tekst. Het slot knoppen staat eronder. Het hartje komt
    uit het symbool #heart, dat de pagina zelf definieert.
--}}
@props([
    'label',
    'kop',
])
<section class="reviews">
    <div class="wrap sec" style="display:flex;justify-content:center">
        <div class="review review--groot" style="transform:rotate(-1.5deg)">
            <svg width="30" height="27" fill="#C8AEC6" aria-hidden="true"><use href="#heart"/></svg>
            <p class="eb">{{ $label }}</p>
            <h1 class="h1"><x-made-cms::emphasis :text="$kop" /></h1>
            <p style="font-size:20px;max-width:calc(40 * var(--ch))">{{ $slot }}</p>
            <div class="row" style="justify-content:center">{{ $knoppen }}</div>
            <img class="review__seal" src="{{ Vite::asset('resources/images/monogram-groen.png') }}" alt="">
        </div>
    </div>
</section>
