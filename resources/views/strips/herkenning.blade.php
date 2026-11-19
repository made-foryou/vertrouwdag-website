{{--
    Strook: "Klinkt dit bekend?" met citaten van bruidsparen als
    briefjes. Op mobiel zijn de briefjes een swipe-stapel.

    Velden: label, kop (Kop met nadruk), citaten (lijst met tekst en
    icoon) en slotzin. De knop is de kennismaking uit de config. Kleur,
    draaiing en lijnkleur horen bij de plek van het briefje en beginnen na
    het vierde opnieuw. Het icoon kiest de redacteur per citaat
    (App\Support\Icoon); zonder keuze is het dat van de plek.
--}}
@use('App\Support\Icoon')
@php
    $briefjes = [
        ['kleur' => 'lila', 'draaiing' => -4, 'lijnkleur' => '#401838', 'icoon' => Icoon::Klok],
        ['kleur' => 'cream', 'draaiing' => 3, 'lijnkleur' => '#404830', 'icoon' => Icoon::Hart],
        ['kleur' => 'mos', 'draaiing' => -2, 'lijnkleur' => '#404830', 'icoon' => Icoon::Lijstje],
        ['kleur' => 'zand', 'draaiing' => 5, 'lijnkleur' => '#404830', 'icoon' => Icoon::Telefoon],
    ];
@endphp
<section class="band--aub" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec stack" style="align-items:center;gap:22px;text-align:center">
        <p class="eb eb--lila" data-reveal data-cms-field="label">{{ $strip->label }}</p>
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" data-reveal />
        <div class="notes" data-deck data-reveal-group data-cms-items="citaten">
            @foreach ($strip->citaten as $citaat)
                @php
                    $briefje = $briefjes[$loop->index % count($briefjes)];
                    $gekozenIcoon = $citaat['icoon'] ?? null;
                    $icoon = blank($gekozenIcoon)
                        ? $briefje['icoon']
                        : Icoon::tryFrom($gekozenIcoon) ?? throw new InvalidArgumentException("De strook herkenning kent geen icoon [{$gekozenIcoon}]; kies er een uit de lijst in het CMS.");
                @endphp
                <x-briefje :kleur="$briefje['kleur']" :draaiing="$briefje['draaiing']">
                    <x-icoon :icoon="$icoon" :kleur="$briefje['lijnkleur']" :grootte="34" />
                    <p data-cms-field="tekst"><x-regels :tekst="$citaat['tekst']" /></p>
                </x-briefje>
            @endforeach
        </div>
        <x-stapel-bolletjes :aantal="count($strip->citaten)" />
        <p data-reveal style="font-size:clamp(24px,2.6vw,32px);line-height:1.4;font-style:italic;max-width:var(--midden)" data-cms-field="slotzin">{{ $strip->slotzin }}</p>
        <x-knop :href="url(config('vertrouwdag.cta.pad'))" variant="lila" style="margin-top:10px">{{ config('vertrouwdag.cta.label') }}</x-knop>
    </div>
</section>
