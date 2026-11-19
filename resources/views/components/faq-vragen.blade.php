{{--
    De vragen van één tabblad van de strook faq, waarvan de eerste openstaat.

    lijst is de sleutel van de lijst in het CMS (vragen, vragen_2 of
    vragen_3), voor data-cms-items: elk kind van dat element is een vraag.
    Met prijzenzin staat die zin boven elk antwoord waarvan de keuze prijzen
    aanstaat (App\Cms\Strips\Faq).
--}}
@use('App\Cms\Strips\Faq')
@props([
    'lijst',
    'vragen' => [],
    'prijzenzin' => null,
])
<div style="display:contents" data-cms-items="{{ $lijst }}">
    @foreach ($vragen as $vraag)
        <x-faq-item :open="$loop->first" :boven="($vraag['prijzen'] ?? null) === Faq::MET_PRIJZEN ? $prijzenzin : null">
            <x-slot:vraag data-cms-field="vraag">{{ $vraag['vraag'] }}</x-slot:vraag>
            <x-slot:antwoord data-cms-field="antwoord">{!! $vraag['antwoord'] !!}</x-slot:antwoord>
        </x-faq-item>
    @endforeach
</div>
