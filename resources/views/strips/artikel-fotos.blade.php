{{--
    Strook: een paar polaroids naast elkaar in de kolom van een artikel,
    om en om scheef.

    Velden: fotos (lijst met foto en bijschrift). Een foto is {url, alt,
    width, height, …} uit de Media-bibliotheek; de alt-tekst hoort bij het
    bestand. Zonder foto (in een concept) staat er een vaste foto, zodat
    elk item een polaroid blijft.
--}}
@use('App\Support\Blog')
@php
    $draaiingen = ['-4deg', '3deg'];
    $terugval = ['url' => Vite::asset(Blog::TERUGVALFOTO), 'alt' => '', 'width' => null, 'height' => null];
@endphp
<div class="art-prose" data-cms-strip="{{ $strip->id }}">
    <div class="art-pols" data-reveal-group data-cms-items="fotos">
        @foreach ($strip->fotos ?? [] as $item)
            @php($foto = $item['foto'] ?? $terugval)
            <figure class="polaroid tilt" style="transform:rotate({{ $draaiingen[$loop->index % count($draaiingen)] }})"><img src="{{ $foto['url'] }}" alt="{{ $foto['alt'] }}" width="{{ $foto['width'] ?? 1100 }}" height="{{ $foto['height'] ?? 733 }}" loading="lazy" data-cms-field="foto"><figcaption data-cms-field="bijschrift">{{ $item['bijschrift'] }}</figcaption></figure>
        @endforeach
    </div>
</div>
