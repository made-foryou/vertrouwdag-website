{{--
    De checklistpagina, opgebouwd uit stroken (resources/views/strips). De
    inhoud staat hier vast in de vorm die het CMS straks levert: per
    strook een Strip met zijn velden. In #18 komt deze inhoud uit het CMS
    en verdwijnt dit blok.

    Het formulier verstuurt nog niets: na een geldige invoer toont het
    het bedankblok. Het echte versturen komt in #25.
--}}
@use('App\Support\Strip')
@php
    $strips = [
        new Strip('hero-checklist', 'hero-checklist', [
            'label' => 'Gratis checklist',
            'kop' => 'Wat regel je *wanneer?*',
            'tekst' => 'De checklist die ik zelf gebruik. Maand voor maand, in de juiste volgorde, met ruimte voor jullie eigen notities.',
        ]),
        new Strip('checklist-bedankt', 'checklist-bedankt', [
            'kop' => 'Dank *jullie wel*',
            'tekst' => 'De checklist is onderweg naar jullie mailbox. Niets ontvangen? Kijk dan even bij reclame of updates.',
            'vraag' => 'Willen jullie het liever samen doornemen?',
        ]),
    ];
@endphp
<x-layouts::site
    title="Gratis trouwplanning-checklist | vertrouwdag"
    description="Wat regel je wanneer? Download de gratis trouwplanning-checklist van weddingplanner Romy: maand voor maand, van twaalf maanden ervoor tot de week zelf."
    og-title="Gratis trouwplanning-checklist · vertrouwdag"
    og-image="resources/images/ringen-pioenen.jpg"
>
    <x-strips :strips="$strips" />
</x-layouts::site>
