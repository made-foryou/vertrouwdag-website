{{--
    De algemene voorwaarden, als strook juridisch (resources/views/strips).
    De inhoud staat hier vast in de vorm die het CMS straks levert. In #18
    komt deze inhoud uit het CMS en verdwijnt dit blok.

    De tekst is nog een placeholder, zoals in het ontwerp. Romy levert de
    definitieve voorwaarden en de datum aan in #27.
--}}
@use('App\Support\Strip')
@php
    $strips = [
        new Strip('juridisch', 'juridisch', [
            'label' => 'Goed om te weten',
            'kop' => 'Algemene *voorwaarden*',
            'bijgewerkt' => '[datum]',
            'inleiding' => '<p class="todo">[Plak hier je algemene voorwaarden, Romy.]</p>',
        ]),
    ];
@endphp
<x-layouts::site
    title="Algemene voorwaarden | vertrouwdag"
    description="De algemene voorwaarden van vertrouwdag weddingplanning."
>
    <x-strips :strips="$strips" />
</x-layouts::site>
