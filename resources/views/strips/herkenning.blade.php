{{--
    Strook: "Klinkt dit bekend?" met citaten van bruidsparen als
    briefjes. Op mobiel zijn de briefjes een swipe-stapel.

    Velden: label, kop (Kop met nadruk), citaten (lijst met tekst) en slotzin.
    De knop is de kennismaking uit de config. Kleur, draaiing en icoon
    horen bij de plek van het briefje en beginnen na het vierde opnieuw.
--}}
@php
    $briefjes = [
        ['kleur' => 'lila', 'draaiing' => -4, 'icoon' => '<svg width="34" height="34" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#401838" stroke-width="1.4" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>'],
        ['kleur' => 'cream', 'draaiing' => 3, 'icoon' => '<svg width="34" height="34" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#404830" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7-4.4-7-9.6C5 7.4 7.2 5 9.6 5c1.1 0 1.9.6 2.4 1.4.5-.8 1.3-1.4 2.4-1.4C16.8 5 19 7.4 19 10.4 19 15.6 12 20 12 20z"/></svg>'],
        ['kleur' => 'mos', 'draaiing' => -2, 'icoon' => '<svg width="34" height="34" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#404830" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h3"/></svg>'],
        ['kleur' => 'zand', 'draaiing' => 5, 'icoon' => '<svg width="34" height="34" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#404830" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="3" width="10" height="18" rx="2"/><path d="M11 18h2"/></svg>'],
    ];
@endphp
<section class="band--aub" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec stack" style="align-items:center;gap:22px;text-align:center">
        <p class="eb eb--lila" data-reveal data-cms-field="label">{{ $strip->label }}</p>
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" data-reveal />
        <div class="notes" data-deck data-reveal-group data-cms-items="citaten">
            @foreach ($strip->citaten as $citaat)
                @php($briefje = $briefjes[$loop->index % count($briefjes)])
                <x-briefje :kleur="$briefje['kleur']" :draaiing="$briefje['draaiing']">
                    {!! $briefje['icoon'] !!}
                    <p data-cms-field="tekst">{{ $citaat['tekst'] }}</p>
                </x-briefje>
            @endforeach
        </div>
        <x-stapel-bolletjes :aantal="count($strip->citaten)" />
        <p data-reveal style="font-size:clamp(24px,2.6vw,32px);line-height:1.4;font-style:italic;max-width:32ch" data-cms-field="slotzin">{{ $strip->slotzin }}</p>
        <x-knop :href="url(config('vertrouwdag.cta.pad'))" variant="lila" style="margin-top:10px">{{ config('vertrouwdag.cta.label') }}</x-knop>
    </div>
</section>
