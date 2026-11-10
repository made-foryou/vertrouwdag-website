<?php

namespace App\Http\Controllers;

use App\Support\Blogs;
use Illuminate\Contracts\View\View;

/**
 * Een artikel op /blog/{slug}. Alleen een blog die online staat heeft een
 * artikel; de tekst staat in resources/views/pages/artikelen/{slug}.
 */
class BlogartikelController extends Controller
{
    public function __invoke(string $slug): View
    {
        $blog = Blogs::metSlug($slug) ?? abort(404);

        return view("pages.artikelen.{$blog->slug}", ['blog' => $blog]);
    }
}
