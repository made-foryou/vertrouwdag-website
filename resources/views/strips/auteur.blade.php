{{--
    Strook: wie het artikel schreef, met een polaroid van Romy en een link
    naar meer over haar.

    Velden: label, tekst, link (tekst en url) en foto (op de polaroid).
    Zonder foto staat de foto uit het ontwerp (<x-foto>).
--}}
<div style="padding-top:40px" data-cms-strip="{{ $strip->id }}">
    <div class="wrap wrap--smal">
        <div class="author" data-reveal>
            <div class="polaroid tilt" style="position:relative;flex:none;width:116px;padding:7px 7px 26px;transform:rotate(-6deg)"><x-foto :foto="$strip->foto" terugval="resources/images/romy.jpg" alt="Romy, weddingplanner van vertrouwdag in Zuid-Holland" :breedte="1100" :hoogte="1571" veld="foto" loading="lazy" /></div>
            <div class="stack" style="flex:1 1 260px;gap:6px">
                <span class="eb eb--sm" data-cms-field="label">{{ $strip->label }}</span>
                <span style="font-size:19px;line-height:1.5"><span data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></span>@if ($strip->link) <a href="{{ $strip->link['url'] }}" data-cms-field="link">{{ $strip->link['text'] }}</a>@endif</span>
            </div>
        </div>
    </div>
</div>
