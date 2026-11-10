/*
 * Mobiel menu: de knop klapt het hoofdmenu open en dicht.
 *
 * De knop houdt aria-expanded en zijn label bij. Een klik op een link in
 * het menu sluit het. Escape sluit het open menu en zet de focus terug
 * op de knop.
 */
export default function mobielMenu() {
    const toggle = document.querySelector('.nav-toggle');
    const nav = document.getElementById('site-nav');

    if (!toggle || !nav) {
        return;
    }

    const setOpen = (open) => {
        nav.classList.toggle('is-open', open);
        toggle.setAttribute('aria-expanded', String(open));
        toggle.setAttribute('aria-label', open ? 'Menu sluiten' : 'Menu openen');
    };

    toggle.addEventListener('click', () => {
        setOpen(!nav.classList.contains('is-open'));
    });

    nav.addEventListener('click', (event) => {
        if (event.target.closest('a')) {
            setOpen(false);
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && nav.classList.contains('is-open')) {
            setOpen(false);
            toggle.focus();
        }
    });
}
