{{--
    Stijlgids met alle gedeelde componenten en het gedrag uit het ontwerp,
    met de inhoud van de pagina's waar ze vandaan komen. Alleen lokaal
    bereikbaar op /componenten (zie routes/web.php).

    De prijzen zijn voorbeelden. Op de site komen ze uit de strook
    pakketten op /diensten (App\Support\Aanbod).
--}}
@use('App\Support\Prijs')
@php
    $prijzen = ['dag' => Prijs::uitTekst('850'), 'partial' => Prijs::uitTekst('1850'), 'full' => Prijs::uitTekst('3500')];
@endphp
<x-layouts::site
    title="Componenten | vertrouwdag"
    description="Alle gedeelde componenten van de site op één pagina. Alleen lokaal."
    og-title="Componenten · vertrouwdag"
    noindex
>
    <section class="wrap legal">
        <p class="eb">Alleen lokaal</p>
        <h1 class="h1">Compo<em>nenten</em></h1>
        <p class="legal__meta">De bouwstenen uit resources/views/components, met de inhoud uit het ontwerp. Test op 375, 768 en 1440 px, en met "Verminder beweging" aan.</p>
    </section>

    {{-- Lopende band (index.html) --}}
    <x-lopende-band :teksten="['Jullie dag in vertrouwde handen', 'Rust, aandacht en overzicht', 'Ik heb het overzicht, jullie genieten', 'Weddingplanner in Zuid-Holland']" />

    {{-- Knoppen --}}
    <section class="wrap sec stack" style="gap:24px">
        <p class="eb" data-reveal>Knoppen</p>
        <div class="row" data-reveal-group>
            <x-knop :href="url('/kennismaken')">Primair</x-knop>
            <x-knop :href="url('/diensten')" variant="secundair">Secundair</x-knop>
            <x-knop :href="url('/kennismaken')" variant="lila">Lila</x-knop>
            <x-knop :href="url('/kennismaken')" variant="aubergine">Aubergine</x-knop>
            <x-knop :href="url('/kennismaken')" klein>Klein</x-knop>
            <x-knop type="submit">Stuur mij de checklist</x-knop>
            <x-knop variant="secundair" data-cookie-open>Cookie-instellingen openen</x-knop>
        </div>
    </section>

    {{-- Prijzen --}}
    <section class="wrap sec stack" style="gap:24px">
        <p class="eb" data-reveal>Prijzen (voorbeeld)</p>
        <p data-reveal style="font-size:21px">De Dag in handen begint bij <x-prijs :prijs="$prijzen['dag']" dienst="dag" /> (<x-prijs :prijs="$prijzen['dag']" dienst="dag" excl /> excl. btw), Hand in hand bij <x-prijs :prijs="$prijzen['partial']" dienst="partial" /> en Helemaal in handen bij <x-prijs :prijs="$prijzen['full']" dienst="full" />, steeds inclusief {{ config('vertrouwdag.btw_percentage') }}% btw.</p>
        <div class="row" data-reveal-group style="gap:48px">
            <x-prijsregel :prijs="$prijzen['dag']" dienst="dag" />
            <x-prijsregel :prijs="$prijzen['partial']" dienst="partial" gedempt />
            <x-prijsregel :prijs="$prijzen['full']" dienst="full" gedempt />
        </div>
    </section>

    {{-- Briefjes in een swipe-stapel (index.html, Herkenning) --}}
    <section class="band--aub">
        <div class="wrap sec stack" style="align-items:center;gap:22px;text-align:center">
            <p class="eb eb--lila" data-reveal>Briefjes in een swipe-stapel</p>
            <h2 class="h2" data-reveal>Klinkt dit <em>een beetje</em> als jullie?</h2>
            <div class="notes" data-deck data-reveal-group>
                <x-briefje kleur="lila" :draaiing="-4">
                    <svg width="34" height="34" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#401838" stroke-width="1.4" stroke-linecap="round"><circle cx="12" cy="12" r="9"/><path d="M12 7v5l3 2"/></svg>
                    <p>"We hebben na het werk gewoon geen tijd om alles uit te zoeken."</p>
                </x-briefje>
                <x-briefje kleur="cream" :draaiing="3">
                    <svg width="34" height="34" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#404830" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><path d="M12 20s-7-4.4-7-9.6C5 7.4 7.2 5 9.6 5c1.1 0 1.9.6 2.4 1.4.5-.8 1.3-1.4 2.4-1.4C16.8 5 19 7.4 19 10.4 19 15.6 12 20 12 20z"/></svg>
                    <p>"Mijn moeder wil helpen, maar ik wil juist dat zij geniet."</p>
                </x-briefje>
                <x-briefje kleur="mos" :draaiing="-2">
                    <svg width="34" height="34" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#404830" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="5" y="3" width="14" height="18" rx="2"/><path d="M9 8h6M9 12h6M9 16h3"/></svg>
                    <p>"Drie offertes, vijf tabbladen en nog steeds geen overzicht."</p>
                </x-briefje>
                <x-briefje kleur="zand" :draaiing="5">
                    <svg width="34" height="34" viewBox="0 0 24 24" aria-hidden="true" fill="none" stroke="#404830" stroke-width="1.4" stroke-linecap="round" stroke-linejoin="round"><rect x="7" y="3" width="10" height="18" rx="2"/><path d="M11 18h2"/></svg>
                    <p>"Wie belt de bloemist als er op de dag iets misgaat?"</p>
                </x-briefje>
            </div>
            <x-stapel-bolletjes :aantal="4" />
            <x-knop :href="url('/kennismaken')" variant="lila" style="margin-top:10px">Plan een kennismaking</x-knop>
        </div>
    </section>

    {{-- Dienstkaarten met link in een swipe-stapel (index.html, Diensten) --}}
    <section class="band">
        <div class="wrap sec stack" style="gap:32px">
            <div class="sec-head" data-reveal>
                <p class="eb">Dienstkaarten met link</p>
                <h2 class="h2">Mijn <em>diensten</em></h2>
            </div>
            <div class="folders" data-deck data-reveal-group>
                <x-dienstkaart kleur="lila" dienst="dag" :prijs="$prijzen['dag']" :href="url('/diensten').'#dagcoordinatie'" :draaiing="-4">
                    <x-slot:soort>Dagcoördinatie</x-slot:soort>
                    <x-slot:titel>De Dag in handen</x-slot:titel>
                    <x-slot:omschrijving>Jullie plannen, ik neem het zes weken ervoor over en ben op de dag het aanspreekpunt.</x-slot:omschrijving>
                </x-dienstkaart>
                <x-dienstkaart kleur="cream" dienst="partial" :prijs="$prijzen['partial']" :href="url('/diensten').'#partial-planning'" :draaiing="4" gespiegeld>
                    <x-slot:soort>Gedeeltelijke planning</x-slot:soort>
                    <x-slot:titel>Hand in hand</x-slot:titel>
                    <x-slot:omschrijving>Locatie staat al? Ik help met leveranciers, planning en budget, en coördineer de dag.</x-slot:omschrijving>
                </x-dienstkaart>
                <x-dienstkaart kleur="groen" dienst="full" :prijs="$prijzen['full']" :href="url('/diensten').'#full-service'" :draaiing="-3">
                    <x-slot:soort>Full service</x-slot:soort>
                    <x-slot:titel>Helemaal in handen</x-slot:titel>
                    <x-slot:omschrijving>Van de eerste locatiebezichtiging tot de dag erna. Jullie kiezen, ik regel de rest.</x-slot:omschrijving>
                </x-dienstkaart>
            </div>
            <x-stapel-bolletjes :aantal="3" licht />
        </div>
    </section>

    {{-- Uitgebreide dienstkaarten (diensten.html, mobiele stapel) --}}
    <section class="wrap sec stack" style="gap:32px">
        <p class="eb" data-reveal>Uitgebreide dienstkaarten (op diensten alleen mobiel)</p>
        <div class="folders" data-deck>
            <x-dienstkaart kleur="lila" dienst="dag" :draaiing="-4" data-for="dagcoordinatie" :kenmerken="['Overdracht zes weken voor de bruiloft', 'Draaiboek en tijdsplanning', 'Afstemming met alle leveranciers', 'Aanwezig van opbouw tot laatste dans']">
                <x-slot:soort>Dagcoördinatie</x-slot:soort>
                <x-slot:titel>De Dag in handen</x-slot:titel>
                <x-slot:omschrijving><strong>Voor stellen</strong> die zelf graag plannen, maar op de dag niets meer willen regelen.</x-slot:omschrijving>
            </x-dienstkaart>
            <x-dienstkaart kleur="cream" dienst="partial" :draaiing="4" gespiegeld data-for="partial-planning" :kenmerken="['Alles uit De Dag in handen', 'Leveranciers voorstellen en offertes vergelijken', 'Planning en budgetoverzicht', 'Drie vaste overlegmomenten']">
                <x-slot:soort>Gedeeltelijke planning</x-slot:soort>
                <x-slot:titel>Hand in hand</x-slot:titel>
                <x-slot:omschrijving><strong>Voor stellen</strong> die de locatie al hebben en hulp willen bij de rest.</x-slot:omschrijving>
            </x-dienstkaart>
            <x-dienstkaart kleur="groen" dienst="full" :draaiing="-3" data-for="full-service" :kenmerken="['Alles uit Hand in hand', 'Locaties zoeken en samen bezichtigen', 'Concept en styling uitwerken', 'Onbeperkt contact, ook de dag erna']">
                <x-slot:soort>Full service</x-slot:soort>
                <x-slot:titel>Helemaal in handen</x-slot:titel>
                <x-slot:omschrijving><strong>Voor stellen</strong> die bij het begin staan en alles in vertrouwde handen willen leggen.</x-slot:omschrijving>
            </x-dienstkaart>
        </div>
        <x-stapel-bolletjes :aantal="3" licht />
    </section>

    {{-- Stapkaarten (index.html, Werkwijze) --}}
    <section class="wrap sec stack" style="gap:56px">
        <div class="werk-head" data-reveal>
            <div class="stack werk-head__txt"><p class="eb">Stapkaarten</p><h2 class="h2">Vier stappen <em>naar ja</em></h2></div>
            <x-knop :href="url('/werkwijze')" variant="secundair">Zo werk ik</x-knop>
        </div>
        <ol class="steps" data-reveal-group>
            <x-stapkaart nummer="1" kleur="lila"><x-slot:titel>Kennismaken</x-slot:titel><x-slot:tekst>Een half uur online. Jullie vertellen, ik luister.</x-slot:tekst></x-stapkaart>
            <x-stapkaart nummer="2" kleur="mos"><x-slot:titel>Voorstel</x-slot:titel><x-slot:tekst>Binnen een week, helder en met een vaste prijs.</x-slot:tekst></x-stapkaart>
            <x-stapkaart nummer="3" kleur="zand"><x-slot:titel>Samen plannen</x-slot:titel><x-slot:tekst>Eén aanspreekpunt voor alle leveranciers.</x-slot:tekst></x-stapkaart>
            <x-stapkaart nummer="4" kleur="groen"><x-slot:titel>Genieten</x-slot:titel><x-slot:tekst>Op jullie dag hoeft niemand iets te regelen.</x-slot:tekst></x-stapkaart>
        </ol>
    </section>

    {{-- Fasenummers (werkwijze.html): de kleur hoort bij het nummer, groot staat op de band --}}
    <section class="wrap sec stack" style="gap:24px">
        <p class="eb" data-reveal>Fasenummers</p>
        <div class="row" data-reveal-group style="gap:28px">
            @foreach (range(1, 4) as $nummer)
                <x-fasenummer :nummer="$nummer" />
            @endforeach
            <x-fasenummer :nummer="5" groot />
        </div>
    </section>

    {{-- Reviews (index.html en over-mij.html) --}}
    <section class="reviews">
        <div class="wrap sec stack" style="align-items:center;gap:40px">
            <div class="pill" data-reveal><h2 class="h2">Wat anderen <em>zeggen</em></h2></div>
            <div class="review-list" data-reveal-group>
                <x-review zegel>
                    <x-slot:label>Collega uit de trouwbranche</x-slot:label>
                    <x-slot:citaat>"[Tekst review leverancier 1]"</x-slot:citaat>
                    <x-slot:naam>[Naam], [Bedrijf]</x-slot:naam>
                </x-review>
                <x-review>
                    <x-slot:label>Collega uit de trouwbranche</x-slot:label>
                    <x-slot:citaat>"[Tekst review leverancier 2]"</x-slot:citaat>
                    <x-slot:naam>[Naam], [Bedrijf]</x-slot:naam>
                </x-review>
            </div>
            <x-review groot zegel data-reveal>
                <x-slot:kop>Kiezen voor wat mijn hart al lang zei</x-slot:kop>
                <x-slot:citaat>Na een pittige periode heb ik bewust stilgestaan bij wat ik echt wil. Het antwoord was er al heel lang: stellen helpen om hun dag zonder zorgen te beleven.</x-slot:citaat>
            </x-review>
        </div>
    </section>

    {{-- FAQ (veelgestelde-vragen.html) --}}
    <section class="wrap" style="max-width:920px;padding-top:56px;padding-bottom:var(--sec)">
        <div class="faq-box" data-reveal>
            <span class="faq-box__tab eb">FAQ-items</span>
            <x-faq-item open>
                <x-slot:vraag>Wanneer schakelen we een weddingplanner in?</x-slot:vraag>
                <x-slot:antwoord><p>Voor Helemaal in handen (full service) het liefst 12 tot 18 maanden van tevoren, zodat we de locatie en datum samen kunnen kiezen. Voor De Dag in handen (dagcoördinatie) is 3 tot 6 maanden ervoor vaak genoeg. Hoe eerder, hoe meer rust.</p></x-slot:antwoord>
            </x-faq-item>
            <x-faq-item>
                <x-slot:vraag>Wat kost een weddingplanner?</x-slot:vraag>
                <x-slot:antwoord><p>De Dag in handen (dagcoördinatie) begint bij <x-prijs :prijs="$prijzen['dag']" dienst="dag" />, Hand in hand (gedeeltelijke planning) bij <x-prijs :prijs="$prijzen['partial']" dienst="partial" /> en Helemaal in handen (full service) bij <x-prijs :prijs="$prijzen['full']" dienst="full" />, steeds inclusief {{ config('vertrouwdag.btw_percentage') }}% btw. Bekijk <a href="{{ url('/diensten') }}">alle diensten</a>.</p></x-slot:antwoord>
            </x-faq-item>
        </div>
    </section>

    {{-- Kruimelpad en chips (archief.html en blog.html) --}}
    <section class="wrap stack" style="align-items:center;gap:20px;text-align:center;padding-top:72px;padding-bottom:72px">
        <x-kruimelpad :links="['Home' => url('/'), 'Blog' => url(App\Support\Blogs::ADRES)]" huidig="Archief" data-reveal />
        <p class="eb" data-reveal>Chips als links (blog)</p>
        <nav class="chips" aria-label="Onderwerpen" data-reveal>
            <x-chip :href="url(App\Support\Blogs::ARCHIEF)">Alles</x-chip>
            <x-chip :href="url(App\Support\Blogs::ARCHIEF).'#planning'">Planning</x-chip>
            <x-chip :href="url(App\Support\Blogs::ARCHIEF).'#locaties'">Locaties in Zuid-Holland</x-chip>
        </nav>
        <p class="eb" data-reveal>Chips als filter (archief)</p>
        <div class="chips" role="group" aria-label="Filter op onderwerp" data-reveal>
            <x-chip actief data-filter="alles">Alles</x-chip>
            <x-chip data-filter="planning">Planning</x-chip>
            <x-chip data-filter="locaties">Locaties in Zuid-Holland</x-chip>
        </div>
    </section>

    {{-- Blogkaarten (blog.html en archief.html): online met link, binnenkort zonder --}}
    <section class="band">
        <div class="wrap sec stack" style="gap:32px">
            <p class="eb" data-reveal>Blogkaarten</p>
            <div class="posts" data-reveal-group>
                <x-blogkaart :blog="new App\Support\Blog('Wanneer schakel je een weddingplanner in? *(En waarom eerder dan je denkt)*', 'De meeste stellen bellen pas als het al druk is. Waarom een vroeg begin juist rust oplevert.', App\Support\Blogcategorie::Planning, 6, Vite::asset('resources/images/paar-meer-herfst.jpg'), 'Bruidspaar bij een meer in de herfst', url('/blog/wanneer-weddingplanner-inschakelen'))" />
                <x-blogkaart :blog="new App\Support\Blog('Landelijk-romantisch trouwen in Zuid-Holland: waar begin je?', 'Van boerderij tot buitenplaats, dicht bij huis.', App\Support\Blogcategorie::Locaties, null, Vite::asset('resources/images/ceremonie-buiten.jpg'), 'Ceremonieplek buiten met witte stoelen', binnenkort: true)" :positie="1" />
                <x-blogkaart :blog="new App\Support\Blog('Waarom jullie familie op jullie trouwdag niets hoeft te regelen', 'Zo mogen jullie ouders en getuigen vooral gast zijn.', App\Support\Blogcategorie::AchterDeSchermen, null, Vite::asset('resources/images/feesttafel.jpg'), 'Gedekte feesttafel met bloemen', binnenkort: true)" :positie="2" />
            </div>
        </div>
    </section>

    {{-- Checklist-formulier (index.html zonder en checklist.html met trouwdatum) --}}
    <section class="wrap sec stack" style="gap:32px">
        <div class="checklist" data-reveal>
            <x-checklist-formulier>
                <p class="eb">Checklist-formulier</p>
                <h2 class="h2">Wat regel je <em>wanneer?</em></h2>
            </x-checklist-formulier>
        </div>
        <x-checklist-formulier id="checklist-form-datum" class="cl-form" trouwdatum data-reveal />
    </section>

    {{-- CTA-blokken (index.html en diensten.html) --}}
    <x-cta-split :kop="['text' => 'Zullen we *kennismaken?*', 'tag' => 'h2']" hart>
        <x-slot:tekst>Een half uur, online en vrijblijvend. Jullie vertellen over jullie plannen, ik vertel hoe ik kan helpen.</x-slot:tekst>
    </x-cta-split>

    <x-cta-split :kop="['text' => 'Twijfelen jullie *welk pakket?*', 'tag' => 'h2']" :foto-hoogte="480" groot>
        <x-slot:tekst>Dat zoeken we samen uit, in een vrijblijvend gesprek van een half uur.</x-slot:tekst>
    </x-cta-split>
</x-layouts::site>
