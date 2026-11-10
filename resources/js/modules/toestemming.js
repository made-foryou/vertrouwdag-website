/*
 * Toestemming: de cookiekeuze van de bezoeker lezen en bewaren.
 *
 * Dit is het contract uit #11. De cookiebanner (#11) en alles wat pas na
 * toestemming mag laden, zoals de Calendly-agenda (#10), gebruiken het:
 *
 * - leesKeuze() geeft { statistieken, marketing } of null zolang er geen
 *   keuze is;
 * - bewaarKeuze(keuze) bewaart de keuze en geeft het event cookiekeuze
 *   op document, met de keuze als detail;
 * - heeftToestemming(categorie) en geefToestemming(categorie) voor één
 *   categorie. Geven laat de andere categorieën zoals ze waren.
 *
 * De keuze staat in localStorage met een versienummer. Een keuze met een
 * ander nummer telt als geen keuze, zodat de banner na een wijziging
 * opnieuw vraagt en een oude keuze nooit stilletjes geldt. Werkt
 * localStorage niet, dan geldt de keuze alleen voor deze pagina.
 */

const SLEUTEL = 'vd-cookies';
const VERSIE = 1;
const CATEGORIEEN = ['statistieken', 'marketing'];

let keuzeVanDezePagina = null;

function zonderToestemming() {
    return Object.fromEntries(CATEGORIEEN.map((categorie) => [categorie, false]));
}

function alleenCategorieen(keuze) {
    return Object.fromEntries(CATEGORIEEN.map((categorie) => [categorie, keuze[categorie] === true]));
}

export function leesKeuze() {
    if (keuzeVanDezePagina) {
        return { ...keuzeVanDezePagina };
    }

    try {
        const bewaard = JSON.parse(localStorage.getItem(SLEUTEL));

        if (bewaard && bewaard.versie === VERSIE) {
            return alleenCategorieen(bewaard);
        }
    } catch {
        // Geen toegang tot localStorage of geen geldige keuze: dan is er geen keuze.
    }

    return null;
}

export function bewaarKeuze(keuze) {
    keuzeVanDezePagina = alleenCategorieen(keuze);

    try {
        localStorage.setItem(SLEUTEL, JSON.stringify({ versie: VERSIE, ...keuzeVanDezePagina }));
    } catch {
        // Niet te bewaren: de keuze geldt dan alleen voor deze pagina.
    }

    document.dispatchEvent(new CustomEvent('cookiekeuze', { detail: { ...keuzeVanDezePagina } }));
}

export function heeftToestemming(categorie) {
    return leesKeuze()?.[categorie] === true;
}

export function geefToestemming(categorie) {
    bewaarKeuze({ ...(leesKeuze() ?? zonderToestemming()), [categorie]: true });
}
