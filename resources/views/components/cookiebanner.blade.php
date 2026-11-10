{{--
    De cookiebanner uit het ontwerp. Teksten, knoppen en categorieën komen
    uit config/vertrouwdag.php (vertrouwdag.cookies), zodat het CMS ze
    later kan leveren (#13).

    De banner staat in de layout direct na de skiplink: wie met Tab begint,
    komt eerst bij de skiplink en dan bij de keuze. Hij is verborgen tot
    resources/js/modules/cookiebanner.js hem toont. Zonder JavaScript
    blijft hij weg en laadt er niets dat toestemming nodig heeft.

    De versie en de categorieën met een vinkje staan als data-attributen
    op #cookie. resources/js/modules/toestemming.js leest ze daar.

    "Alleen noodzakelijk" en "Alles accepteren" hebben dezelfde stijl:
    weigeren is even zichtbaar als accepteren.
--}}
@php
    $cookies = config('vertrouwdag.cookies');
    $keuzes = array_keys(array_filter($cookies['categorieen'], fn (array $categorie): bool => ! $categorie['verplicht']));
    $link = '<a href="'.e(route('cookiebeleid')).'">'.e($cookies['cookiebeleid']).'</a>';
@endphp
<div class="cookie" id="cookie" data-versie="{{ $cookies['versie'] }}" data-categorieen="{{ json_encode($keuzes) }}" hidden>
    <div class="cookie__box" role="dialog" aria-modal="false" aria-labelledby="ck-title" aria-describedby="ck-tekst" tabindex="-1">
        <div class="cookie__main">
            <div class="cookie__txt">
                <p class="cookie__title" id="ck-title">{{ $cookies['titel'] }}</p>
                <p id="ck-tekst">{!! str_replace(':cookiebeleid', $link, e($cookies['tekst'])) !!}</p>
            </div>
            <div class="row" style="gap:10px">
                <x-knop variant="secundair" data-ck="prefs" aria-expanded="false" aria-controls="cookie-prefs">{{ $cookies['knoppen']['voorkeuren'] }}</x-knop>
                <x-knop data-ck="none">{{ $cookies['knoppen']['weigeren'] }}</x-knop>
                <x-knop data-ck="all">{{ $cookies['knoppen']['accepteren'] }}</x-knop>
            </div>
        </div>
        <div class="cookie__prefs" id="cookie-prefs" hidden>
            @foreach ($cookies['categorieen'] as $sleutel => $categorie)
                @if ($categorie['verplicht'])
                    <div class="pref"><span><b>{{ $categorie['naam'] }}</b><span>{{ $categorie['uitleg'] }}</span></span><span class="eb eb--sm eb--grijs">{{ $cookies['altijd_aan'] }}</span></div>
                @else
                    <label class="pref"><span><b>{{ $categorie['naam'] }}</b><span>{{ $categorie['uitleg'] }}</span></span><input type="checkbox" value="{{ $sleutel }}"></label>
                @endif
            @endforeach
            <div style="display:flex;justify-content:flex-end;padding-top:12px"><x-knop data-ck="save">{{ $cookies['knoppen']['opslaan'] }}</x-knop></div>
        </div>
    </div>
</div>
