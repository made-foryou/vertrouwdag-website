{{--
    Strook: de kop van het blogarchief, met een kruimelpad en chips die de
    blogs eronder op onderwerp filteren (resources/js/modules/blogfilter.js).

    Velden: label, kop (met accent) en tekst. De chips komen uit
    App\Support\Blogcategorie; "Alles" staat aan. Zonder JavaScript doen
    de chips niets en staan alle blogs er.
--}}
@use('App\Support\Blogcategorie')
@use('App\Support\Blogs')
<section class="wrap stack" style="align-items:center;gap:20px;text-align:center;padding-top:72px;padding-bottom:40px" data-cms-strip="{{ $strip->id }}">
    <x-kruimelpad :links="['Home' => url('/'), 'Blog' => url(Blogs::ADRES)]" huidig="Archief" data-reveal />
    <p class="eb" data-reveal data-cms-field="label">{{ $strip->label }}</p>
    <h1 class="h1" data-reveal data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h1>
    <p class="lead" data-reveal style="max-width:44ch" data-cms-field="tekst">{{ $strip->tekst }}</p>
    <div class="chips" role="group" aria-label="Filter op onderwerp" data-reveal>
        <x-chip actief data-filter="alles">Alles</x-chip>
        @foreach (Blogcategorie::cases() as $categorie)
            <x-chip :data-filter="$categorie->value">{{ $categorie->label() }}</x-chip>
        @endforeach
    </div>
</section>
