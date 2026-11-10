{{--
    Strook: wie het artikel schreef, met een polaroid van Romy en een link
    naar meer over haar.

    Velden: label, tekst en link (tekst en url). De foto ligt vast tot #21.
--}}
<div class="wrap" style="max-width:728px;margin-top:40px" data-cms-strip="{{ $strip->id }}">
    <div class="author" data-reveal>
        <div class="polaroid tilt" style="position:relative;flex:none;width:116px;padding:7px 7px 26px;transform:rotate(-6deg)"><img src="{{ Vite::asset('resources/images/romy.jpg') }}" alt="Romy, weddingplanner van vertrouwdag in Zuid-Holland" width="1100" height="1571" loading="lazy"></div>
        <div class="stack" style="flex:1 1 260px;gap:6px">
            <span class="eb eb--sm" data-cms-field="label">{{ $strip->label }}</span>
            <span style="font-size:19px;line-height:1.5"><span data-cms-field="tekst">{{ $strip->tekst }}</span>@if ($strip->link) <a href="{{ $strip->link['url'] }}" data-cms-field="link">{{ $strip->link['text'] }}</a>@endif</span>
        </div>
    </div>
</div>
