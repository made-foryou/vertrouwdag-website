<?php

namespace App\Support;

use Illuminate\Support\Facades\Vite;
use InvalidArgumentException;

/**
 * De blogs uit config/blogs.php, de nieuwste eerst.
 *
 * Dit is de tijdelijke bron. In #20 komen de blogs uit het CMS en geeft
 * een striptype ze via with() aan de strook. De strip-views krijgen ze
 * nu al zo, als $strip->blog of $strip->blogs, en blijven dan gelijk.
 */
final class Blogs
{
    /**
     * @return list<Blog>
     *
     * @throws InvalidArgumentException als een blog een onbekende categorie heeft
     */
    public static function alle(): array
    {
        return array_map(self::maak(...), array_values(config('blogs', [])));
    }

    /**
     * De blogs die online staan, voor het archief.
     *
     * @return list<Blog>
     */
    public static function gepubliceerd(): array
    {
        return array_values(array_filter(self::alle(), fn (Blog $blog): bool => $blog->isGepubliceerd()));
    }

    /**
     * De nieuwste blog die online staat, groot op /blog.
     */
    public static function uitgelicht(): ?Blog
    {
        return self::gepubliceerd()[0] ?? null;
    }

    /**
     * De kaarten onder de uitgelichte blog: de eerste blogs van de lijst
     * zonder de uitgelichte, ook wat binnenkort komt.
     *
     * @return list<Blog>
     */
    public static function laatste(int $aantal = 3): array
    {
        $blogs = self::alle();
        $uitgelicht = array_find_key($blogs, fn (Blog $blog): bool => $blog->isGepubliceerd());

        if ($uitgelicht !== null) {
            unset($blogs[$uitgelicht]);
        }

        return array_slice(array_values($blogs), 0, $aantal);
    }

    /**
     * De blog die online staat op dit adres.
     */
    public static function metSlug(string $slug): ?Blog
    {
        return array_find(self::gepubliceerd(), fn (Blog $blog): bool => $blog->slug === $slug);
    }

    /**
     * @param  array{titel: string, intro: string, categorie: string, leestijd: ?int, afbeelding: string, alt: string, slug: ?string, binnenkort: bool}  $gegevens
     */
    private static function maak(array $gegevens): Blog
    {
        $categorie = Blogcategorie::tryFrom($gegevens['categorie'])
            ?? throw new InvalidArgumentException("De blog [{$gegevens['titel']}] heeft een onbekende categorie [{$gegevens['categorie']}]; kies planning, locaties, budget, inspiratie of achter-de-schermen.");

        return new Blog(
            titel: $gegevens['titel'],
            intro: $gegevens['intro'],
            categorie: $categorie,
            leestijd: $gegevens['leestijd'],
            afbeelding: Vite::asset($gegevens['afbeelding']),
            alt: $gegevens['alt'],
            slug: $gegevens['slug'],
            binnenkort: $gegevens['binnenkort'],
        );
    }
}
