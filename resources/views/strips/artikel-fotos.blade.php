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
@endphp
<div data-cms-strip="{{ $strip->id }}">
    <div class="wrap wrap--smal">
        <div class="art-pols" data-reveal-group="notes" data-cms-items="fotos">
            @foreach ($strip->fotos ?? [] as $item)
                <figure class="polaroid tilt" style="transform:rotate({{ $draaiingen[$loop->index % count($draaiingen)] }})"><x-foto :foto="$item['foto'] ?? null" :terugval="Blog::TERUGVALFOTO" :breedte="1100" :hoogte="733" sizes="270px" veld="foto" loading="lazy" /><figcaption data-cms-field="bijschrift">{{ $item['bijschrift'] }}</figcaption></figure>
            @endforeach
        </div>
    </div>
</div>
