{{--
    Strook: de hero van /veelgestelde-vragen met kop en tekst, en rechts
    briefjes met vragen die bruidsparen vaak stellen. Op mobiel zijn de
    briefjes verborgen; ze zijn sfeer en staan ook voor screenreaders uit.

    Velden: label, kop (met accent), tekst, app_tekst (de link naar
    WhatsApp aan het eind van de tekst, met het nummer uit de config) en
    briefjes (lijst met tekst). Kleur, draaiing en de verschuiving van
    het tweede briefje horen bij de plek en beginnen na het derde opnieuw.
--}}
@use('Illuminate\View\ComponentAttributeBag')
@php
    $plekken = [
        ['kleur' => 'lila', 'draaiing' => -5, 'style' => null],
        ['kleur' => 'mos', 'draaiing' => 4, 'style' => 'margin-top:36px'],
        ['kleur' => 'zand', 'draaiing' => -2, 'style' => null],
    ];
@endphp
<section class="wrap hero" style="padding-top:72px;padding-bottom:72px;gap:48px" data-cms-strip="{{ $strip->id }}">
    <div class="hero__txt" data-reveal style="flex-basis:460px;gap:22px">
        <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
        <h1 style="font-size:clamp(46px,6.4vw,88px)" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h1>
        <p class="lead" style="max-width:34ch"><span data-cms-field="tekst">{{ $strip->tekst }}</span> <x-whatsapp-link data-cms-field="app_tekst">{{ $strip->app_tekst }}</x-whatsapp-link>.</p>
    </div>
    <div class="faq-notes" data-reveal-group aria-hidden="true" data-cms-items="briefjes">
        @foreach ($strip->briefjes ?? [] as $briefje)
            @php($plek = $plekken[$loop->index % count($plekken)])
            <x-briefje :kleur="$plek['kleur']" :draaiing="$plek['draaiing']" :attributes="new ComponentAttributeBag(array_filter(['style' => $plek['style']]))">
                <p data-cms-field="tekst">{{ $briefje['tekst'] }}</p>
            </x-briefje>
        @endforeach
    </div>
</section>
