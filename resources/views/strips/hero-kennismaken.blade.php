{{--
    Strook: de hero van /kennismaken met links de uitnodiging en de
    stappen van het gesprek, en rechts de Calendly-agenda met de polaroid
    van Romy.

    Velden: label, kop (met accent), tekst, stappen (lijst met tekst),
    opmerking (optioneel, de schuine regel onder de stappen), foto (op de
    polaroid) en bijschrift (onder de polaroid). Het nummer van een stap
    volgt de volgorde. De kleur hoort bij de plek en begint na de derde
    opnieuw. Zonder foto staat de foto uit het ontwerp (<x-foto>).

    De agenda ligt vast. Zonder toestemming voor marketing laadt de pagina
    niets van Calendly: er staat alleen een poort met "Agenda tonen" en
    een link naar Calendly. resources/js/modules/calendly-agenda.js laadt
    de agenda in #cal-embed na toestemming voor de categorie in
    data-toestemming, en toont het vangnet
    (#cal-fallback) als de agenda niet verschijnt. De Calendly-link komt
    uit de config.
--}}
@php
    $kleuren = [
        'background:#E0D0E0;color:#401838',
        'background:#E3E6D8;color:#401838',
        'background:#404830;color:#E0D0E0',
    ];
    $calendly = config('vertrouwdag.calendly');
@endphp
<section class="wrap meet" data-cms-strip="{{ $strip->id }}">
    <div class="meet__txt" data-reveal>
        <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
        <h1 class="h1" data-cms-field="kop"><x-accent :tekst="$strip->kop" /></h1>
        <p style="font-size:21px;line-height:1.65;max-width:34ch" data-cms-field="tekst">{{ $strip->tekst }}</p>
        <ol class="mini-steps" data-cms-items="stappen">
            @foreach ($strip->stappen ?? [] as $stap)
                <li><span style="{{ $kleuren[$loop->index % count($kleuren)] }}">{{ $loop->iteration }}</span><p data-cms-field="tekst">{{ $stap['tekst'] }}</p></li>
            @endforeach
        </ol>
        @if (filled($strip->opmerking))
            <p style="font-size:18px;font-style:italic;color:var(--grijs)" data-cms-field="opmerking">{{ $strip->opmerking }}</p>
        @endif
    </div>

    <div class="meet__cal" data-reveal>
        <div class="polaroid tilt meet__pol" style="transform:rotate(-8deg)">
            <x-foto :foto="$strip->foto" terugval="resources/images/romy.jpg" alt="Romy, weddingplanner van vertrouwdag in Zuid-Holland" :breedte="1100" :hoogte="1571" veld="foto" />
            <span style="font-size:17px;bottom:7px" data-cms-field="bijschrift">{{ $strip->bijschrift }}</span>
        </div>
        <div class="cal-box" id="agenda">
            <div class="cal-box__stripe" aria-hidden="true"></div>
            <div class="cal-gate" id="cal-gate">
                <div class="stamp" style="position:absolute;right:22px;top:26px;width:96px;height:112px;border-width:8px;outline-offset:-8px;transform:rotate(7deg)" aria-hidden="true"><img src="{{ Vite::asset('resources/images/monogram-groen.png') }}" alt="" style="width:60px;height:60px"></div>
                <p class="eb">Jullie agenda-moment</p>
                <h2 class="h3">Kies hier <em>een moment</em></h2>
                <p style="font-size:19px;max-width:36ch">De afsprakenplanner van Calendly gebruikt cookies. Die laad ik pas als jullie daar toestemming voor geven.</p>
                <div class="row" style="justify-content:center;gap:12px">
                    <x-knop id="cal-show">Agenda tonen</x-knop>
                    <x-knop :href="$calendly" variant="secundair" target="_blank" rel="noopener">Open in een nieuw tabblad</x-knop>
                </div>
            </div>
            <div class="cal-embed" id="cal-embed" tabindex="-1" data-calendly-url="{{ $calendly }}" data-toestemming="marketing" hidden></div>
            <div class="cal-fallback" id="cal-fallback" tabindex="-1" hidden>
                <p class="eb">Agenda laadt niet</p>
                <h2 class="h3">Open de agenda <em>in een nieuw tabblad</em></h2>
                <p style="font-size:19px;max-width:36ch">De agenda kan hier niet geladen worden, bijvoorbeeld door een adblocker. In een nieuw tabblad werkt hij gewoon.</p>
                <x-knop :href="$calendly" target="_blank" rel="noopener">Kies een moment</x-knop>
                <p style="font-size:17px;font-style:italic;color:var(--grijs)">Of stuur een <x-whatsapp-link>appje</x-whatsapp-link> of <x-mail-link>mail</x-mail-link>.</p>
            </div>
        </div>
    </div>
</section>
