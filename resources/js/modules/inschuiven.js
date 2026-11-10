/*
 * Inschuiven bij scrollen.
 *
 * Een element met data-reveal schuift in zodra het in beeld komt. Bij
 * data-reveal-group komen de kinderen na elkaar binnen, bij
 * data-reveal-inner tegelijk. De stylesheet verbergt ze alleen als de
 * html de klasse js heeft, dus zonder JavaScript is alles zichtbaar.
 *
 * Met prefers-reduced-motion: reduce (of zonder IntersectionObserver)
 * staat alles meteen op zijn plek.
 */
export default function inschuiven() {
    const elementen = document.querySelectorAll('[data-reveal],[data-reveal-group],[data-reveal-inner]');

    if (!elementen.length) {
        return;
    }

    document.querySelectorAll('[data-reveal-group]').forEach((groep) => {
        [...groep.children].forEach((kind, index) => kind.style.setProperty('--d', `${index * 0.12}s`));
    });

    const minderBeweging = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    if (minderBeweging || !('IntersectionObserver' in window)) {
        elementen.forEach((element) => element.classList.add('is-in'));

        return;
    }

    const observer = new IntersectionObserver((entries) => {
        entries.forEach((entry) => {
            if (entry.isIntersecting) {
                entry.target.classList.add('is-in');
                observer.unobserve(entry.target);
            }
        });
    }, { rootMargin: '0px 0px -8% 0px', threshold: 0.12 });

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
