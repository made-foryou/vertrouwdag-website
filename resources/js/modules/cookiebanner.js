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
 */
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

    function open({ metVoorkeuren = false, door = null } = {}) {
        vinkjes.forEach((vinkje) => {
            vinkje.checked = geldend?.[vinkje.value] === true;
        });
        toonVoorkeuren(metVoorkeuren);
        banner.hidden = false;

        if (door) {
            opener = door;
            box.focus({ preventScroll: true });
        }
    }

    function sluit() {
        const focusStondErin = banner.contains(document.activeElement);

        banner.hidden = true;

        if (focusStondErin && opener?.isConnected) {
            opener.focus({ preventScroll: true });
        }

        opener = null;
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
        open();
    }

    whenActivated(() => {
        followChoiceMadeElsewhere();

        if (geldend) {
            geefVrij(geldend);
        }
    });
}
