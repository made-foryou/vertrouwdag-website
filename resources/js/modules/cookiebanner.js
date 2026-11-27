/*
 * Cookiebanner: de keuze vragen en vrijgeven wat bij de keuze hoort.
 *
 * Zonder geldige keuze verschijnt de banner. Hij pakt de focus niet:
 * hij staat in de HTML direct na de skiplink, dus Tab komt er vanzelf.
 * "Alleen noodzakelijk", "Alles accepteren" en "Keuze opslaan" bewaren de
 * keuze met het contract uit ./toestemming. "Voorkeuren" klapt de
 * categorieën open en dicht.
 *
 * Een element met data-cookie-open opent de banner met de voorkeuren en
 * de bewaarde keuze, en zet de focus op de banner. Escape sluit hem
 * zonder iets te wijzigen, als er al een keuze is. Sluit de banner terwijl
 * de focus erin stond, dan gaat de focus terug naar wat hem opende.
 *
 * Bij elke keuze, ook een van de Calendly-agenda, gaat de banner dicht en
 * draaien de scripts met type="text/plain" waarvan de categorie in
 * data-toestemming nu toestemming heeft. De banner kent die scripts niet,
 * alleen hun categorie. Trekt de bezoeker een toestemming in, dan
 * herlaadt de pagina: wat al geladen is, valt niet terug te draaien.
 *
 * A stored choice releases the scripts only once the page is really
 * opened, not while the browser prerenders it (#38). A page that waited
 * while the visitor chose on another page, prerendered or in the
 * back/forward cache, follows that choice once it is shown. It goes
 * through cookiekeuze, so the Calendly agenda follows it as well, and
 * withdrawn consent reloads the page.
 *
 * The banner slides in and out (#40). On a visit that arrives from
 * outside the site it waits until the entrance of the page has played
 * (#36), so it does not interrupt the hero; if the script runs later than
 * that, it slides in at once. After a click within the site it is simply
 * there, so it stays put in the transition between pages (#38). Opened
 * with data-cookie-open it slides in straight away. While it slides out
 * it is inert; without motion it opens and closes at once.
 */
import { isMotionAllowed } from '../motion';
import { whenActivated } from '../prerender';
import { bewaarKeuze, leesKeuze } from './toestemming';

export default function cookiebanner() {
    const banner = document.getElementById('cookie');

    if (!banner) {
        return;
    }

    const box = banner.querySelector('.cookie__box');
    const voorkeuren = document.getElementById('cookie-prefs');
    const voorkeurenKnop = banner.querySelector('[data-ck="prefs"]');
    const vinkjes = [...banner.querySelectorAll('input[type="checkbox"][value]')];
    let geldend = leesKeuze();
    let opener = null;

    function keuzeVolgens(heeftToestemming) {
        return Object.fromEntries(vinkjes.map((vinkje) => [vinkje.value, heeftToestemming(vinkje)]));
    }

    function toonVoorkeuren(open) {
        voorkeuren.hidden = !open;
        voorkeurenKnop.setAttribute('aria-expanded', String(open));
    }

    function open({ metVoorkeuren = false, door = null, slidesIn = true } = {}) {
        vinkjes.forEach((vinkje) => {
            vinkje.checked = geldend?.[vinkje.value] === true;
        });
        toonVoorkeuren(metVoorkeuren);
        banner.classList.remove('is-leaving');
        banner.classList.toggle('is-sliding-in', slidesIn);
        banner.inert = false;
        banner.hidden = false;

        if (door) {
            opener = door;
            box.focus({ preventScroll: true });
        }
    }

    function sluit() {
        const focusStondErin = banner.contains(document.activeElement);

        if (focusStondErin && opener?.isConnected) {
            opener.focus({ preventScroll: true });
        }

        opener = null;
        slideOut();
    }

    function slideOut() {
        if (banner.hidden) {
            return;
        }

        if (!isMotionAllowed()) {
            banner.hidden = true;

            return;
        }

        banner.inert = true;
        banner.classList.add('is-leaving');
    }

    box.addEventListener('animationend', () => {
        if (!banner.classList.contains('is-leaving')) {
            return;
        }

        banner.classList.remove('is-leaving');
        banner.inert = false;
        banner.hidden = true;
    });

    function openOnceThePageHasArrived() {
        const arrives = document.documentElement.classList.contains('arrival') && isMotionAllowed();

        if (!arrives) {
            open({ slidesIn: false });

            return;
        }

        const entrance = document.getAnimations().filter((animation) => animation.animationName?.startsWith('entrance-'));

        Promise.allSettled(entrance.map((animation) => animation.finished)).then(() => {
            if (geldend === null && banner.hidden) {
                open();
            }
        });
    }

    function geefVrij(keuze) {
        document.querySelectorAll('script[type="text/plain"][data-toestemming]').forEach((wachtend) => {
            if (keuze[wachtend.dataset.toestemming] !== true) {
                return;
            }

            const script = document.createElement('script');

            [...wachtend.attributes].forEach(({ name, value }) => {
                if (name === 'type') {
                    return;
                }

                script.setAttribute(name === 'data-src' ? 'src' : name, value);
            });
            script.textContent = wachtend.textContent;
            wachtend.replaceWith(script);
        });
    }

    function trektIn(vorige, nieuwe) {
        return vorige !== null && Object.keys(vorige).some((categorie) => vorige[categorie] && !nieuwe[categorie]);
    }

    banner.addEventListener('click', (event) => {
        const knop = event.target.closest('[data-ck]');

        switch (knop?.dataset.ck) {
            case 'prefs':
                toonVoorkeuren(voorkeuren.hidden);
                break;
            case 'none':
                bewaarKeuze(keuzeVolgens(() => false));
                break;
            case 'all':
                bewaarKeuze(keuzeVolgens(() => true));
                break;
            case 'save':
                bewaarKeuze(keuzeVolgens((vinkje) => vinkje.checked));
                break;
        }
    });

    document.addEventListener('click', (event) => {
        const knop = event.target.closest('[data-cookie-open]');

        if (knop) {
            open({ metVoorkeuren: true, door: knop });
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !banner.hidden && geldend !== null) {
            sluit();
        }
    });

    document.addEventListener('cookiekeuze', (event) => {
        const vorige = geldend;

        geldend = event.detail;
        sluit();

        if (trektIn(vorige, geldend)) {
            window.location.reload();

            return;
        }

        geefVrij(geldend);
    });

    function followChoiceMadeElsewhere() {
        const stored = leesKeuze();

        if (stored !== null && JSON.stringify(stored) !== JSON.stringify(geldend)) {
            document.dispatchEvent(new CustomEvent('cookiekeuze', { detail: stored }));
        }
    }

    window.addEventListener('pageshow', (event) => {
        if (event.persisted) {
            followChoiceMadeElsewhere();
        }
    });

    if (!geldend) {
        openOnceThePageHasArrived();
    }

    whenActivated(() => {
        followChoiceMadeElsewhere();

        if (geldend) {
            geefVrij(geldend);
        }
    });
}
