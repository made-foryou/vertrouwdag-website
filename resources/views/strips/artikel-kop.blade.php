{{--
    Strook: de kop van een artikel, met kruimelpad, onderwerp, leestijd en
    titel, en daaronder een grote foto in een boog met een sticker.

    Velden: foto (url), alt en sticker (meerdere regels). De blog zelf is
    extra data: blog, een App\Support\Blog. Daar komen de titel, het
    onderwerp en de leestijd vandaan.

    De strook geeft zoekmachines het artikel als BlogPosting (JSON-LD),
    met deze foto als afbeelding.
--}}
@php
    $blog = $strip->blog;

    $artikel = [
        '@context' => 'https://schema.org',
        '@type' => 'BlogPosting',
        'headline' => $blog->platteTitel(),
        'image' => $strip->foto,
        'author' => [
            '@type' => 'Person',
            'name' => config('vertrouwdag.bedrijf.eigenaar'),
            'url' => route('over-mij'),
        ],
        'publisher' => [
            '@type' => 'Organization',
            'name' => config('vertrouwdag.bedrijf.naam'),
        ],
        'mainEntityOfPage' => $blog->url(),
    ];
@endphp
@push('jsonld')
    <x-json-ld :data="$artikel" />
@endpush
<header data-cms-strip="{{ $strip->id }}">
    <div class="art-head" data-reveal>
        <x-kruimelpad :links="['Home' => url('/'), 'Blog' => route('blog')]" :huidig="$blog->categorie->label()" />
        <div class="row" style="gap:12px;justify-content:center">
            <span class="eb eb--sm pill-tag pill-tag--lila">{{ $blog->categorie->label() }}</span>
            @if ($blog->leestijd !== null)
                <span style="font-size:17px;color:var(--grijs)">{{ $blog->leestijd }} min lezen</span>
            @endif
        </div>
        <h1><x-accent :tekst="$blog->titel" /></h1>
    </div>
    <div class="art-hero" data-reveal>
        <div class="art-hero__img"><img src="{{ $strip->foto }}" alt="{{ $strip->alt }}" width="1100" height="733" fetchpriority="high" data-cms-field="foto"></div>
        <div class="sticker sticker--aub art-hero__sticker" aria-hidden="true" data-cms-field="sticker">{!! nl2br(e($strip->sticker)) !!}</div>
    </div>
</header>
