{{--
    Strook: de trouwdag zelf als fase op de aubergine band, met briefjes
    over wie wat doet. Op mobiel zijn de briefjes een swipe-stapel.

    Velden: nummer (het nummer van de fase; leeg of iets anders dan een
    positief heel getal geeft geen boogje, want in het CMS is het tekst),
    label, kop (met accent), briefjes (lijst met wie en tekst) en
    slotzin. Kleur en draaiing horen bij de plek van het briefje en
    beginnen na het vierde opnieuw. Het boogje krijgt geen
    data-cms-field, omdat het cijfer in een eigen span staat; een
    wijziging van het nummer herlaadt de frame.
--}}
@php
    $nummer = filter_var(trim((string) $strip->nummer), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);
    $briefjes = [
        ['kleur' => 'lila', 'draaiing' => -4],
        ['kleur' => 'cream', 'draaiing' => 3],
        ['kleur' => 'mos', 'draaiing' => -2],
        ['kleur' => 'zand', 'draaiing' => 4],
    ];
@endphp
<section class="band--aub" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec stack" style="align-items:center;gap:20px;text-align:center">
        @if ($nummer !== false)
            <x-fasenummer :nummer="$nummer" groot data-reveal />
        @endif
        <p class="eb eb--lila" data-reveal data-cms-field="label">{{ $strip->label }}</p>
        <h2 class="h2" data-reveal data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h2>
        <div class="notes" data-deck data-reveal-group data-cms-items="briefjes">
            @foreach ($strip->briefjes as $briefje)
                @php($plek = $briefjes[$loop->index % count($briefjes)])
                <x-briefje :kleur="$plek['kleur']" :draaiing="$plek['draaiing']" style="min-height:190px">
                    <span class="eb eb--sm" data-cms-field="wie">{{ $briefje['wie'] }}</span>
                    <p style="font-size:22px" data-cms-field="tekst">{{ $briefje['tekst'] }}</p>
                </x-briefje>
            @endforeach
        </div>
        <x-stapel-bolletjes :aantal="count($strip->briefjes)" />
        <p data-reveal style="font-size:21px;max-width:46ch" data-cms-field="slotzin">{{ $strip->slotzin }}</p>
    </div>
</section>
