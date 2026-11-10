{{--
    Het cookiebeleid, als strook juridisch (resources/views/strips). De
    inhoud staat hier vast in de vorm die het CMS straks levert. In #18
    komt deze inhoud uit het CMS en verdwijnt dit blok.

    De tekst komt uit het ontwerp; Romy levert de definitieve versie en de
    datum aan in #27. De knop opent de cookie-instellingen van de banner
    (#11). Het e-mailadres komt uit config/vertrouwdag.php via
    <x-mail-link>.
--}}
@use('App\Support\Strip')
@use('Illuminate\Support\Facades\Blade')
@php
    $mail = Blade::render('<x-mail-link>{{ $adres }}</x-mail-link>', ['adres' => config('vertrouwdag.email')]);

    $strips = [
        new Strip('juridisch', 'juridisch', [
            'label' => 'Goed om te weten',
            'kop' => 'Cookie*beleid*',
            'bijgewerkt' => '[datum]',
            'inleiding' => '<p>Op vertrouwdag.nl gebruik ik zo min mogelijk cookies. Alleen wat nodig is om de site te laten werken staat altijd aan. Voor al het andere vraag ik eerst jullie toestemming.</p>',
            'secties' => [
                [
                    'id' => null,
                    'kop' => 'Noodzakelijk',
                    'tekst' => '<p>Ik onthoud jullie cookiekeuze in de browser (<code>vd-cookies</code>), zodat de melding niet bij elke pagina terugkomt. Dit bewaar ik op jullie eigen apparaat en het wordt niet met mij gedeeld.</p>',
                ],
                [
                    'id' => null,
                    'kop' => 'Statistieken',
                    'tekst' => '<p>Met toestemming gebruik ik Google Analytics om te zien hoe de site gebruikt wordt, bijvoorbeeld welke pagina\'s het meest bekeken worden. IP-adressen worden geanonimiseerd. Zonder toestemming wordt Google Analytics niet geladen.</p>',
                ],
                [
                    'id' => null,
                    'kop' => 'Marketing',
                    'tekst' => '<p>De afsprakenplanner op de pagina Kennismaken komt van Calendly. Calendly plaatst eigen cookies, daarom laad ik de agenda pas nadat jullie op "Agenda tonen" klikken of marketingcookies toestaan. Later gebruik ik mogelijk Google Ads; dat valt ook onder deze toestemming.</p>',
                ],
                [
                    'id' => null,
                    'kop' => 'Je keuze aanpassen',
                    'tekst' => '<p>Jullie kunnen je keuze altijd wijzigen.</p>',
                ],
            ],
            'cookieknop' => 'Cookie-instellingen openen',
            'afsluiting' => '<p>Vragen? Mail naar '.$mail.'. Meer over hoe ik met persoonsgegevens omga staat in de <a href="'.e(route('privacyverklaring')).'">privacyverklaring</a>.</p>',
        ]),
    ];
@endphp
<x-layouts::site
    title="Cookiebeleid | vertrouwdag"
    description="Welke cookies vertrouwdag.nl gebruikt, waarvoor, en hoe jullie je keuze aanpassen."
>
    <x-strips :strips="$strips" />
</x-layouts::site>
