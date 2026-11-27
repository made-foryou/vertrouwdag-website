/*
 * Mobiel menu: de knop klapt het hoofdmenu open en dicht.
 *
 * De knop houdt aria-expanded en zijn label bij. Een klik op een link in
 * het menu sluit het. Escape sluit het open menu en zet de focus terug
 * op de knop.
 *
 * The menu also closes on a click outside it, the haze over the page
 * included, and when the focus moves on past it, so Tab after the last
 * link does not wander behind it out of sight (#40). A window that loses
 * the focus leaves it open. The stylesheet slides it open and stops the
 * page behind it from scrolling.
 */
export default function mobielMenu() {
    const toggle = document.querySelector('.nav-toggle');
    const nav = document.getElementById('site-nav');

    if (!toggle || !nav) {
        return;
    }

    const isOpen = () => nav.classList.contains('is-open');
    const isPartOfMenu = (node) => nav.contains(node) || toggle.contains(node);

    const setOpen = (open) => {
        nav.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Menu sluiten' : 'Menu openen');
    };

    toggle.addEventListener('click', () => {
        setOpen(!isOpen());
    });

    nav.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            setOpen(false);
        }
    });

    document.addEventListener('click', (event) => {
        if (isOpen() && !isPartOfMenu(event.target)) {
            setOpen(false);
        }
    });

    const closeWhenFocusLeaves = (event) => {
        if (isOpen() && event.relatedTarget !== null && !isPartOfMenu(event.relatedTarget)) {
            setOpen(false);
        }
    };

    nav.addEventListener('focusout', closeWhenFocusLeaves);
    toggle.addEventListener('focusout', closeWhenFocusLeaves);

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && isOpen()) {
            setOpen(false);
            toggle.focus();
        }
    });
}
