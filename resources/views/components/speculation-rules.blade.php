{{--
    Loading ahead (#38): an internal link is prerendered as soon as a visitor rests on it (eagerness moderate), so the
    next page opens without waiting. A browser without speculation rules ignores the script.

    Never prerendered:
    - the CMS, and an address with ?made-cms= (edit or preview): those belong to the editor, not to a visitor;
    - /checklist/openen/: opening it confirms the address of a signup, so it may only run on a real click;
    - a link that opens a new tab or a download, or that opts out with data-no-prerender.
    Links to Calendly and other sites are never prerendered: the rules only match this origin. A prerendered page
    releases scripts with consent (Google Analytics, Calendly) only once it is opened (resources/js/prerender.js), so
    it counts as a visit only then.

    In the edit frame of the CMS nothing is loaded ahead (App\Support\CmsEditFrame).
--}}
@use('App\Support\CmsEditFrame')
@php
    $rules = [
        'prerender' => [[
            'source' => 'document',
            'where' => ['and' => [
                ['href_matches' => '/*'],
                ['not' => ['href_matches' => '/made-cms']],
                ['not' => ['href_matches' => '/made-cms/*']],
                ['not' => ['href_matches' => '/*\\?*made-cms=*']],
                ['not' => ['href_matches' => '/checklist/openen/*']],
                ['not' => ['selector_matches' => '[target=_blank], [download], [data-no-prerender]']],
            ]],
            'eagerness' => 'moderate',
        ]],
    ];
@endphp
@unless (app(CmsEditFrame::class)->contains(request()))
    <script type="speculationrules">@json($rules)</script>
@endunless
