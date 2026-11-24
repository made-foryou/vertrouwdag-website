{{--
    Strook: de hero van /checklist met links een polaroid en een papiertje
    met de eerste punten van de checklist, en rechts de kop, de tekst en
    het aanmeldformulier met trouwdatum.

    Velden: label, kop (Kop met nadruk), tekst en foto (op de polaroid). De
    visual is decoratie voor schermlezers (aria-hidden), dus de foto heeft
    een lege alt. Zonder foto staat de foto uit het ontwerp (<x-foto>);
    het bijschrift en het papiertje liggen vast. Na het versturen toont
    het formulier de strook checklist-bedankt (#bedankt).
--}}
<section data-cms-strip="{{ $strip->id }}">
    <div class="wrap hero" style="gap:64px;padding-bottom:var(--sec)">
        <div class="hero__media cl-media" data-entrance aria-hidden="true">
            <div class="polaroid tilt" style="left:6%;top:4%;width:58%;padding:10px 10px 42px;transform:rotate(-7deg);z-index:1">
                <x-foto :foto="$strip->foto" terugval="resources/images/ringen-pioenen.jpg" :breedte="1100" :hoogte="733" decoratief veld="foto" fetchpriority="high" />
                <span style="font-size:19px">nog 12 maanden</span>
            </div>
            <div class="cl-paper">
                <p class="eb" style="font-size:9px;margin-bottom:10px">Trouwplanning-checklist</p>
                <div class="ck"><i></i>Datum prikken</div>
                <div class="ck"><i></i>Budget op papier</div>
                <div class="ck"><i></i>Locatie bezichtigen</div>
                <div class="ck"><i></i>Fotograaf boeken</div>
                <div class="ck"><i></i>Save the dates</div>
            </div>
        </div>
        <div class="hero__txt" data-entrance style="gap:20px">
            <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
            <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h1" />
            <p style="font-size:21px;max-width:calc(36 * var(--ch))" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
            <x-checklist-formulier class="cl-form" trouwdatum />
        </div>
    </div>
</section>
