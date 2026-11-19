{{--
    Strook: "Waarom vertrouwdag", een kop met een inleiding en de
    kwaliteiten van Romy als briefjes met een icoon. Op mobiel zijn de
    briefjes een swipe-stapel.

    Velden: label, kop (Kop met nadruk), tekst en kwaliteiten (lijst met titel
    en tekst). Kleur, draaiing en icoon horen bij de plek van het briefje
    en beginnen na het derde opnieuw.
--}}
@php
    $briefjes = [
        ['kleur' => 'lila', 'draaiing' => -4, 'icoon' => '<svg width="30" height="30" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#401838" stroke-width="1.4" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>'],
        ['kleur' => 'mos', 'draaiing' => 3, 'icoon' => '<svg width="30" height="30" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#404830" stroke-width="1.4" stroke-linecap="round"><circle cx="11" cy="11" r="6"/><path d="M16 16l4 4"/></svg>'],
        ['kleur' => 'zand', 'draaiing' => -2, 'icoon' => '<svg width="30" height="30" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#404830" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 21s-6-5.3-6-10a6 6 0 0 1 12 0c0 4.7-6 10-6 10z"/><circle cx="12" cy="11" r="2"/></svg>'],
    ];
@endphp
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec stack" style="align-items:center;gap:22px;text-align:center">
        <p class="eb" data-reveal data-cms-field="label">{{ $strip->label }}</p>
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" data-reveal style="max-width:18ch" />
        <p data-reveal style="font-size:21px;line-height:1.65;max-width:48ch" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
        <div class="notes notes--licht" data-deck data-reveal-group style="padding-top:32px" data-cms-items="kwaliteiten">
            @foreach ($strip->kwaliteiten as $kwaliteit)
                @php($plek = $briefjes[$loop->index % count($briefjes)])
                <x-briefje :kleur="$plek['kleur']" :draaiing="$plek['draaiing']" style="text-align:left;align-items:flex-start;gap:10px;box-shadow:var(--schaduw)">
                    {!! $plek['icoon'] !!}
                    <h3 class="h3" data-cms-field="titel">{{ $kwaliteit['titel'] }}</h3>
                    <p style="font-style:normal;font-size:18px;line-height:1.45" data-cms-field="tekst"><x-regels :tekst="$kwaliteit['tekst']" /></p>
                </x-briefje>
            @endforeach
        </div>
        <x-stapel-bolletjes :aantal="count($strip->kwaliteiten)" licht />
    </div>
</section>
