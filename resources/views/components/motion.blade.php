{{--
    The one switch for motion (#35), inline in <head> so the classes are on <html> before the first paint.

    js: JavaScript runs. Code that needs JavaScript to work, such as the tabs, hangs off this class.
    motion: the site may move. Only under this class does the stylesheet hide anything to slide it in, so without
    JavaScript everything is there at once. It is off with prefers-reduced-motion: reduce, and follows that setting
    when it changes. Scripts ask resources/js/motion.js.
    arrival: the visit arrives from outside the site (another site, a search engine, a typed address), so the header
    fades in (#36). A click within the site leaves it out, so the header stays put between pages.

    In the edit mode of the CMS nothing moves (#34, decision 4): the frame reloads after a save, and a strip that is
    still invisible or slides in confuses the editor and the outlines. The preview (?made-cms=preview) is what a
    visitor sees, so it moves. The package turns its EditMode on for both, hence the check on the query as well.
--}}
@use('Made\CmsPackage\Content\Data\EditMode')
@use('Made\CmsPackage\Http\Middleware\ResolvePage')
@if (app(EditMode::class)->active && request()->query(ResolvePage::QUERY) === ResolvePage::EDIT)
    <script>document.documentElement.classList.add('js')</script>
@else
    <script>
        (() => {
            const root = document.documentElement;
            const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');
            const update = () => root.classList.toggle('motion', !reducedMotion.matches);
            const cameFromThisSite = document.referrer !== '' && new URL(document.referrer).origin === location.origin;

            root.classList.add('js');
            root.classList.toggle('arrival', !cameFromThisSite);
            update();
            reducedMotion.addEventListener?.('change', update);
        })();
    </script>
@endif
