{{--
    Strook: de kop van het blogarchief, met een kruimelpad en chips die de
    blogs eronder op onderwerp filteren (resources/js/modules/blogfilter.js).

    Velden: label, kop (Kop met nadruk) en tekst. De chips komen uit
    App\Support\Blogcategorie; "Alles" staat aan. Zonder JavaScript doen
    de chips niets en staan alle blogs er.
--}}
@use('App\Support\Blogcategorie')
@use('App\Support\Blogs')
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap stack" data-entrance style="align-items:center;gap:20px;text-align:center;padding-top:72px;padding-bottom:40px">
        <x-kruimelpad :links="['Home' => url('/'), 'Blog' => url(Blogs::ADRES)]" huidig="Archief" />
        <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h1" />
        <p class="lead" style="max-width:calc(44 * var(--ch))" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
        <div class="chips" role="group" aria-label="Filter op onderwerp">
            <x-chip actief data-filter="alles">Alles</x-chip>
            @foreach (Blogcategorie::cases() as $categorie)
                <x-chip :data-filter="$categorie->value">{{ $categorie->label() }}</x-chip>
            @endforeach
        </div>
    </div>
</section>
