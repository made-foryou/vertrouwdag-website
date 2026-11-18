{{--
    Strook: een band op aubergine voor wie liever eerst appt, met drie
    briefjes naar WhatsApp, e-mail en Instagram. Het nummer, het adres en
    het Instagram-account komen uit de config; het e-mailbriefje toont het
    adres zelf.

    Velden: kop (Kop met nadruk), tekst, app_tekst (op het WhatsApp-briefje)
    en insta_tekst (op het Instagram-briefje). Kleur en draaiing van de
    briefjes liggen vast.
--}}
@use('App\Support\Contact')
<section class="band--aub" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec stack" style="align-items:center;gap:18px;text-align:center">
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" data-reveal />
        <p data-reveal style="font-size:20px" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
        <div class="contact-notes" data-reveal-group>
            <x-briefje kleur="lila" :draaiing="-4" :href="Contact::whatsappUrl()" target="_blank" rel="noopener"><span class="eb eb--sm">WhatsApp</span><span class="contact-notes__big" data-cms-field="app_tekst">{{ $strip->app_tekst }}</span></x-briefje>
            <x-briefje kleur="cream" :draaiing="3" :href="Contact::mailUrl()"><span class="eb eb--sm">E-mail</span><span class="contact-notes__big">{{ config('vertrouwdag.email') }}</span></x-briefje>
            <x-briefje kleur="mos" :draaiing="-2" :href="config('vertrouwdag.instagram')" target="_blank" rel="noopener"><span class="eb eb--sm">Instagram</span><span class="contact-notes__big" data-cms-field="insta_tekst">{{ $strip->insta_tekst }}</span></x-briefje>
        </div>
    </div>
</section>
