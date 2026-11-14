{{--
    Strook: de artikelen na het uitgelichte als polaroids op de band, dan
    de blogs die binnenkort komen, en een knop naar het archief.

    Velden: binnenkort (lijst met titel, intro, categorie en foto) en knop
    (tekst en url). De kaarten zijn extra data (with()): blogs, een lijst
    van App\Support\Blog, eerst de artikelen en dan wat binnenkort komt.
    Die staan samen in één raster, dus is er geen data-cms-items: een
    wijziging in de lijst binnenkort herlaadt het frame na het opslaan.
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
