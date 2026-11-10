{{--
    Strook: de kop van /blog, met chips die het archief openen op een
    onderwerp (/blog/archief#planning).

    Velden: label, kop (met accent) en tekst. De chips komen uit
    App\Support\Blogcategorie.
--}}
@use('App\Support\Blogcategorie')
<section class="wrap stack" style="align-items:center;gap:20px;text-align:center;padding-top:72px;padding-bottom:40px" data-cms-strip="{{ $strip->id }}">
    <p class="eb" data-reveal data-cms-field="label">{{ $strip->label }}</p>
    <h1 class="display" data-reveal style="font-size:clamp(52px,8vw,120px);padding:0" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h1>
    <p class="lead" data-reveal style="max-width:44ch" data-cms-field="tekst">{{ $strip->tekst }}</p>
    <nav class="chips" aria-label="Onderwerpen" data-reveal>
        <x-chip :href="route('blog.archief')">Alles</x-chip>
        @foreach (Blogcategorie::cases() as $categorie)
            <x-chip :href="route('blog.archief').'#'.$categorie->value">{{ $categorie->label() }}</x-chip>
        @endforeach
    </nav>
</section>
