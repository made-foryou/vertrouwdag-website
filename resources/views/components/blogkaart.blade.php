{{--
    Een blog als polaroid, voor /blog en het archief. Een blog die online
    staat is een link naar zijn artikel. Een blog die binnenkort komt is
    een kaart zonder link, met een minder felle foto.

    blog: een App\Support\Blog. positie: de plek in het raster, vanaf 0.
    Die bepaalt hoe scheef de kaart hangt; de middelste van elke drie
    staat iets lager. data-post en data-cat zijn voor het blogfilter.
--}}
@props([
    'blog',
    'positie' => 0,
])
@php
    $element = $blog->isGepubliceerd() ? 'a' : 'article';
    $draaiing = ['-2.5deg', '2deg', '-1.5deg'][$positie % 3];
    $stijl = "transform:rotate({$draaiing})".($positie % 3 === 1 ? ';margin-top:28px' : '');
@endphp
<{{ $element }} @if ($blog->isGepubliceerd()) href="{{ $blog->url() }}" @endif {{ $attributes->class(['pol', 'pol--soon' => $blog->binnenkort])->merge(['style' => $stijl]) }} data-post data-cat="{{ $blog->categorie->value }}">
    <img src="{{ $blog->afbeelding }}" alt="{{ $blog->alt }}" width="1100" height="733" loading="lazy" data-foto>
    <div class="pol__txt"><span class="eb eb--sm">{{ $blog->kenmerk() }}</span><h3 class="h3"><x-accent :tekst="$blog->titel" /></h3><p>{{ $blog->intro }}</p></div>
</{{ $element }}>
