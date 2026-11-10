{{--
    De privacyverklaring, als strook juridisch (resources/views/strips).
    De inhoud staat hier vast in de vorm die het CMS straks levert. In #18
    komt deze inhoud uit het CMS en verdwijnt dit blok.

    De tekst is nog een placeholder, zoals in het ontwerp. Romy levert de
    definitieve privacyverklaring en de datum aan in #27. Het e-mailadres
    komt uit config/vertrouwdag.php via <x-mail-link>.
--}}
@use('App\Support\Strip')
@use('Illuminate\Support\Facades\Blade')
@php
    $mail = Blade::render('<x-mail-link>{{ $adres }}</x-mail-link>', ['adres' => config('vertrouwdag.email')]);

    $strips = [
        new Strip('juridisch', 'juridisch', [
            'label' => 'Goed om te weten',
            'kop' => 'Privacy*verklaring*',
            'bijgewerkt' => '[datum]',
            'inleiding' => '<p class="todo">[Plak hier je privacyverklaring, Romy.]</p>',
            'afsluiting' => '<p>Vragen over privacy? Mail naar '.$mail.'.</p>',
        ]),
    ];
@endphp
<x-layouts::site
    title="Privacyverklaring | vertrouwdag"
    description="Hoe vertrouwdag omgaat met jullie persoonsgegevens."
>
    <x-strips :strips="$strips" />
</x-layouts::site>
