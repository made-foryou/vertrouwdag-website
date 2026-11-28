{{--
    Strook: de kop van /blog, met chips die het archief openen op een
    onderwerp (/blog/archief#planning).

    Velden: variant, label, kop (Kop met nadruk) en tekst. De chips komen
    uit App\Support\Blogcategorie.

    The variant archief is the markup of the old hero-archief: the head of the blog archive, with a breadcrumb and
    chips that filter the blogs below it (resources/js/modules/blogfilter.js). "Alles" is on. Without JavaScript the
    chips do nothing and every blog is there.
--}}
@use('App\Support\Blogcategorie')
@use('App\Support\Blogs')
@php
    $variant = $strip->variant ?: 'blog';

    if (! in_array($variant, ['blog', 'archief'], true)) {
        throw new InvalidArgumentException("De strook hero-blog kent geen variant [{$variant}]; kies blog of archief.");
    }
@endphp
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap stack" data-entrance style="align-items:center;gap:20px;text-align:center;padding-top:72px;padding-bottom:40px">
        @if ($variant === 'archief')
            <x-kruimelpad :links="['Home' => url('/'), 'Blog' => url(Blogs::ADRES)]" huidig="Archief" />
        @endif
        <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h1" />
        <p class="lead" style="max-width:calc(44 * var(--ch))" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
        @if ($variant === 'archief')
            <div class="chips" role="group" aria-label="Filter op onderwerp">
                <x-chip actief data-filter="alles">Alles</x-chip>
                @foreach (Blogcategorie::cases() as $categorie)
                    <x-chip :data-filter="$categorie->value">{{ $categorie->label() }}</x-chip>
                @endforeach
            </div>
        @else
            <nav class="chips" aria-label="Onderwerpen">
                <x-chip :href="url(Blogs::ARCHIEF)">Alles</x-chip>
                @foreach (Blogcategorie::cases() as $categorie)
                    <x-chip :href="url(Blogs::ARCHIEF).'#'.$categorie->value">{{ $categorie->label() }}</x-chip>
                @endforeach
            </nav>
        @endif
    </div>
</section>
