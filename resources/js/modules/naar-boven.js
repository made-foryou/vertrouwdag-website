/*
 * Elke pagina opent bovenaan, behalve bij een #-link.
 *
 * Browsers zetten na herladen of terug de oude scrollpositie terug; het
 * ontwerp wil elke pagina bovenaan openen. Een adres met een # gaat naar
 * het anker. Zodra iemand zelf scrolt (wiel, vegen of een toets), blijft
 * de pagina staan waar hij is.
 *
 * Chrome herstelt de positie soms pas na het load-event, dus alleen
 * terugscrollen is niet genoeg: scrollRestoration = 'manual' zet dat
 * herstellen uit. Het springen naar een anker blijft gewoon werken.
 */
export default function naarBoven() {
    if ('scrollRestoration' in history) {
        history.scrollRestoration = 'manual';
    }

    let zelfGescrold = false;

    ['wheel', 'touchmove', 'keydown'].forEach((type) => {
        window.addEventListener(type, () => {
            zelfGescrold = true;
        }, { once: true, passive: true });
    });

    const scrolNaarBoven = () => {
        if (!location.hash && !zelfGescrold) {
            window.scrollTo({ top: 0, left: 0, behavior: 'instant' });
        }
    };

    scrolNaarBoven();
    window.addEventListener('pageshow', scrolNaarBoven);
    window.addEventListener('load', scrolNaarBoven);
}
