{{--
    Strook: de blogs na de uitgelichte als polaroids op de band, met een
    knop naar het archief.

    Velden: knop (tekst en url). De blogs zijn extra data: blogs, een
    lijst van App\Support\Blog, ook wat binnenkort komt.
--}}
<section class="band" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec">
        <div class="posts" data-reveal-group>
            @foreach ($strip->blogs ?? [] as $blog)
                <x-blogkaart :blog="$blog" :positie="$loop->index" />
            @endforeach
        </div>
        @if ($strip->knop)
            <div class="center" style="padding-top:48px"><x-knop :href="$strip->knop['url']" variant="secundair" data-cms-field="knop">{{ $strip->knop['text'] }}</x-knop></div>
        @endif
    </div>
</section>
