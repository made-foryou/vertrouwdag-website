/*
 * Foto's laden rustig in (#39).
 *
 * Een foto met data-foto staat op een plaatshouder in de kleur van het
 * palet. De stylesheet houdt hem onzichtbaar tot hij hier de klasse
 * is-geladen krijgt. Een foto die pas na het laden van de pagina
 * binnenkomt, faadt in (is-infaden). Een foto die er al is, uit de cache
 * of omdat hij snel was, staat er meteen, zodat een volgende pagina niet
 * knippert.
 *
 * Een foto die niet laadt, wordt ook getoond: dan staat zijn alt-tekst
 * er, zoals zonder deze module. Zonder beweging (reduced motion of de
 * bewerkmodus van het CMS, #35) faadt niets, dus daar staat elke foto er
 * meteen.
 */
import { isMotionAllowed } from '../motion';

export default function fotosLaden() {
    const fotos = document.querySelectorAll('img[data-foto]');

    if (!fotos.length) {
        return;
    }

    fotos.forEach((foto) => {
        if (!isMotionAllowed() || foto.complete) {
            foto.classList.add('is-geladen');

            return;
        }

        foto.addEventListener('load', () => foto.classList.add('is-geladen', 'is-infaden'), { once: true });
        foto.addEventListener('error', () => foto.classList.add('is-geladen'), { once: true });
    });
}
