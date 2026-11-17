{{--
    Strook: de kop van een artikel, met kruimelpad, onderwerp, leestijd en
    titel, en daaronder een grote foto in een boog met een sticker.

    Velden: titel (met accent), intro, categorie, leestijd, foto en
    sticker (meerdere regels). De intro staat alleen op de kaart op /blog
    en in het archief, niet in het artikel; het frame herlaadt na het
    opslaan. De sticker staat er alleen als hij gevuld is. Zonder foto (in
    een concept) staat er een vaste foto, zodat de kop zijn vorm houdt.

    De blog is extra data (with()): blog, een App\Support\Blog, of null
    zolang de titel of het onderwerp ontbreekt. Daar komen het onderwerp
    en de leestijd vandaan.

    De strook geeft zoekmachines het artikel als BlogPosting (JSON-LD),
    met deze foto als afbeelding.
--}}
@use('App\Support\Blog')
@use('App\Support\Blogs')
@php
    $blog = $strip->blog;
    $foto = $strip->foto ?? ['url' => Vite::asset(Blog::TERUGVALFOTO), 'alt' => '', 'width' => null, 'height' => null];
@endphp
@if ($blog)
    @push('jsonld')
        <x-json-ld :data="[
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'headline' => $blog->platteTitel(),
            'image' => $blog->afbeelding,
            'author' => [
                '@type' => 'Person',
                'name' => config('vertrouwdag.bedrijf.eigenaar'),
                'url' => url('/over-mij'),
            ],
            'publisher' => [
                '@type' => 'Organization',
                'name' => config('vertrouwdag.bedrijf.naam'),
            ],
            'mainEntityOfPage' => url()->current(),
        ]" />
    @endpush
@endif
<header data-cms-strip="{{ $strip->id }}">
    <div class="art-head" data-reveal>
        <x-kruimelpad :links="['Home' => url('/'), 'Blog' => url(Blogs::ADRES)]" :huidig="$blog?->categorie->label()" />
        @if ($blog)
            <div class="row" style="gap:12px;justify-content:center">
                <span class="eb eb--sm pill-tag pill-tag--lila">{{ $blog->categorie->label() }}</span>
                @if ($blog->leestijd !== null)
                    <span style="font-size:17px;color:var(--grijs)">{{ $blog->leestijd }} min lezen</span>
                @endif
            </div>
        @endif
        <h1 data-cms-field="titel"><x-accent :tekst="$strip->titel" /></h1>
    </div>
    <div class="art-hero" data-reveal>
        <div class="art-hero__img"><img src="{{ $foto['url'] }}" alt="{{ $foto['alt'] }}" width="{{ $foto['width'] ?? 1100 }}" height="{{ $foto['height'] ?? 733 }}" fetchpriority="high" data-cms-field="foto" data-foto></div>
        @if (filled($strip->sticker))
            <div class="sticker sticker--aub art-hero__sticker" aria-hidden="true" data-cms-field="sticker">{!! nl2br(e($strip->sticker)) !!}</div>
        @endif
    </div>
</header>
