/*
 * The photo of a blog card grows into the photo at the top of the article (#38).
 *
 * The stylesheet names the arch of the article heading blog-photo. Just before the page swaps, the photo of the blog
 * card that links to the next page gets the same name, so the transition between the pages morphs one into the other.
 * Only a blog card does: any other photo in a link, such as the logo, leads to a page without that heading and would
 * float loose from the rest. Both carry data-blog-photo. Any other one with the name, such as the heading of the
 * article this page is, gives it up: two elements with one name break the transition. A page that comes back from the
 * back/forward cache drops the names it handed out.
 */
const NAME = 'blog-photo';

export default function blogPhotoTransition() {
    const named = new Set();

    const name = (element, value) => {
        element.style.viewTransitionName = value;
        named.add(element);
    };

    const forget = () => {
        named.forEach((element) => element.style.removeProperty('view-transition-name'));
        named.clear();
    };

    window.addEventListener('pageswap', (event) => {
        forget();

        const url = event.activation?.entry?.url;

        if (!event.viewTransition || !url) {
            return;
        }

        const target = new URL(url);
        const photo = [...document.querySelectorAll('a[href] img[data-blog-photo]')].find((img) => {
            const link = new URL(img.closest('a').href);

            return link.origin === target.origin && link.pathname === target.pathname;
        });

        if (!photo) {
            return;
        }

        document.querySelectorAll('[data-blog-photo]').forEach((element) => {
            if (element !== photo && getComputedStyle(element).viewTransitionName === NAME) {
                name(element, 'none');
            }
        });
        name(photo, NAME);
    });

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            forget();
        }
    });
}
