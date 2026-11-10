/*
 * Calendly-agenda: pas laden na toestemming.
 *
 * De strook hero-kennismaken zet een lege #cal-embed op de pagina met de
 * Calendly-link in data-calendly-url en de categorie waar de agenda bij
 * hoort in data-toestemming (marketing). Zonder toestemming laadt er
 * niets van Calendly. De agenda laadt als:
 * - de bezoeker al toestemming voor die categorie gaf;
 * - de bezoeker op "Agenda tonen" (#cal-show) klikt, wat die toestemming
 *   geeft;
 * - de cookiebanner een keuze met die categorie bewaart.
 *
 * Vangnet: verschijnt de agenda niet binnen 6 seconden, of laadt het
 * script niet (adblocker, strenge browser, geen verbinding), dan toont de
 * pagina #cal-fallback met een knop naar Calendly in plaats van een leeg
 * vlak.
 */
import { geefToestemming, heeftToestemming } from './toestemming';

const WIDGET_SCRIPT = 'https://assets.calendly.com/assets/external/widget.js';
const WACHTTIJD = 6000;
const OPMAAK = {
    hide_gdpr_banner: '1',
    background_color: 'fbfbf7',
    text_color: '2d331f',
    primary_color: '401838',
};

export default function calendlyAgenda() {
    const embed = document.querySelector('[data-calendly-url]');

    if (!embed) {
        return;
    }

    const categorie = embed.dataset.toestemming;
    const poort = document.getElementById('cal-gate');
    const vangnet = document.getElementById('cal-fallback');
    let geladen = false;

    function focusStondIn(element) {
        return element.contains(document.activeElement);
    }

    function toonVangnet() {
        if (embed.hidden) {
            return;
        }

        const focusMee = focusStondIn(embed);

        embed.hidden = true;
        embed.replaceChildren();
        vangnet.hidden = false;

        if (focusMee) {
            vangnet.focus({ preventScroll: true });
        }
    }

    function laad({ focus = false } = {}) {
        if (geladen) {
            return;
        }

        geladen = true;

        const url = new URL(embed.dataset.calendlyUrl);

        Object.entries(OPMAAK).forEach(([naam, waarde]) => url.searchParams.set(naam, waarde));

        const widget = document.createElement('div');

        widget.className = 'calendly-inline-widget';
        widget.dataset.url = url.toString();
        embed.append(widget);

        const script = document.createElement('script');

        script.src = WIDGET_SCRIPT;
        script.async = true;
        script.addEventListener('error', toonVangnet);
        document.head.append(script);

        poort.hidden = true;
        embed.hidden = false;

        if (focus) {
            embed.focus({ preventScroll: true });
        }

        setTimeout(() => {
            if (!embed.querySelector('iframe')) {
                toonVangnet();
            }
        }, WACHTTIJD);
    }

    document.getElementById('cal-show')?.addEventListener('click', () => {
        // Eerst laden, zodat de focus van de verdwenen knop naar de agenda gaat.
        laad({ focus: true });
        geefToestemming(categorie);
    });

    document.addEventListener('cookiekeuze', (event) => {
        if (event.detail[categorie]) {
            laad();
        }
    });

    if (heeftToestemming(categorie)) {
        laad();
    }
}
