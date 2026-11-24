{{--
    Strook: de kop van /blog, met chips die het archief openen op een
    onderwerp (/blog/archief#planning).

    Velden: label, kop (Kop met nadruk) en tekst. De chips komen uit
    App\Support\Blogcategorie.
--}}
@use('App\Support\Blogcategorie')
@use('App\Support\Blogs')
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap stack" data-entrance style="align-items:center;gap:20px;text-align:center;padding-top:72px;padding-bottom:40px">
        <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h1" />
        <p class="lead" style="max-width:calc(44 * var(--ch))" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
        <nav class="chips" aria-label="Onderwerpen">
            <x-chip :href="url(Blogs::ARCHIEF)">Alles</x-chip>
            @foreach (Blogcategorie::cases() as $categorie)
                <x-chip :href="url(Blogs::ARCHIEF).'#'.$categorie->value">{{ $categorie->label() }}</x-chip>
            @endforeach
        </nav>
    </div>
</section>
