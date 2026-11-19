<?php

use Illuminate\Database\Migrations\Migration;
use Made\CmsPackage\Content\Actions\SetPageShareImage;
use Made\CmsPackage\Content\Actions\UpdatePageMeta;
use Made\CmsPackage\Content\Data\PageMetaChanges;
use Made\CmsPackage\Content\Data\PagePath;
use Made\CmsPackage\Content\Models\Media;
use Made\CmsPackage\Content\Models\Page;

/**
 * Moves the titles, descriptions and share photos the site kept in a fixed list (App\Support\PaginaSeo) into the
 * cms (#23), so Google and a shared link see the same as before. It only fills what is empty: what an editor typed
 * stays. The list is a snapshot of that moment.
 *
 * The services page gets no description: the site writes one with the current prices (FallbackDescription). A
 * share photo is the photo of the design that the media library already holds under that name; without it the page
 * shares the default image of the site.
 */
return new class extends Migration
{
    /**
     * @var array<string, array{title: string, description: ?string, photo: ?string}>
     */
    private const array PAGES = [
        '/' => [
            'title' => 'Jullie dag in vertrouwde handen | vertrouwdag',
            'description' => 'Weddingplanner in Zuid-Holland. De Dag in handen, Hand in hand en Helemaal in handen: van dagcoördinatie tot full service, zodat jullie en jullie familie op de dag zelf alleen maar hoeven te genieten.',
            'photo' => null,
        ],
        '/diensten' => [
            'title' => 'Diensten en prijzen | vertrouwdag weddingplanner',
            'description' => null,
            'photo' => 'ceremonie-buiten',
        ],
        '/werkwijze' => [
            'title' => 'Werkwijze: zo plan ik jullie bruiloft | vertrouwdag',
            'description' => 'Van het eerste kennismakingsgesprek tot de dag na de bruiloft: zo werk ik als weddingplanner in Zuid-Holland, stap voor stap.',
            'photo' => null,
        ],
        '/over-mij' => [
            'title' => 'Over Romy | Persoonlijke weddingplanner in Zuid-Holland',
            'description' => 'Ik ben Romy, de weddingplanner achter vertrouwdag. Punctueel, met oog voor detail en vanuit het hart, zodat jullie op jullie trouwdag alleen maar hoeven te genieten.',
            'photo' => 'romy',
        ],
        '/blog' => [
            'title' => 'Blog: rustig je bruiloft plannen | vertrouwdag',
            'description' => 'Praktische tips voor het plannen van jullie bruiloft, trouwlocaties in Zuid-Holland en een kijkje achter de schermen bij weddingplanner Romy.',
            'photo' => null,
        ],
        '/blog/archief' => [
            'title' => 'Blogarchief: alle artikelen | vertrouwdag',
            'description' => 'Alle blogs van weddingplanner Romy op een rij: tips voor het plannen van jullie bruiloft, locaties in Zuid-Holland en een kijkje achter de schermen.',
            'photo' => null,
        ],
        '/blog/wanneer-weddingplanner-inschakelen' => [
            'title' => 'Wanneer schakel je een weddingplanner in? | vertrouwdag',
            'description' => 'Wanneer schakel je een weddingplanner in? Voor full service het liefst 12 tot 18 maanden van tevoren. Waarom een vroeg begin juist rust oplevert.',
            'photo' => null,
        ],
        '/veelgestelde-vragen' => [
            'title' => 'Veelgestelde vragen over een weddingplanner | vertrouwdag',
            'description' => 'Wat kost een weddingplanner, wanneer schakel je er een in en wat als het regent? Antwoorden op de vragen die bruidsparen mij het vaakst stellen.',
            'photo' => 'hand-in-hand',
        ],
        '/checklist' => [
            'title' => 'Gratis trouwplanning-checklist | vertrouwdag',
            'description' => 'Wat regel je wanneer? Download de gratis trouwplanning-checklist van weddingplanner Romy: maand voor maand, van twaalf maanden ervoor tot de week zelf.',
            'photo' => 'ringen-pioenen',
        ],
        '/kennismaken' => [
            'title' => 'Plan een kennismaking | vertrouwdag weddingplanner',
            'description' => 'Plan een vrijblijvend kennismakingsgesprek van 30 minuten, online of op locatie. Vertel over jullie plannen, ik vertel hoe ik kan helpen.',
            'photo' => 'romy',
        ],
        '/privacyverklaring' => [
            'title' => 'Privacyverklaring | vertrouwdag',
            'description' => 'Hoe vertrouwdag omgaat met jullie persoonsgegevens.',
            'photo' => null,
        ],
        '/cookiebeleid' => [
            'title' => 'Cookiebeleid | vertrouwdag',
            'description' => 'Welke cookies vertrouwdag.nl gebruikt, waarvoor, en hoe jullie je keuze aanpassen.',
            'photo' => null,
        ],
        '/algemene-voorwaarden' => [
            'title' => 'Algemene voorwaarden | vertrouwdag',
            'description' => 'De algemene voorwaarden van vertrouwdag weddingplanning.',
            'photo' => null,
        ],
    ];

    public function up(): void
    {
        foreach (self::PAGES as $path => $meta) {
            $page = Page::query()->atPath(PagePath::fromString($path))->first();

            if ($page === null) {
                continue;
            }

            UpdatePageMeta::run($page, new PageMetaChanges(
                $page->meta_title ?? $meta['title'],
                $page->meta_description ?? $meta['description'],
                $page->in_sitemap,
                $page->indexable,
            ));

            $photo = $meta['photo'] === null || $page->share_image_id !== null ? null : $this->photoOfTheDesign($meta['photo']);

            if ($photo !== null) {
                SetPageShareImage::run($page, $photo->id);
            }
        }
    }

    /**
     * Nothing to undo: the titles and descriptions are content of the cms now, and an editor may have changed them.
     */
    public function down(): void {}

    /**
     * The oldest photo with this name: the seeders put the photos of the design in the library first.
     */
    private function photoOfTheDesign(string $name): ?Media
    {
        return Media::query()->images()->where('name', $name)->oldest('id')->first();
    }
};
