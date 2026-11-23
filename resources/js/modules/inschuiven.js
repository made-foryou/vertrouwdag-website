/*
 * Inschuiven bij scrollen.
 *
 * Een element met data-reveal schuift in zodra het in beeld komt. Bij
 * data-reveal-group komen de kinderen na elkaar binnen: elk kind krijgt
 * zijn plek in de groep als --i, de stylesheet rekent daar de vertraging
 * mee uit. De waarde van het attribuut kiest de variant (#37). De stylesheet
 * verbergt ze alleen als de html de klasse motion heeft (#35), dus zonder
 * JavaScript is alles zichtbaar.
 *
 * Zonder beweging (reduced motion of de bewerkmodus van het CMS) of zonder
 * IntersectionObserver staat alles meteen op zijn plek.
 */
import { isMotionAllowed } from '../motion';

export default function inschuiven() {
    const elementen = document.querySelectorAll('[data-reveal],[data-reveal-group]');

    if (!elementen.length) {
        return;
    }

    document.querySelectorAll('[data-reveal-group]').forEach((groep) => {
        [...groep.children].forEach((kind, index) => kind.style.setProperty('--i', index));
    });

    if (!isMotionAllowed() || !('IntersectionObserver' in window)) {
        elementen.forEach((element) => element.classList.add('is-in'));

        return;
    }

    // Threshold 0: a strip taller than the screen, such as a long text, still comes in.
    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-in');
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0 });

    elementen.forEach((element) => observer.observe(element));

    // Vangnet: kort na het laden alles tonen wat boven de vouw nog verborgen is.
    window.addEventListener('load', () => setTimeout(() => {
        elementen.forEach((element) => {
            if (element.getBoundingClientRect().top < window.innerHeight) {
                element.classList.add('is-in');
            }
        });
    }, 1200));
}
