{{--
    Strook: "Wat zit waarin?", de pakketten naast elkaar in een tabel,
    met als laatste rij de vanaf-prijzen. Op mobiel maakt de stylesheet er
    een matrix met vinkjes van, met de legenda eronder. De hero van
    /diensten linkt naar #vergelijk.

    Velden: label, kop (Kop met nadruk), sticker (meerdere regels), onderdelen
    (lijst met onderdeel en per dienst ja, nee of deels: dag, partial en
    full), prijs_label (de kop van de prijsrij) en slotzin.

    De kolommen, hun namen en prijzen volgen de strook pakketten op
    /diensten: pakketten (van App\Cms\Strips\Vergelijking::with()) is een
    lijst van App\Support\Pakket. Zonder pakketten is er geen tabel, en
    een pakket zonder prijs heeft een lege prijscel.

    De tabel krijgt geen data-cms-items: in de kop en de body staat een
    cel of rij die geen item is, dus een wijziging herlaadt de frame.
--}}
@php
    $kolomKlassen = ['dag' => 'c-dag', 'partial' => 'c-par', 'full' => 'c-full'];
    $antwoorden = ['ja' => 'Ja', 'nee' => 'Nee', 'deels' => 'Deels'];

    $kolommen = array_map(fn (\App\Support\Pakket $pakket): array => [
        'dienst' => $pakket->dienst->value,
        'titel' => $pakket->titel,
        'klasse' => $kolomKlassen[$pakket->dienst->value],
        'prijs' => $pakket->prijs,
    ], $strip->pakketten ?? []);
@endphp
<section id="vergelijk" class="stack" style="max-width:1080px;margin:0 auto;padding:112px 16px 96px;gap:36px" data-cms-strip="{{ $strip->id }}">
    <div class="sec-head" style="position:relative">
        <p class="eb" data-cms-field="label">{{ $strip->label }}</p>
        <x-made-cms::heading :heading="$strip->kop" field="kop" emphasis class="h2" />
        @if ($strip->sticker)
            <div class="sticker sticker--aub" style="right:8px;top:-20px;transform:rotate(10deg)" aria-hidden="true" data-cms-field="sticker"><x-regels :tekst="$strip->sticker" /></div>
        @endif
    </div>
    @if (count($kolommen) > 0)
        <div class="cmp-wrap" tabindex="0" role="region" aria-label="Vergelijking van de pakketten">
            <table class="cmp">
                <thead>
                    <tr>
                        <th scope="col"><span class="sr-only">Onderdeel</span></th>
                        @foreach ($kolommen as $kolom)
                            <th scope="col" class="{{ $kolom['klasse'] }}" data-short="{{ $kolom['titel'] }}">{{ $kolom['titel'] }}</th>
                        @endforeach
                    </tr>
                </thead>
                <tbody>
                    @foreach ($strip->onderdelen ?? [] as $onderdeel)
                        <tr>
                            <th scope="row">{{ $onderdeel['onderdeel'] }}</th>
                            @foreach ($kolommen as $kolom)
                                @php($antwoord = (string) ($onderdeel[$kolom['dienst']] ?? ''))
                                @php($tekst = $antwoorden[$antwoord] ?? throw new InvalidArgumentException("De strook vergelijking kent geen antwoord [{$antwoord}] bij [{$onderdeel['onderdeel']}]; kies ja, nee of deels."))
                                <td class="{{ $antwoord }}" data-l="{{ $kolom['titel'] }}">{{ $tekst }}</td>
                            @endforeach
                        </tr>
                    @endforeach
                    <tr>
                        <th scope="row" data-cms-field="prijs_label">{{ $strip->prijs_label }}</th>
                        @foreach ($kolommen as $kolom)
                            <td data-l="{{ $kolom['titel'] }}">@if ($kolom['prijs'] !== null)<x-prijs :prijs="$kolom['prijs']" :dienst="$kolom['dienst']" /><small class="cmp-ex"><x-prijs :prijs="$kolom['prijs']" :dienst="$kolom['dienst']" excl /> excl. btw</small>@endif</td>
                        @endforeach
                    </tr>
                </tbody>
            </table>
        </div>
        <p class="cmp-legend" aria-hidden="true"><span><i></i>zit erin</span><span><b></b>zit er niet in</span></p>
    @endif
    @if ($strip->slotzin)
        <p class="center" style="font-size:20px" data-cms-field="slotzin">{{ $strip->slotzin }}</p>
    @endif
</section>
