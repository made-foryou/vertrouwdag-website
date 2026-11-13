<?php

namespace App\Support;

use Illuminate\Http\Request;
use Made\CmsPackage\Content\Actions\ListMenuPages;
use Made\CmsPackage\Content\Data\PagePath;

/**
 * Een link in het hoofdmenu van de header, ook in het mobiele menu. Het
 * menu komt uit de paginaboom van het CMS (#19): Romy zet een pagina in of
 * uit het menu en bepaalt de volgorde in het pagina's-scherm. Het label is
 * de titel van de pagina.
 *
 * De knop naar de kennismaking en de footerlinks staan in
 * config/vertrouwdag.php, tot het package globale inhoud heeft (besluit 5
 * in #13).
 */
final readonly class Menulink
{
    /**
     * @param  string  $pad  het adres van de pagina, zoals /diensten
     * @param  string  $label  de titel van de pagina in het CMS
     */
    public function __construct(
        public string $pad,
        public string $label,
    ) {}

    /**
     * De pagina's bovenaan de boom die gepubliceerd zijn en in het menu
     * staan, in de volgorde van het pagina's-scherm. Een pagina onder een
     * menu-item komt er niet in: het ontwerp heeft geen submenu.
     *
     * @return list<self>
     */
    public static function hoofdmenu(): array
    {
        $links = [];

        foreach (ListMenuPages::run()->roots as $pagina) {
            if ($pagina->path !== null) {
                $links[] = new self($pagina->path->value, $pagina->title);
            }
        }

        return $links;
    }

    /**
     * Of de link de pagina van het verzoek is: zijn eigen pad en de paden
     * eronder, zodat Blog ook actief is op het archief en de artikelen. De
     * homepage is alleen actief op / zelf, anders is ze het overal.
     */
    public function isActief(Request $request): bool
    {
        if ($this->pad === PagePath::HOME) {
            return $request->is('/');
        }

        $pad = ltrim($this->pad, '/');

        return $request->is($pad, $pad.'/*');
    }
}
