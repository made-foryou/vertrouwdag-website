/*
 * Toestemming: de cookiekeuze van de bezoeker lezen en bewaren.
 *
 * Dit is het contract uit #11. De cookiebanner en alles wat pas na
 * toestemming mag laden, zoals de Calendly-agenda (#10), gebruiken het:
 *
 * - leesKeuze() geeft per categorie true of false, bijvoorbeeld
 *   { statistieken: true, marketing: false }, of null zolang er geen
 *   keuze is;
 * - bewaarKeuze(keuze) bewaart de keuze en geeft het event cookiekeuze
 *   op document, met de keuze als detail. Dat gebeurt bij elke wijziging;
 * - heeftToestemming(categorie) en geefToestemming(categorie) voor één
 *   categorie. Geven laat de andere categorieën zoals ze waren.
 *
 * In de HTML horen er twee afspraken bij:
 *
 * - een element met data-cookie-open opent de cookie-instellingen;
 * - een script met type="text/plain" en data-toestemming="{categorie}"
 *   draait pas na toestemming voor die categorie. Een extern script zet
 *   zijn adres in data-src. Een embed, zoals de Calendly-agenda, geeft
 *   zijn categorie ook aan met data-toestemming, en laadt zichzelf met
 *   dit contract.
 *
 * De categorieën en het versienummer komen uit de banner (#cookie), die
 * ze uit config/vertrouwdag.php haalt. De keuze staat in localStorage
 * met dat nummer. Een keuze met een ander nummer telt als geen keuze,
 * zodat de banner na een wijziging opnieuw vraagt en een oude keuze
 * nooit stilletjes geldt. Werkt localStorage niet, dan geldt de keuze
 * alleen voor deze pagina.
 */

const SLEUTEL = 'vd-cookies';

let instellingen = null;
let keuzeVanDezePagina = null;

function leesInstellingen() {
    if (!instellingen) {
        const banner = document.getElementById('cookie');
        let categorieen = [];

        try {
            categorieen = JSON.parse(banner?.dataset.categorieen ?? '[]');
        } catch {
            // Geen geldige lijst: dan is er voor niets toestemming te geven.
        }

        instellingen = {
            versie: Number.parseInt(banner?.dataset.versie ?? '', 10),
            categorieen: Array.isArray(categorieen) ? categorieen : [],
        };
    }

    return instellingen;
}

function zonderToestemming() {
    return Object.fromEntries(leesInstellingen().categorieen.map((categorie) => [categorie, false]));
}

function alleenCategorieen(keuze) {
    return Object.fromEntries(leesInstellingen().categorieen.map((categorie) => [categorie, keuze[categorie] === true]));
}

export function leesKeuze() {
    if (keuzeVanDezePagina) {
        return { ...keuzeVanDezePagina };
    }

    const { versie } = leesInstellingen();

    if (Number.isNaN(versie)) {
        return null;
    }

    try {
        const bewaard = JSON.parse(localStorage.getItem(SLEUTEL));

        if (bewaard && bewaard.versie === versie) {
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
        localStorage.setItem(SLEUTEL, JSON.stringify({ versie: leesInstellingen().versie, ...keuzeVanDezePagina }));
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
