/*
 * Swipe-stapels op mobiel (briefjes en dienstkaarten).
 *
 * Een element met data-deck is tot 720px breed een rij kaarten die over
 * elkaar liggen; de stylesheet zet ze in een horizontale scroll-snap. De
 * kaart in het midden krijgt is-active, zijn buren is-near, en het
 * bijbehorende bolletje in het eerstvolgende .deck-dots-blok licht op.
 *
 * Een tik op een kaart erachter haalt hem eerst naar voren; een link
 * erin opent pas als hij vooraan ligt. Met het toetsenbord komt een kaart
 * naar voren zodra hij (of een link erin) de focus krijgt, en Enter
 * opent een link meteen. Met prefers-reduced-motion: reduce schuift de
 * stapel zonder animatie.
 */
export default function swipeStapel() {
    const stapels = document.querySelectorAll('[data-deck]');

    if (!stapels.length) {
        return;
    }

    const mobiel = window.matchMedia('(max-width: 720px)');
    const minderBeweging = window.matchMedia('(prefers-reduced-motion: reduce)');

    stapels.forEach((stapel) => {
        const kaarten = [...stapel.children];

        let bolletjesBlok = stapel.nextElementSibling;

        while (bolletjesBlok && !bolletjesBlok.classList.contains('deck-dots')) {
            bolletjesBlok = bolletjesBlok.nextElementSibling;
        }

        const bolletjes = bolletjesBlok ? [...bolletjesBlok.children] : [];

        let actief = 0;
        let gepland = false;

        const bijwerken = () => {
            gepland = false;

            if (!mobiel.matches) {
                kaarten.forEach((kaart) => kaart.classList.remove('is-active', 'is-near'));

                return;
            }

            const midden = stapel.getBoundingClientRect().left + stapel.clientWidth / 2;
            let kleinsteAfstand = Infinity;

            kaarten.forEach((kaart, index) => {
                const vlak = kaart.getBoundingClientRect();
                const afstand = Math.abs(vlak.left + vlak.width / 2 - midden);

                if (afstand < kleinsteAfstand) {
                    kleinsteAfstand = afstand;
                    actief = index;
                }
            });

            kaarten.forEach((kaart, index) => {
                kaart.classList.toggle('is-active', index === actief);
                kaart.classList.toggle('is-near', Math.abs(index - actief) === 1);
            });

            bolletjes.forEach((bolletje, index) => bolletje.classList.toggle('is-on', index === actief));
        };

        const planBijwerken = () => {
            if (!gepland) {
                gepland = true;
                requestAnimationFrame(bijwerken);
            }
        };

        const naarVoren = (kaart) => {
            kaart.scrollIntoView({
                behavior: minderBeweging.matches ? 'auto' : 'smooth',
                inline: 'center',
                block: 'nearest',
            });
        };

        stapel.addEventListener('scroll', planBijwerken, { passive: true });
        window.addEventListener('resize', planBijwerken);

        kaarten.forEach((kaart, index) => {
            kaart.addEventListener('click', (event) => {
                // Een klik via het toetsenbord (Enter) heeft detail 0 en gaat gewoon door.
                if (!mobiel.matches || index === actief || event.detail === 0) {
                    return;
                }

                event.preventDefault();
                naarVoren(kaart);
            });

            kaart.addEventListener('focusin', () => {
                if (mobiel.matches && index !== actief) {
                    naarVoren(kaart);
                }
            });
        });

        bijwerken();
    });
}
