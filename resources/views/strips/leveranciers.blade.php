{{--
    Strook: een oproep aan leveranciers om samen te werken, met een knop
    die een mail opent en een link naar WhatsApp. Het e-mailadres en het
    WhatsApp-nummer komen uit de config.

    Velden: label, kop (Kop met nadruk), tekst, knop_tekst en app_tekst.
--}}
<section class="wrap sec-top" id="samenwerken" data-cms-strip="{{ $strip->id }}">
    <div class="founding founding--lev" data-reveal>
        <div class="stack" style="gap:12px;max-width:600px">
            <p class="eb eb--lila" data-cms-field="label">{{ $strip->label }}</p>
            <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" />
            <p style="font-size:20px" data-cms-field="tekst">{{ $strip->tekst }}</p>
        </div>
        <div class="stack founding__acties">
            <x-mail-link onderwerp="Samenwerking met vertrouwdag" class="btn btn--l" data-cms-field="knop_tekst">{{ $strip->knop_tekst }}</x-mail-link>
            <x-whatsapp-link class="founding__alt" data-cms-field="app_tekst">{{ $strip->app_tekst }}</x-whatsapp-link>
        </div>
    </div>
</section>
