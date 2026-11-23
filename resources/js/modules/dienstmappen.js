/*
 * Diensten: mappen met tabjes, en ankers die de juiste map openen.
 *
 * Een [data-tabfolders] heeft tabjes (role="tab") die elk met
 * aria-controls naar hun map (role="tabpanel") wijzen. Een klik op een
 * tabje opent zijn map. Met de pijltjes gaat de focus naar het vorige
 * of volgende tabje (rond), met Home en End naar het eerste of laatste,
 * en die map gaat meteen open. Het open tabje heeft aria-selected="true"
 * en als enige tabindex 0. Op een touchscherm breder dan 720px (een
 * tablet) bladert vegen over de mappen naar de vorige of volgende.
 *
 * Een adres met #dagcoordinatie, #partial-planning of #full-service
 * opent die map, ook als je vanaf de homepage komt. Daarna staan de
 * tabjes onder de sticky header in beeld. Tot 720px breed verbergt de
 * stylesheet de mappen en staat er een mapjes-stapel (.tf-m) met per
 * kaart data-for; dan schuift de stapel naar de kaart van het anker.
 * swipe-stapel.js haalt hem naar voren en laat zijn bolletje oplichten.
 *
 * Zonder JavaScript staan de mappen onder elkaar en zijn de tabjes
 * verborgen. Zonder beweging (#35) scrolt het zonder animatie.
 *
 * De strook faq gebruikt dezelfde tabjes voor zijn tabbladen met vragen,
 * zonder vegen en zonder mapjes-stapel: die zoeken hier naar .tf__stack
 * en .tf-m, en slaan over wat er niet is.
 */
import { isMotionAllowed } from '../motion';

const VEEG_AFSTAND = 50; // pixels

export default function dienstmappen() {
    const mappen = document.querySelectorAll('[data-tabfolders]');

    if (!mappen.length) {
        return;
    }

    const mobiel = window.matchMedia('(max-width: 720px)');
    const header = document.querySelector('.site-header');

    // De plek op de pagina zonder transforms: de mappen schuiven bij het
    // laden nog in (data-reveal), en die verschuiving telt hier niet mee.
    const paginaTop = (element) => {
        let top = 0;

        for (let huidig = element; huidig; huidig = huidig.offsetParent) {
            top += huidig.offsetTop;
        }

        return top;
    };

    const scrolNaar = (element, animatie) => {
        const ruimte = (header?.offsetHeight ?? 0) + 16;

        window.scrollTo({
            top: paginaTop(element) - ruimte,
            behavior: animatie && isMotionAllowed() ? 'smooth' : 'auto',
        });
    };

    mappen.forEach((root) => {
        const tabjes = [...root.querySelectorAll('[role="tab"]')];
        const panelen = tabjes.map((tabje) => document.getElementById(tabje.getAttribute('aria-controls')));
        const stapel = root.querySelector('.tf__stack');
        const mobieleStapel = root.parentElement.querySelector('.tf-m [data-deck]');

        let actief = Math.max(0, tabjes.findIndex((tabje) => tabje.getAttribute('aria-selected') === 'true'));

        const openen = (index, { focus = false } = {}) => {
            actief = (index + tabjes.length) % tabjes.length;

            tabjes.forEach((tabje, i) => {
                tabje.setAttribute('aria-selected', String(i === actief));
                tabje.tabIndex = i === actief ? 0 : -1;
            });

            panelen.forEach((panel, i) => panel?.classList.toggle('is-active', i === actief));

            if (focus) {
                tabjes[actief].focus();
            }
        };

        tabjes.forEach((tabje, index) => {
            tabje.addEventListener('click', () => openen(index));

            tabje.addEventListener('keydown', (event) => {
                const naar = {
                    ArrowRight: actief + 1,
                    ArrowLeft: actief - 1,
                    Home: 0,
                    End: tabjes.length - 1,
                }[event.key];

                if (naar === undefined) {
                    return;
                }

                event.preventDefault();
                openen(naar, { focus: true });
            });
        });

        let veegStart = null;

        stapel?.addEventListener('touchstart', (event) => {
            veegStart = mobiel.matches ? null : { x: event.touches[0].clientX, y: event.touches[0].clientY };
        }, { passive: true });

        stapel?.addEventListener('touchend', (event) => {
            if (veegStart === null) {
                return;
            }

            const dx = event.changedTouches[0].clientX - veegStart.x;
            const dy = event.changedTouches[0].clientY - veegStart.y;

            veegStart = null;

            if (Math.abs(dx) > VEEG_AFSTAND && Math.abs(dx) > Math.abs(dy) * 1.5) {
                openen(actief + (dx < 0 ? 1 : -1));
            }
        }, { passive: true });

        const indexVanAnker = () => panelen.findIndex((panel) => panel && `#${panel.id}` === location.hash);

        const naarAnker = (animatie) => {
            const index = indexVanAnker();

            if (index < 0) {
                return;
            }

            openen(index);

            if (!mobiel.matches) {
                scrolNaar(root, animatie);

                return;
            }

            const kaart = mobieleStapel?.querySelector(`[data-for="${CSS.escape(panelen[index].id)}"]`);

            if (!kaart) {
                return;
            }

            scrolNaar(mobieleStapel.parentElement, animatie);
            mobieleStapel.scrollTo({
                left: kaart.offsetLeft - (mobieleStapel.clientWidth - kaart.offsetWidth) / 2,
                behavior: animatie && isMotionAllowed() ? 'smooth' : 'auto',
            });
        };

        // De juiste map meteen open; het scrollen pas na het laden, want
        // de browser springt zelf ook nog naar het anker.
        if (indexVanAnker() > -1) {
            openen(indexVanAnker());
        }

        if (document.readyState === 'complete') {
            naarAnker(false);
        } else {
            window.addEventListener('load', () => setTimeout(() => naarAnker(false), 50));
        }

        window.addEventListener('hashchange', () => naarAnker(true));
    });
}
