{{--
    Strook: de gratis checklist, met links een polaroid en een papiertje
    met vinkjes en rechts het aanmeldformulier, zonder trouwdatum.

    Velden: label, kop (Kop met nadruk), tekst en foto (op de polaroid). De
    visual is decoratie voor schermlezers (aria-hidden), dus de foto heeft
    een lege alt. Zonder foto staat de foto uit het ontwerp (<x-foto>);
    het bijschrift en het papiertje liggen vast.
--}}
<section id="checklist" data-cms-strip="{{ $strip->id }}">
    <div class="wrap sec">
        <div class="checklist" data-reveal>
            <div class="checklist__media" aria-hidden="true">
                <div class="polaroid polaroid--wide tilt" style="left:6%;top:0;width:70%;padding:10px 10px 40px;transform:rotate(-6deg);z-index:1">
                    <x-foto :foto="$strip->foto" terugval="resources/images/hand-in-hand.jpg" :breedte="1100" :hoogte="733" decoratief veld="foto" loading="lazy" />
                    <span style="font-size:18px">12 maanden te gaan</span>
                </div>
                <div class="paper">
                    <div class="paper__head"><p class="eb" style="font-size:9px">Trouwplanning-checklist</p></div>
                    <div style="padding:8px 22px 0">
                        <div class="paper__row done"><span class="box"><svg width="11" height="11" viewBox="0 0 12 12"><path d="M2 6.5l2.6 2.5L10 3.5" fill="none" stroke="#F6F6F0" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Datum prikken</div>
                        <div class="paper__row done"><span class="box"><svg width="11" height="11" viewBox="0 0 12 12"><path d="M2 6.5l2.6 2.5L10 3.5" fill="none" stroke="#F6F6F0" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round"/></svg></span>Locatie bezichtigen</div>
                        <div class="paper__row"><span class="box"></span>Fotograaf boeken</div>
                        <div class="paper__row"><span class="box"></span>Save the dates</div>
                    </div>
                    <div style="margin-top:auto;padding:0 18px 18px;display:flex;justify-content:flex-end">
                        <div style="width:64px;height:64px;border-radius:999px;background:#401838;display:flex;align-items:center;justify-content:center;transform:rotate(-10deg);box-shadow:var(--schaduw-sm)"><svg width="30" height="30" viewBox="0 0 24 24"><path d="M5 12.5l4.5 4.5L19 7.5" fill="none" stroke="#E0D0E0" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"/></svg></div>
                    </div>
                </div>
            </div>
            <x-checklist-formulier>
                <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
                <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" />
                <p style="font-size:20px" data-cms-field="tekst"><x-regels :tekst="$strip->tekst" /></p>
            </x-checklist-formulier>
        </div>
    </div>
</section>
