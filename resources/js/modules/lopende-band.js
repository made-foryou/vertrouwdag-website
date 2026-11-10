/*
 * De lopende band: altijd vol en naadloos doorlopend.
 *
 * De teksten in .marquee__track gaan in één groep. Die groep wordt zo
 * vaak gekloond dat de band ook op een breed scherm vol is, en het spoor
 * schuift met een vaste snelheid naar links. Na één groepsbreedte springt
 * het terug, wat niet te zien is omdat de kloon er dan precies staat.
 * Het loopt via een script (niet via CSS), zodat het overal gelijk loopt.
 *
 * Met prefers-reduced-motion: reduce staat de band stil, wel vol.
 */
const SNELHEID = 45; // pixels per seconde

export default function lopendeBand() {
    const sporen = document.querySelectorAll('.marquee__track');

    if (!sporen.length) {
        return;
    }

    const banden = [...sporen].map((spoor) => {
        const groep = document.createElement('div');
        groep.className = 'marquee__group';
        [...spoor.children].forEach((kind) => groep.appendChild(kind));
        spoor.appendChild(groep);
        spoor.classList.add('is-ready');

        const band = { spoor, groep, breedte: 0, zichtbareBreedte: 0, x: 0 };

        band.vullen = () => {
            spoor.querySelectorAll('.marquee__group.is-clone').forEach((kloon) => kloon.remove());
            band.breedte = groep.getBoundingClientRect().width;
            band.zichtbareBreedte = spoor.parentElement.clientWidth;

            if (!band.breedte) {
                return;
            }

            const nodig = Math.ceil(band.zichtbareBreedte / band.breedte) + 1;

            for (let i = 0; i < nodig; i++) {
                const kloon = groep.cloneNode(true);
                kloon.classList.add('is-clone');
                spoor.appendChild(kloon);
            }
        };

        band.vullen();

        return band;
    });

    // De groep wordt breder zodra de lettertypes geladen zijn.
    document.fonts?.ready.then(() => banden.forEach((band) => band.vullen()));

    let wachtOpResize;

    window.addEventListener('resize', () => {
        clearTimeout(wachtOpResize);
        wachtOpResize = setTimeout(() => {
            banden.forEach((band) => {
                if (band.spoor.parentElement.clientWidth !== band.zichtbareBreedte) {
                    band.vullen();
                }
            });
        }, 200);
    });

    if (window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        return;
    }

    let vorige = null;

    const stap = (nu) => {
        if (vorige !== null) {
            // Na een pauze (ander tabblad) niet ineens verspringen.
            const verstreken = Math.min((nu - vorige) / 1000, 0.1);

            banden.forEach((band) => {
                if (!band.breedte) {
                    return;
                }

                band.x -= SNELHEID * verstreken;

                if (band.x <= -band.breedte) {
                    band.x += band.breedte;
                }

                band.spoor.style.transform = `translate3d(${band.x.toFixed(2)}px,0,0)`;
            });
        }

        vorige = nu;
        requestAnimationFrame(stap);
    };

    requestAnimationFrame(stap);
}
