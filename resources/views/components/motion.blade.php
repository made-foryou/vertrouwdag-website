{{--
    The one switch for motion (#35), inline in <head> so the classes are on <html> before the first paint.

    js: JavaScript runs. Code that needs JavaScript to work, such as the tabs, hangs off this class.
    motion: the site may move. Only under this class does the stylesheet hide anything to slide it in, so without
    JavaScript everything is there at once. It is off with prefers-reduced-motion: reduce, and follows that setting
    when it changes. Scripts ask resources/js/motion.js.
    arrival: the visit arrives from outside the site (another site, a search engine, a typed address), so the header
    fades in (#36). A click within the site leaves it out, so the header stays put between pages.

    Without motion the transition between pages is skipped as well (#38). That has to happen here: pagereveal fires
    on the new page before its first render, before any module runs. Skipping rejects the ready promise of the
    transition. Nothing waits for it, so the rejection is caught rather than logged as an error on every click.

    In the edit frame of the CMS nothing moves (App\Support\CmsEditFrame).
--}}
@use('App\Support\CmsEditFrame')
@if (app(CmsEditFrame::class)->contains(request()))
    <script>
        document.documentElement.classList.add('js');
        addEventListener('pagereveal', ({ viewTransition }) => {
            viewTransition?.ready.catch(() => {});
            viewTransition?.skipTransition();
        });
    </script>
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
            addEventListener('pagereveal', ({ viewTransition }) => {
                if (viewTransition && !root.classList.contains('motion')) {
                    viewTransition.ready.catch(() => {});
                    viewTransition.skipTransition();
                }
            });
        })();
    </script>
@endif
