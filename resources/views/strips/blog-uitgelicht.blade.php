{{--
    Strook: het nieuwste artikel, groot, als link naar het artikel.

    Velden: label (op het labeltje, "Nieuwste blog") en link_tekst. Het
    artikel is extra data (with()): blog, een App\Support\Blog uit
    App\Support\Blogs, of null. Zonder artikel blijft de strook leeg.
--}}
<section class="wrap" style="padding-bottom:72px" data-cms-strip="{{ $strip->id }}">
    @if ($strip->blog)
        <a class="feature" href="{{ $strip->blog->url() }}" data-reveal>
            <div class="feature__img"><img src="{{ $strip->blog->afbeelding }}" alt="{{ $strip->blog->alt }}" width="1100" height="733" data-foto></div>
            <div class="feature__txt">
                <div class="row" style="gap:12px"><span class="eb eb--sm pill-tag" data-cms-field="label">{{ $strip->label }}</span><span class="eb eb--sm">{{ $strip->blog->kenmerk() }}</span></div>
                <h2 class="h2"><x-made-cms::emphasis :text="$strip->blog->titel" /></h2>
                <p><x-regels :tekst="$strip->blog->intro" /></p>
                <span class="lnk" data-cms-field="link_tekst">{{ $strip->link_tekst }}</span>
            </div>
        </a>
    @endif
</section>
