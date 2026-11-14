<?php

namespace App\Support;

use Illuminate\Support\Str;
use Made\CmsPackage\Content\Actions\FindMedia;
use Made\CmsPackage\Content\Data\EditMode;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Data\PageStrips;
use Made\CmsPackage\Content\Models\Page;
use Made\CmsPackage\Content\Strips\StripTypes;

/**
 * De artikelen van de blog, uit het CMS (#20, optie B).
 *
 * Een artikel is een gepubliceerde pagina direct onder /blog met een
 * zichtbare strook artikel-kop. Daar komen de titel, de intro, het
 * onderwerp, de leestijd en de foto vandaan. Een pagina onder /blog
 * zonder artikelkop, zoals het archief, is geen artikel.
 *
 * De nieuwste staat eerst: de pagina die het laatst is aangemaakt
 * (besluit Menno, 2026-11-14). Het package bewaart alleen de laatste
 * publicatie, en die verschuift bij elke correctie.
 *
 * Het overzicht en het archief tonen de gepubliceerde artikelen, ook in
 * de bewerkmodus. Alleen de artikelkop van de pagina zelf volgt daar de
 * werkversie, zoals de stroken van die pagina.
 *
 * Elke instantie leest de artikelen één keer. Vraag hem per render op
 * (app(Blogs::class)), zodat een nieuw artikel nooit ontbreekt.
 */
final class Blogs
{
    /**
     * Het adres van de blog, de ouder van de artikelen.
     */
    public const string ADRES = '/blog';

    /**
     * Het adres van het archief, met een onderwerp als anker
     * (/blog/archief#planning).
     */
    public const string ARCHIEF = '/blog/archief';

    private const string KOP = 'artikel-kop';

    /**
     * @var list<Blog>|null
     */
    private ?array $artikelen = null;

    public function __construct(private readonly StripTypes $types) {}

    /**
     * Elk gepubliceerd artikel, het nieuwste eerst.
     *
     * @return list<Blog>
     */
    public function artikelen(): array
    {
        return $this->artikelen ??= $this->lees();
    }

    /**
     * Het nieuwste artikel, groot op /blog.
     */
    public function uitgelicht(): ?Blog
    {
        return $this->artikelen()[0] ?? null;
    }

    /**
     * De artikelen na het uitgelichte, als kaart op /blog.
     *
     * @return list<Blog>
     */
    public function laatste(int $aantal = 3): array
    {
        return array_slice($this->artikelen(), 1, $aantal);
    }

    /**
     * Of de pagina een artikel is: direct onder /blog, met een zichtbare
     * artikelkop.
     */
    public function isArtikel(Page $page): bool
    {
        return $this->artikelkop($page) !== null;
    }

    /**
     * Het artikel op de pagina, uit haar artikelkop. Null voor een pagina
     * die geen artikel is, of waarvan de artikelkop nog geen titel of
     * onderwerp heeft.
     */
    public function vanPagina(Page $page): ?Blog
    {
        $kop = $this->artikelkop($page);

        if ($kop === null) {
            return null;
        }

        $type = $this->types->find(self::KOP);

        return Blog::uitVelden($type->render($kop, FindMedia::run($type->media($kop))), url((string) $page->route?->path));
    }

    /**
     * @return list<Blog>
     */
    private function lees(): array
    {
        $blog = Page::query()->atPath(PagePath::fromString(self::ADRES))->first();

        if ($blog === null || ! $this->types->has(self::KOP)) {
            return [];
        }

        $koppen = [];

        foreach (Page::query()->published()->childrenOf($blog)->with('route')->orderByDesc('created_at')->orderByDesc('id')->get() as $pagina) {
            $kop = $this->kopIn($pagina->strips);

            if ($kop !== null && $pagina->route !== null) {
                $koppen[] = ['waarden' => $kop, 'url' => url($pagina->route->path)];
            }
        }

        $type = $this->types->find(self::KOP);
        $media = FindMedia::run(array_merge(...array_map(fn (array $kop): array => $type->media($kop['waarden']), $koppen)));
        $artikelen = [];

        foreach ($koppen as $kop) {
            $artikel = Blog::uitVelden($type->render($kop['waarden'], $media), $kop['url']);

            if ($artikel !== null) {
                $artikelen[] = $artikel;
            }
        }

        return $artikelen;
    }

    /**
     * De opgeslagen waarden van de artikelkop van een pagina direct onder
     * /blog. In de bewerkmodus is dat de werkversie.
     *
     * @return array<string, mixed>|null
     */
    private function artikelkop(Page $page): ?array
    {
        $adres = $page->route?->path;

        if ($adres === null || Str::beforeLast($adres, '/') !== self::ADRES || ! $this->types->has(self::KOP)) {
            return null;
        }

        return $this->kopIn(app(EditMode::class)->active ? ($page->draft_strips ?? $page->strips) : $page->strips);
    }

    /**
     * De waarden van de eerste zichtbare artikelkop in de stroken.
     *
     * @param  list<array<string, mixed>>  $stroken
     * @return array<string, mixed>|null
     */
    private function kopIn(array $stroken): ?array
    {
        foreach (PageStrips::fromArray($stroken)->strips as $strip) {
            if ($strip->type === self::KOP && $strip->visible) {
                return $strip->values;
            }
        }

        return null;
    }
}
