{{--
    Strook: de nieuwste blog die online staat, groot, als link naar het
    artikel.

    Velden: label (op het labeltje, "Nieuwste blog") en link_tekst. De
    blog zelf is extra data: blog, een App\Support\Blog of null. Zonder
    blog blijft de strook leeg.
--}}
<section class="wrap" style="padding-bottom:72px" data-cms-strip="{{ $strip->id }}">
    @if ($strip->blog)
        <a class="feature" href="{{ $strip->blog->url() }}" data-reveal>
            <div class="feature__img"><img src="{{ $strip->blog->afbeelding }}" alt="{{ $strip->blog->alt }}" width="1100" height="733"></div>
            <div class="feature__txt">
                <div class="row" style="gap:12px"><span class="eb eb--sm pill-tag" data-cms-field="label">{{ $strip->label }}</span><span class="eb eb--sm">{{ $strip->blog->kenmerk() }}</span></div>
                <h2><x-accent :tekst="$strip->blog->titel" /></h2>
                <p>{{ $strip->blog->intro }}</p>
                <span class="lnk" data-cms-field="link_tekst">{{ $strip->link_tekst }}</span>
            </div>
        </a>
    @endif
</section>
