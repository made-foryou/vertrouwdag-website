<?php

namespace App\Cms\Strips;

use App\Support\Aanbod;
use App\Support\FaqSchema;
use App\Support\Pakket;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Blade;
use Made\CmsPackage\Content\Strips\Fields\Choice;
use Made\CmsPackage\Content\Strips\Fields\Items;
use Made\CmsPackage\Content\Strips\Fields\RichText;
use Made\CmsPackage\Content\Strips\Fields\Text;
use Made\CmsPackage\Content\Strips\StripType;

/**
 * De strook faq in het CMS: de veelgestelde vragen in een kaart met een
 * tabje, waarvan de eerste openstaat. Hij rendert met de view
 * resources/views/strips/faq.blade.php.
 *
 * Een antwoord is opgemaakte tekst. De prijzen staan alleen in de strook
 * pakketten op /diensten (#17). Daarom typt een redacteur geen bedragen,
 * maar zet hij per vraag de keuze prijzen aan. De strook zet dan boven het
 * antwoord een zin met elk pakket en zijn vanaf-prijs inclusief btw uit
 * App\Support\Aanbod (besluit Menno, 2026-11-12, zie #18). Heeft niet elk
 * pakket een prijs, dan valt die zin weg.
 *
 * with() geeft de zin als prijzenzin, en de vragen als FAQPage voor
 * zoekmachines als schema, met de zin erbij. Zo zijn de pagina en de
 * gestructureerde gegevens altijd gelijk.
 */
final class Faq extends StripType
{
    /**
     * De waarde van de keuze prijzen die de zin boven het antwoord zet.
     */
    public const string MET_PRIJZEN = 'noemen';

    public function key(): string
    {
        return 'faq';
    }

    public function label(): string
    {
        return 'Veelgestelde vragen';
    }

    public function description(): string
    {
        return 'Vragen met een antwoord dat openklapt, ook voor zoekmachines. Een antwoord kan de prijzen van de pakketten noemen.';
    }

    public function fields(): array
    {
        return [
            Text::make('label')
                ->default('Alle vragen')
                ->hint('Het tabje boven de kaart.'),
            Items::make('vragen', [
                Text::make('vraag')->required(),
                RichText::make('antwoord')->required(),
                Choice::make('prijzen', [
                    self::MET_PRIJZEN => 'Noem de vanaf-prijzen boven het antwoord',
                ])->hint('De prijzen inclusief btw komen uit de pakketten op Diensten. Leeg: alleen het antwoord.'),
            ])
                ->min(1)
                ->itemLabel('Vraag')
                ->default([
                    [
                        'vraag' => 'Wanneer schakelen we een weddingplanner in?',
                        'antwoord' => '<p>Voor Helemaal in handen (full service) het liefst 12 tot 18 maanden van tevoren, zodat we de locatie en datum samen kunnen kiezen. Voor De Dag in handen (dagcoördinatie) is 3 tot 6 maanden ervoor vaak genoeg. Hoe eerder, hoe meer rust.</p>',
                    ],
                    [
                        'vraag' => 'Wat kost een weddingplanner?',
                        'antwoord' => '<p>Na de kennismaking krijgen jullie een voorstel met een vaste prijs. <a href="'.Aanbod::ADRES.'">Bekijk wat er in elk pakket zit</a>.</p>',
                        'prijzen' => self::MET_PRIJZEN,
                    ],
                    [
                        'vraag' => 'Wat is het verschil tussen de pakketten?',
                        'antwoord' => '<p>Bij De Dag in handen plannen jullie zelf en neem ik het zes weken voor de bruiloft over. Bij Hand in hand help ik ook met leveranciers en budget. Bij Helemaal in handen doen we alles samen, vanaf het allereerste begin.</p>',
                    ],
                    [
                        'vraag' => 'Werk je ook buiten Zuid-Holland?',
                        'antwoord' => '<p>Mijn thuisbasis is Zuid-Holland, van Rotterdam tot de Hoeksche Waard. Trouwen jullie net daarbuiten, dan kijk ik graag of het past. Reiskosten spreken we vooraf af.</p>',
                    ],
                    [
                        'vraag' => 'Wat als het regent?',
                        'antwoord' => '<p>Er is altijd een plan B, en dat staat al in het draaiboek voordat het nodig is. Op de dag zelf beslis ik samen met de locatie, zodat jullie er niet mee bezig hoeven te zijn.</p>',
                    ],
                    [
                        'vraag' => 'Hoe gaat het kennismakingsgesprek?',
                        'antwoord' => '<p>Online, ongeveer 30 minuten, en vrijblijvend. Jullie vertellen over jullie plannen, ik stel vragen en vertel hoe ik werk. Liever fysiek afspreken kan ook. <a href="/kennismaken">Plan direct een moment</a>.</p>',
                    ],
                    [
                        'vraag' => 'Werk je met vaste leveranciers?',
                        'antwoord' => '<p>Ik ken veel goede leveranciers in de regio en stel ze voor als ze bij jullie passen. Jullie zitten nergens aan vast.</p>',
                    ],
                    [
                        'vraag' => 'Moet onze familie nog iets doen op de dag zelf?',
                        'antwoord' => '<p>Nee. Leveranciers, vragen en onverwachte dingen komen bij mij terecht. Jullie ouders, getuigen en vrienden zijn gewoon gast.</p>',
                    ],
                    [
                        'vraag' => 'Hoeveel contact hebben we tijdens de voorbereiding?',
                        'antwoord' => '<p>We spreken vaste overlegmomenten af, en tussendoor kunnen jullie mij mailen of appen. Jullie weten altijd wat de volgende stap is.</p>',
                    ],
                    [
                        'vraag' => 'Kunnen we een pakket aanpassen?',
                        'antwoord' => '<p>Ja, maatwerk is mogelijk. In het kennismakingsgesprek kijken we wat jullie nodig hebben, en dat zet ik in het voorstel.</p>',
                    ],
                    [
                        'vraag' => 'Hoe groot mag onze bruiloft zijn?',
                        'antwoord' => '<p>Ik ben gespecialiseerd in persoonlijke, intieme bruiloften, maar ook een groter feest regel ik met dezelfde aandacht voor detail.</p>',
                    ],
                ])
                ->hint('De eerste vraag staat open.'),
        ];
    }

    /**
     * @param  array<string, mixed>  $values
     * @return array{prijzenzin: ?string, schema: array<string, mixed>}
     */
    public function with(array $values): array
    {
        $prijzenzin = $this->prijzenzin(app(Aanbod::class));
        $vragen = [];

        foreach ($values['vragen'] ?? [] as $vraag) {
            $metPrijzen = $prijzenzin !== null && ($vraag['prijzen'] ?? null) === self::MET_PRIJZEN;

            $vragen[] = [
                'vraag' => (string) ($vraag['vraag'] ?? ''),
                'antwoord' => ($metPrijzen ? "<p>{$prijzenzin}</p>" : '').($vraag['antwoord'] ?? ''),
            ];
        }

        return [
            'prijzenzin' => $prijzenzin,
            'schema' => FaqSchema::vanVragen($vragen),
        ];
    }

    /**
     * De pakketten met hun vanaf-prijs inclusief btw in één zin, zoals "De
     * Dag in handen (dagcoördinatie) begint bij €1.028,50, Hand in hand
     * (gedeeltelijke planning) bij €2.238,50 en …, steeds inclusief 21%
     * btw." Null als niet elk pakket een prijs heeft.
     */
    private function prijzenzin(Aanbod $aanbod): ?string
    {
        if (! $aanbod->isCompleet()) {
            return null;
        }

        $delen = [];

        foreach ($aanbod->pakketten() as $index => $pakket) {
            $delen[] = sprintf('%s (%s) %s %s', e($pakket->titel), e(mb_strtolower($pakket->soort)), $index === 0 ? 'begint bij' : 'bij', $this->prijs($pakket));
        }

        return Arr::join($delen, ', ', ' en ').', steeds inclusief '.config('vertrouwdag.btw_percentage').'% btw.';
    }

    private function prijs(Pakket $pakket): string
    {
        return trim(Blade::render('<x-prijs :prijs="$prijs" :dienst="$dienst" />', ['prijs' => $pakket->prijs, 'dienst' => $pakket->dienst->value]));
    }
}
