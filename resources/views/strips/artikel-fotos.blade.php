{{--
    Strook: een paar polaroids naast elkaar in de kolom van een artikel,
    om en om scheef.

    Velden: fotos (lijst met foto (url), alt en bijschrift).
--}}
@php
    $draaiingen = ['-4deg', '3deg'];
@endphp
<div class="art-prose" data-cms-strip="{{ $strip->id }}">
    <div class="art-pols" data-reveal-group data-cms-items="fotos">
        @foreach ($strip->fotos ?? [] as $foto)
            <figure class="polaroid tilt" style="transform:rotate({{ $draaiingen[$loop->index % count($draaiingen)] }})"><img src="{{ $foto['foto'] }}" alt="{{ $foto['alt'] }}" width="1100" height="733" loading="lazy" data-cms-field="foto"><figcaption data-cms-field="bijschrift">{{ $foto['bijschrift'] }}</figcaption></figure>
        @endforeach
    </div>
</div>
