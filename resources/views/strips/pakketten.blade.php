{{--
    Strook: de drie pakketten als mappen met tabjes. Op desktop één map
    tegelijk, met tabjes erboven (resources/js/modules/dienstmappen.js).
    Op mobiel verbergt de stylesheet de mappen en staat er dezelfde
    mapjes-stapel als op de homepage. Zonder JavaScript staan de mappen
    onder elkaar.

    Velden: label, kop (Kop met nadruk), hint (boven de mappen, alleen met
    JavaScript), hint_mobiel (onder de stapel) en diensten (lijst met
    dienst, soort, titel, omschrijving, kenmerken, foto, bijschrift en
    prijs).

    dienst (dag, partial of full) bepaalt via App\Support\Dienst de kleur
    en het anker: het id van de map, waar de links op de homepage naartoe
    gaan. Zonder foto staat de foto van de dienst uit het ontwerp, op de
    polaroid en op het mapje (<x-foto>). De foto is sfeer, dus altijd
    zonder alt-tekst (#12). omschrijving is opgemaakte tekst met een eigen alinea,
    dus ze staat in een div. kenmerken is één tekst met een kenmerk per
    regel. Het package kent geen lijst in een lijst; een wijziging daarin
    herlaadt de frame. prijs is de tekst van de redacteur, in hele euro's
    exclusief btw (App\Support\Prijs::uitTekst). Een onleesbare prijs
    geeft geen prijsregel. Deze prijzen gelden voor de hele site
    (App\Support\Aanbod).
--}}
@use('App\Support\Dienst')
@use('App\Support\Prijs')
@php
    $plaatsing = [
        'dag' => ['polaroid' => -5, 'kaart' => -4, 'gespiegeld' => false],
        'partial' => ['polaroid' => 4, 'kaart' => 4, 'gespiegeld' => true],
        'full' => ['polaroid' => -3, 'kaart' => -3, 'gespiegeld' => false],
    ];

    $diensten = array_map(function (array $dienst) use ($plaatsing): array {
        $welkeDienst = Dienst::tryFrom($dienst['dienst']) ?? throw new InvalidArgumentException("De strook pakketten kent geen dienst [{$dienst['dienst']}]; kies dag, partial of full.");
        $regels = array_map('trim', preg_split('/\R/', (string) ($dienst['kenmerken'] ?? '')));

        return [
            ...$dienst,
            'welkeDienst' => $welkeDienst,
            'kleur' => $welkeDienst->kleur(),
            'plaatsing' => $plaatsing[$welkeDienst->value],
            'prijs' => Prijs::uitTekst(is_string($dienst['prijs'] ?? null) ? $dienst['prijs'] : null),
            'kenmerken' => array_values(array_filter($regels, fn (string $regel): bool => $regel !== '')),
        ];
    }, $strip->diensten ?? []);
@endphp
<section class="band" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec">
        <div class="sec-head" data-reveal style="margin-bottom:40px">
            <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
            <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" />
            @if ($strip->hint)
                <p class="tf-hint" data-cms-field="hint">{{ $strip->hint }}</p>
            @endif
        </div>

        <div class="tf" data-tabfolders data-reveal>
            <div class="tf__tabs" role="tablist" aria-label="Pakketten" data-cms-items="diensten">
                @foreach ($diensten as $dienst)
                    <button type="button" class="tf__tab tf--{{ $dienst['kleur']->value }}" role="tab" id="tab-{{ $dienst['welkeDienst']->value }}" aria-controls="{{ $dienst['welkeDienst']->anker() }}" aria-selected="{{ $loop->first ? 'true' : 'false' }}" @unless ($loop->first) tabindex="-1" @endunless><span class="tf__nr">{{ str_pad((string) $loop->iteration, 2, '0', STR_PAD_LEFT) }}</span><span data-cms-field="titel">{{ $dienst['titel'] }}</span></button>
                @endforeach
            </div>

            <div class="tf__stack" data-cms-items="diensten">
                @foreach ($diensten as $dienst)
                    <article @class(['tf__panel', 'tf--'.$dienst['kleur']->value, 'is-active' => $loop->first]) role="tabpanel" id="{{ $dienst['welkeDienst']->anker() }}" aria-labelledby="tab-{{ $dienst['welkeDienst']->value }}" tabindex="0">
                        <div class="tf__media">
                            <div class="polaroid tf__pol" style="transform:rotate({{ $dienst['plaatsing']['polaroid'] }}deg)">
                                <x-foto :foto="$dienst['foto'] ?? null" :terugval="$dienst['welkeDienst']->foto()" :breedte="1100" :hoogte="825" decoratief veld="foto" loading="lazy" />
                                <span data-cms-field="bijschrift">{{ $dienst['bijschrift'] }}</span>
                            </div>
                        </div>
                        <div class="tf__body">
                            <p @class(array_filter(['eb', 'eb--sm', $dienst['kleur']->eyebrowKlasse()])) data-cms-field="soort">{{ $dienst['soort'] }}</p>
                            <h2 class="h2" data-cms-field="titel">{{ $dienst['titel'] }}</h2>
                            <div class="tf__desc" data-cms-field="omschrijving">{!! $dienst['omschrijving'] !!}</div>
                            @if (count($dienst['kenmerken']) > 0)
                                <ul class="hl">
                                    @foreach ($dienst['kenmerken'] as $kenmerk)
                                        <li><svg width="14" height="13" fill="{{ $dienst['kleur']->hartKleur() }}" aria-hidden="true"><use href="#heart"/></svg>{{ $kenmerk }}</li>
                                    @endforeach
                                </ul>
                            @endif
                            <div class="tf__foot">
                                <x-prijsregel class="tf__price" :prijs="$dienst['prijs']" :dienst="$dienst['welkeDienst']->value" :gedempt="$dienst['kleur']->isGedempt()" />
                            </div>
                            <div class="tf__cta"><x-knop :href="url(config('vertrouwdag.cta.pad'))" :variant="$dienst['kleur']->knopVariant()">{{ config('vertrouwdag.cta.label') }}</x-knop></div>
                        </div>
                    </article>
                @endforeach
            </div>
        </div>

        <div class="tf-m">
            <div class="folders" data-deck data-cms-items="diensten">
                @foreach ($diensten as $dienst)
                    <x-dienstkaart :kleur="$dienst['kleur']->value" :dienst="$dienst['welkeDienst']->value" :prijs="$dienst['prijs']" :foto="$dienst['foto'] ?? null" :draaiing="$dienst['plaatsing']['kaart']" :gespiegeld="$dienst['plaatsing']['gespiegeld']" :kenmerken="$dienst['kenmerken']" :data-for="$dienst['welkeDienst']->anker()">
                        <x-slot:soort data-cms-field="soort">{{ $dienst['soort'] }}</x-slot:soort>
                        <x-slot:titel data-cms-field="titel">{{ $dienst['titel'] }}</x-slot:titel>
                        <x-slot:omschrijving data-cms-field="omschrijving">{!! $dienst['omschrijving'] !!}</x-slot:omschrijving>
                    </x-dienstkaart>
                @endforeach
            </div>
            <x-stapel-bolletjes :aantal="count($diensten)" licht />
            @if ($strip->hint_mobiel)
                <p class="tf-m__hint" data-cms-field="hint_mobiel">{{ $strip->hint_mobiel }}</p>
            @endif
        </div>
    </div>
</section>
