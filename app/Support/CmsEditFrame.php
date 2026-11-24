<?php

namespace App\Support;

use Illuminate\Http\Request;
use Made\CmsPackage\Content\Data\EditMode;
use Made\CmsPackage\Http\Middleware\ResolvePage;

/**
 * The frame of the CMS in which an editor edits a page (?made-cms=edit). Nothing moves there and nothing is loaded
 * ahead (#34, decision 4): the frame reloads after a save, and motion confuses the editor and the outlines. The
 * package turns its EditMode on for the preview (?made-cms=preview) as well, but the preview is what a visitor sees,
 * so it moves like the site.
 */
final class CmsEditFrame
{
    public function contains(Request $request): bool
    {
        return app(EditMode::class)->active
            && $request->query(ResolvePage::QUERY) === ResolvePage::EDIT;
    }
}
