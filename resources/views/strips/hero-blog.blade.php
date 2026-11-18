{{--
    Strook: de kop van /blog, met chips die het archief openen op een
    onderwerp (/blog/archief#planning).

    Velden: label, kop (Kop met nadruk) en tekst. De chips komen uit
    App\Support\Blogcategorie.
--}}
@use('App\Support\Blogcategorie')
@use('App\Support\Blogs')
<section class="wrap stack" style="align-items:center;gap:20px;text-align:center;padding-top:72px;padding-bottom:40px" data-cms-strip="{{ $strip->id }}">
    <p class="eb" data-reveal data-cms-field="label">{{ $strip->label }}</p>
    <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h1" data-reveal />
    <p class="lead" data-reveal style="max-width:44ch" data-cms-field="tekst">{{ $strip->tekst }}</p>
    <nav class="chips" aria-label="Onderwerpen" data-reveal>
        <x-chip :href="url(Blogs::ARCHIEF)">Alles</x-chip>
        @foreach (Blogcategorie::cases() as $categorie)
            <x-chip :href="url(Blogs::ARCHIEF).'#'.$categorie->value">{{ $categorie->label() }}</x-chip>
        @endforeach
    </nav>
</section>
