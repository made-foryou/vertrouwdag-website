<?php

namespace App\Support;

use Illuminate\Support\Arr;
use Made\CmsPackage\Content\Models\Page;

/**
 * The description the site writes for a page without a description of its own in the cms (#23). Only the site
 * knows these: the services page names the current prices, and an article has its intro. Any other page has
 * none, so the card "Hoe Google hem ziet" in the cms tells the editor it is missing.
 */
final class FallbackDescription
{
    public function __construct(
        private readonly Aanbod $aanbod,
        private readonly Blogs $blogs,
    ) {}

    public function forPage(Page $page): ?string
    {
        if ($page->route?->path === Aanbod::ADRES) {
            return $this->describeServices();
        }

        $intro = $this->blogs->vanPagina($page)?->intro;

        return filled($intro) ? $intro : null;
    }

    /**
     * Every service with its starting price including vat, because the visitors are consumers (decision Menno,
     * 2026-11-10, see #6). Names and prices come from the strip "pakketten", so they follow a change by the editor
     * (#17). Without a price for every package, the description names no amounts.
     */
    private function describeServices(): string
    {
        if (! $this->aanbod->isCompleet()) {
            return __('seo.services.without_prices');
        }

        $services = array_map(
            fn (Pakket $pakket): string => __('seo.services.service', [
                'name' => $pakket->titel,
                'kind' => mb_strtolower($pakket->soort),
                'price' => $pakket->prijs?->inclBtw(),
            ]),
            $this->aanbod->pakketten(),
        );

        return __('seo.services.with_prices', ['services' => Arr::join($services, ', ', ' en ')]);
    }
}
