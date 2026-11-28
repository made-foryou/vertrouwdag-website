{{--
    Strook: een kop met briefjes (in het CMS: "Briefjes"). Op mobiel zijn
    de briefjes een swipe-stapel.

    Velden: variant, nummer, label, kop (Kop met nadruk), tekst, citaten
    (lijst met titel, tekst en icoon) en slotzin. Kleur, draaiing en
    lijnkleur horen bij de plek van het briefje en beginnen na de laatste
    plek opnieuw. Het icoon kiest de redacteur per briefje
    (App\Support\Icoon); zonder keuze is het dat van de plek.

    The variant (Herkenning, Kwaliteiten or Trouwdag) gives the markup each of these had as a strip of its own:
    - herkenning: "Klinkt dit bekend?" on the aubergine band, quotes of couples with an icon, a closing line and the
      button to the kennismaking from the config;
    - kwaliteiten: "Waarom vertrouwdag" on light, an intro, and per note an icon, a title (h3) and a text;
    - trouwdag: the wedding day as a phase on the aubergine band: the number of the phase (empty or anything but a
      positive whole number gives no arch, as it is text in the cms), per note who (titel) and what, and a closing
      line. The arch has no data-cms-field, as the digit is in a span of its own: a change reloads the frame.
    Fields a variant does not show are left out.
--}}
@use('App\Support\Icoon')
@php
    $variant = $strip->variant ?: 'herkenning';
    $stijl = match ($variant) {
        'herkenning' => [
            'donker' => true,
            'gap' => 22,
            'kopStijl' => null,
            'notesKlasse' => 'notes',
            'notesStijl' => null,
            'briefjeStijl' => null,
            'plekken' => [
                ['kleur' => 'lila', 'draaiing' => -4, 'lijnkleur' => '#401838', 'icoon' => Icoon::Klok],
                ['kleur' => 'cream', 'draaiing' => 3, 'lijnkleur' => '#404830', 'icoon' => Icoon::Hart],
                ['kleur' => 'mos', 'draaiing' => -2, 'lijnkleur' => '#404830', 'icoon' => Icoon::Lijstje],
                ['kleur' => 'zand', 'draaiing' => 5, 'lijnkleur' => '#404830', 'icoon' => Icoon::Telefoon],
            ],
            'slotzinStijl' => 'font-size:clamp(24px,2.6vw,32px);line-height:1.4;font-style:italic;max-width:var(--midden)',
        ],
        'kwaliteiten' => [
            'donker' => false,
            'gap' => 22,
            'kopStijl' => 'max-width:calc(18 * var(--ch))',
            'notesKlasse' => 'notes notes--licht',
            'notesStijl' => 'padding-top:32px',
            'briefjeStijl' => 'text-align:left;align-items:flex-start;gap:10px;box-shadow:var(--schaduw)',
            'plekken' => [
                ['kleur' => 'lila', 'draaiing' => -4, 'lijnkleur' => '#401838', 'icoon' => Icoon::Klok],
                ['kleur' => 'mos', 'draaiing' => 3, 'lijnkleur' => '#404830', 'icoon' => Icoon::Vergrootglas],
                ['kleur' => 'zand', 'draaiing' => -2, 'lijnkleur' => '#404830', 'icoon' => Icoon::Locatie],
            ],
            'slotzinStijl' => null,
        ],
        'trouwdag' => [
            'donker' => true,
            'gap' => 20,
            'kopStijl' => null,
            'notesKlasse' => 'notes',
            'notesStijl' => null,
            'briefjeStijl' => 'min-height:190px',
            'plekken' => [
                ['kleur' => 'lila', 'draaiing' => -4],
                ['kleur' => 'cream', 'draaiing' => 3],
                ['kleur' => 'mos', 'draaiing' => -2],
                ['kleur' => 'zand', 'draaiing' => 4],
            ],
            'slotzinStijl' => 'font-size:21px;max-width:calc(46 * var(--ch))',
        ],
        default => throw new InvalidArgumentException("De strook briefjes kent geen variant [{$variant}]; kies herkenning, kwaliteiten of trouwdag."),
    };
    $nummer = $variant === 'trouwdag' ? filter_var(trim((string) $strip->nummer), FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]) : false;
@endphp
<section @if ($stijl['donker']) class="band--aub" @endif data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec stack" style="align-items:center;gap:{{ $stijl['gap'] }}px;text-align:center">
        @if ($nummer !== false)
            <x-fasenummer :nummer="$nummer" groot data-reveal />
        @endif
        <p @class(['eb', 'eb--lila' => $stijl['donker']]) data-reveal="text" data-cms-field="label">{{ $strip->label }}</p>
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" data-reveal="text" :style="$stijl['kopStijl']" />
        @if ($variant === 'kwaliteiten')
            <p data-reveal="text" style="font-size:21px;line-height:1.65;max-width:calc(48 * var(--ch))" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
        @endif
        <div class="{{ $stijl['notesKlasse'] }}" data-deck data-reveal-group="notes" @if ($stijl['notesStijl']) style="{{ $stijl['notesStijl'] }}" @endif data-cms-items="citaten">
            @foreach ($strip->citaten as $citaat)
                @php
                    $plek = $stijl['plekken'][$loop->index % count($stijl['plekken'])];
                    $gekozenIcoon = $citaat['icoon'] ?? null;
                    $icoon = $variant === 'trouwdag' ? null : (blank($gekozenIcoon)
                        ? $plek['icoon']
                        : Icoon::tryFrom($gekozenIcoon) ?? throw new InvalidArgumentException("De strook briefjes kent geen icoon [{$gekozenIcoon}]; kies er een uit de lijst in het CMS."));
                @endphp
                <x-briefje :kleur="$plek['kleur']" :draaiing="$plek['draaiing']" :style="$stijl['briefjeStijl']">
                    @switch ($variant)
                        @case ('kwaliteiten')
                            <x-icoon :icoon="$icoon" :kleur="$plek['lijnkleur']" :grootte="30" />
                            <h3 class="h3" data-cms-field="titel">{{ $citaat['titel'] ?? '' }}</h3>
                            <p style="font-style:normal;font-size:18px;line-height:1.45" data-cms-field="tekst"><x-regels :tekst="$citaat['tekst']" /></p>
                            @break
                        @case ('trouwdag')
                            <span class="eb eb--sm" data-cms-field="titel">{{ $citaat['titel'] ?? '' }}</span>
                            <p style="font-size:22px" data-cms-field="tekst"><x-regels :tekst="$citaat['tekst']" /></p>
                            @break
                        @default
                            <x-icoon :icoon="$icoon" :kleur="$plek['lijnkleur']" :grootte="34" />
                            <p data-cms-field="tekst"><x-regels :tekst="$citaat['tekst']" /></p>
                    @endswitch
                </x-briefje>
            @endforeach
        </div>
        <x-stapel-bolletjes :aantal="count($strip->citaten)" :licht="! $stijl['donker']" />
        @if ($stijl['slotzinStijl'] !== null)
            <p data-reveal="text" style="{{ $stijl['slotzinStijl'] }}" data-cms-field="slotzin">{{ $strip->slotzin }}</p>
        @endif
        @if ($variant === 'herkenning')
            <x-knop :href="url(config('vertrouwdag.cta.pad'))" variant="lila" style="margin-top:10px">{{ config('vertrouwdag.cta.label') }}</x-knop>
        @endif
    </div>
</section>
