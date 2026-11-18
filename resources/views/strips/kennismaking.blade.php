{{--
    Strook: een uitnodiging om kennis te maken, op aubergine, met de knop
    naar de kennismaking uit de config. Afsluiter van een artikel.

    Velden: label, kop (met accent) en tekst.
--}}
<section class="wrap sec" style="max-width:1088px" data-cms-strip="{{ $strip->id }}">
    <div class="founding" data-reveal>
        <div class="stack" style="gap:12px;flex:1 1 420px">
            <p class="eb eb--lila" data-cms-field="label">{{ $strip->label }}</p>
            <h2 class="h2" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h2>
            <p style="font-size:19px" data-cms-field="tekst">{{ $strip->tekst }}</p>
        </div>
        <x-knop :href="url(config('vertrouwdag.cta.pad'))" variant="lila">{{ config('vertrouwdag.cta.label') }}</x-knop>
    </div>
</section>
