<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Made\CmsPackage\Content\Models\Page;

/**
 * De titel, omschrijving en Open Graph van een pagina, zoals de layout
 * (resources/views/layouts/site.blade.php) ze in de head zet.
 *
 * De vaste lijst in voorAdres() is de enige plek voor de SEO van de
 * pagina's. De vaste views lezen eruit, en een pagina uit het CMS ook
 * (page.blade.php). Zo houdt een pagina haar SEO als ze naar het CMS
 * verhuist (#16 t/m #18). Kan het CMS dit per pagina, dan vervalt de
 * lijst (#23).
 */
final readonly class PaginaSeo
{
    /**
     * De omschrijving van de site in één zin, voor Open Graph en als
     * omschrijving van een pagina die niet in de lijst staat.
     */
    public const string ALGEMENE_OMSCHRIJVING = 'Weddingplanner in Zuid-Holland. Warm en persoonlijk, en tegelijk strak georganiseerd.';

    /**
     * @param  string  $titel  de <title>
     * @param  string  $omschrijving  de meta description
     * @param  string  $ogTitel  de korte titel van een gedeelde link
     * @param  ?string  $ogOmschrijving  null is gelijk aan de omschrijving
     * @param  string  $ogAfbeelding  een pad voor Vite::asset()
     * @param  string  $ogType  website, of article voor een blog
     */
    public function __construct(
        public string $titel,
        public string $omschrijving,
        public string $ogTitel,
        public ?string $ogOmschrijving = null,
        public string $ogAfbeelding = 'resources/images/paar-landschap.jpg',
        public string $ogType = 'website',
    ) {}

    /**
     * De SEO van een adres uit de vaste lijst, of null als het adres er
     * niet in staat. De waarden komen uit het ontwerp, op de geaccepteerde
     * afwijkingen na (#12).
     */
    public static function voorAdres(string $adres): ?self
    {
        return match ('/'.trim($adres, '/')) {
            '/' => new self(
                titel: 'Jullie dag in vertrouwde handen | vertrouwdag',
                omschrijving: 'Weddingplanner in Zuid-Holland. De Dag in handen, Hand in hand en Helemaal in handen: van dagcoördinatie tot full service, zodat jullie en jullie familie op de dag zelf alleen maar hoeven te genieten.',
                ogTitel: 'vertrouwdag · Jullie dag in vertrouwde handen',
                ogOmschrijving: self::ALGEMENE_OMSCHRIJVING,
            ),
            '/diensten' => new self(
                titel: 'Diensten en prijzen | vertrouwdag weddingplanner',
                omschrijving: self::omschrijvingVanDeDiensten(app(Aanbod::class)),
                ogTitel: 'Diensten en prijzen · vertrouwdag',
                ogOmschrijving: self::ALGEMENE_OMSCHRIJVING,
                ogAfbeelding: 'resources/images/ceremonie-buiten.jpg',
            ),
            '/werkwijze' => new self(
                titel: 'Werkwijze: zo plan ik jullie bruiloft | vertrouwdag',
                omschrijving: 'Van het eerste kennismakingsgesprek tot de dag na de bruiloft: zo werk ik als weddingplanner in Zuid-Holland, stap voor stap.',
                ogTitel: 'Werkwijze: zo plan ik jullie bruiloft · vertrouwdag',
            ),
            '/over-mij' => new self(
                titel: 'Over Romy | Persoonlijke weddingplanner in Zuid-Holland',
                omschrijving: 'Ik ben Romy, de weddingplanner achter vertrouwdag. Punctueel, met oog voor detail en vanuit het hart, zodat jullie op jullie trouwdag alleen maar hoeven te genieten.',
                ogTitel: 'Over Romy · vertrouwdag',
                ogAfbeelding: 'resources/images/romy.jpg',
            ),
            '/blog' => new self(
                titel: 'Blog: rustig je bruiloft plannen | vertrouwdag',
                omschrijving: 'Praktische tips voor het plannen van jullie bruiloft, trouwlocaties in Zuid-Holland en een kijkje achter de schermen bij weddingplanner Romy.',
                ogTitel: 'Blog: rustig je bruiloft plannen · vertrouwdag',
                ogAfbeelding: 'resources/images/paar-meer-herfst.jpg',
            ),
            '/blog/archief' => new self(
                titel: 'Blogarchief: alle artikelen | vertrouwdag',
                omschrijving: 'Alle blogs van weddingplanner Romy op een rij: tips voor het plannen van jullie bruiloft, locaties in Zuid-Holland en een kijkje achter de schermen.',
                ogTitel: 'Blogarchief: alle artikelen · vertrouwdag',
                ogAfbeelding: 'resources/images/paar-meer-herfst.jpg',
            ),
            '/veelgestelde-vragen' => new self(
                titel: 'Veelgestelde vragen over een weddingplanner | vertrouwdag',
                omschrijving: 'Wat kost een weddingplanner, wanneer schakel je er een in en wat als het regent? Antwoorden op de vragen die bruidsparen mij het vaakst stellen.',
                ogTitel: 'Veelgestelde vragen over een weddingplanner · vertrouwdag',
                ogAfbeelding: 'resources/images/hand-in-hand.jpg',
            ),
            '/checklist' => new self(
                titel: 'Gratis trouwplanning-checklist | vertrouwdag',
                omschrijving: 'Wat regel je wanneer? Download de gratis trouwplanning-checklist van weddingplanner Romy: maand voor maand, van twaalf maanden ervoor tot de week zelf.',
                ogTitel: 'Gratis trouwplanning-checklist · vertrouwdag',
                ogAfbeelding: 'resources/images/ringen-pioenen.jpg',
            ),
            '/kennismaken' => new self(
                titel: 'Plan een kennismaking | vertrouwdag weddingplanner',
                omschrijving: 'Plan een vrijblijvend kennismakingsgesprek van 30 minuten, online of op locatie. Vertel over jullie plannen, ik vertel hoe ik kan helpen.',
                ogTitel: 'Plan een kennismaking · vertrouwdag',
                ogAfbeelding: 'resources/images/romy.jpg',
            ),
            '/privacyverklaring' => new self(
                titel: 'Privacyverklaring | vertrouwdag',
                omschrijving: 'Hoe vertrouwdag omgaat met jullie persoonsgegevens.',
                ogTitel: 'Privacyverklaring · vertrouwdag',
            ),
            '/cookiebeleid' => new self(
                titel: 'Cookiebeleid | vertrouwdag',
                omschrijving: 'Welke cookies vertrouwdag.nl gebruikt, waarvoor, en hoe jullie je keuze aanpassen.',
                ogTitel: 'Cookiebeleid · vertrouwdag',
            ),
            '/algemene-voorwaarden' => new self(
                titel: 'Algemene voorwaarden | vertrouwdag',
                omschrijving: 'De algemene voorwaarden van vertrouwdag weddingplanning.',
                ogTitel: 'Algemene voorwaarden · vertrouwdag',
            ),
            default => null,
        };
    }

    /**
     * De omschrijving van /diensten noemt elke dienst met zijn vanaf-prijs
     * inclusief btw, want de bezoekers zijn consumenten (besluit Menno,
     * 2026-11-10, zie #6). Namen en prijzen komen uit de strook pakketten,
     * zodat ze een wijziging van Romy volgen (#17). Heeft niet elk pakket
     * een prijs, dan noemt de omschrijving geen bedragen.
     */
    private static function omschrijvingVanDeDiensten(Aanbod $aanbod): string
    {
        if (! $aanbod->isCompleet()) {
            return 'Van dagcoördinatie tot full service: pakketten met een vaste vanaf-prijs, zodat jullie vooraf weten waar jullie aan toe zijn.';
        }

        $diensten = array_map(
            fn (Pakket $pakket): string => sprintf('%s (%s) vanaf %s', $pakket->titel, mb_strtolower($pakket->soort), $pakket->prijs?->inclBtw()),
            $aanbod->pakketten(),
        );

        return Arr::join($diensten, ', ', ' en ').', inclusief btw. Vaste vanaf-prijzen, zodat jullie vooraf weten waar jullie aan toe zijn.';
    }

    /**
     * De SEO van een pagina uit het CMS: uit de vaste lijst op haar adres,
     * en anders gemaakt van haar titel met de algemene omschrijving.
     */
    public static function voorPagina(Page $page): self
    {
        $adres = $page->route?->path;

        return ($adres !== null ? self::voorAdres($adres) : null) ?? new self(
            titel: "{$page->title} | vertrouwdag",
            omschrijving: self::ALGEMENE_OMSCHRIJVING,
            ogTitel: "{$page->title} · vertrouwdag",
        );
    }
}
