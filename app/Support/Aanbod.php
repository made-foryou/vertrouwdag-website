<?php

namespace App\Support;

use Made\CmsPackage\Content\Actions\FindPage;
use Made\CmsPackage\Content\Actions\FindPublishedPage;
use Made\CmsPackage\Content\Data\EditMode;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Data\PageStrips;
use Made\CmsPackage\Content\Strips\StripTypes;

/**
 * De diensten met hun vanaf-prijs, zoals Romy ze in de strook pakketten op
 * /diensten zet. Dat is de enige plek voor de prijzen (besluit Menno,
 * 2026-11-12, zie #17): de homepage, de vergelijking, de omschrijving
 * voor zoekmachines en de veelgestelde vragen lezen ze hier.
 *
 * Een bezoeker krijgt de gepubliceerde strook. In de bewerkmodus van het
 * CMS is het de werkversie (het concept, anders de gepubliceerde versie),
 * zodat een gewijzigde prijs meteen overal in het frame staat. Een
 * verborgen strook telt mee: verbergen haalt de prijzen niet weg.
 *
 * Elke instantie leest de pagina één keer. Vraag hem per render op
 * (app(Aanbod::class)), zodat een gewijzigde prijs nooit blijft hangen.
 */
final class Aanbod
{
    /**
     * Het adres van de pagina met de strook pakketten.
     */
    public const string ADRES = '/diensten';

    private const string STROOK = 'pakketten';

    /**
     * @var list<Pakket>|null
     */
    private ?array $pakketten = null;

    public function __construct(private readonly StripTypes $types) {}

    /**
     * De diensten in de volgorde van de strook, elke dienst één keer. Leeg
     * als er op /diensten geen strook pakketten staat.
     *
     * @return list<Pakket>
     */
    public function pakketten(): array
    {
        return $this->pakketten ??= $this->lees();
    }

    public function prijs(Dienst $dienst): ?Prijs
    {
        foreach ($this->pakketten() as $pakket) {
            if ($pakket->dienst === $dienst) {
                return $pakket->prijs;
            }
        }

        return null;
    }

    /**
     * Of er pakketten zijn en elk een (leesbare) prijs heeft. Alleen dan
     * noemt een tekst die alle pakketten opsomt hun bedragen, zoals de
     * omschrijving van /diensten.
     */
    public function isCompleet(): bool
    {
        $pakketten = $this->pakketten();

        foreach ($pakketten as $pakket) {
            if ($pakket->prijs === null) {
                return false;
            }
        }

        return $pakketten !== [];
    }

    /**
     * @return list<Pakket>
     */
    private function lees(): array
    {
        $pakketten = [];

        foreach ($this->waarden()['diensten'] ?? [] as $item) {
            $dienst = is_array($item) && is_string($item['dienst'] ?? null) ? Dienst::tryFrom($item['dienst']) : null;

            if ($dienst === null || isset($pakketten[$dienst->value])) {
                continue;
            }

            $pakketten[$dienst->value] = new Pakket(
                dienst: $dienst,
                soort: (string) ($item['soort'] ?? ''),
                titel: (string) ($item['titel'] ?? ''),
                prijs: Prijs::uitTekst(is_string($item['prijs'] ?? null) ? $item['prijs'] : null),
            );
        }

        return array_values($pakketten);
    }

    /**
     * De waarden van de eerste strook pakketten op /diensten, zoals de view
     * ze krijgt: een ontbrekend veld heeft zijn standaardwaarde.
     *
     * @return array<string, mixed>
     */
    private function waarden(): array
    {
        $adres = PagePath::fromString(self::ADRES);

        if (app(EditMode::class)->active) {
            $page = FindPage::run($adres);
            $stroken = $page === null ? [] : ($page->draft_strips ?? $page->strips);
        } else {
            $stroken = FindPublishedPage::run($adres)?->strips ?? [];
        }

        foreach (PageStrips::fromArray($stroken)->strips as $strip) {
            if ($strip->type === self::STROOK && $this->types->has(self::STROOK)) {
                return $this->types->find(self::STROOK)->render($strip->values);
            }
        }

        return [];
    }
}
