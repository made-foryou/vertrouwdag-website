{{--
    Strook: alle blogs die online staan als polaroids op de band, met een
    knop terug naar /blog. Het filter in de kop verbergt de blogs van
    andere onderwerpen en toont de melding als er niets overblijft.

    Velden: kop (alleen voor schermlezers, zodat de h3's van de kaarten
    na de h1 een h2 hebben), leeg (de melding zonder blogs) en knop (tekst
    en url). De blogs zijn extra data: blogs, een lijst van
    App\Support\Blog.
--}}
<section class="band" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec">
        @if (filled($strip->kop))
            <h2 class="sr-only" data-cms-field="kop">{{ $strip->kop }}</h2>
        @endif
        <div class="posts" data-blog-archive data-reveal-group>
            @foreach ($strip->blogs ?? [] as $blog)
                <x-blogkaart :blog="$blog" :positie="$loop->index" />
            @endforeach
        </div>
        <p class="posts__empty" @if (filled($strip->blogs)) hidden @endif data-cms-field="leeg">{{ $strip->leeg }}</p>
        @if ($strip->knop)
            <div class="center" style="padding-top:48px"><x-knop :href="$strip->knop['url']" variant="secundair" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop></div>
        @endif
    </div>
</section>
