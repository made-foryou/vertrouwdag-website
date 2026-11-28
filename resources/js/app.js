/*
 * Entrypoint van de site.
 *
 * Elk gedrag uit het ontwerp is een eigen module in ./modules/{gedrag}.js
 * met een default export die hier wordt aangeroepen. Een module doet niets
 * als zijn elementen niet op de pagina staan. fotosLaden gaat eerst,
 * want tot hij draait staan de foto's onzichtbaar. Waar de pagina opent
 * na terug, vooruit of herladen, laten we aan de browser (#38).
 */

import fotosLaden from './modules/fotos-laden';
import mobielMenu from './modules/mobiel-menu';
import inschuiven from './modules/inschuiven';
import swipeStapel from './modules/swipe-stapel';
import lopendeBand from './modules/lopende-band';
import dienstmappen from './modules/dienstmappen';
import checklistFormulier from './modules/checklist-formulier';
import blogfilter from './modules/blogfilter';
import cookiebanner from './modules/cookiebanner';
import calendlyAgenda from './modules/calendly-agenda';
import blogPhotoTransition from './modules/blog-photo-transition';
import readMore from './modules/read-more';

fotosLaden();
mobielMenu();
inschuiven();
swipeStapel();
lopendeBand();
dienstmappen();
checklistFormulier();
blogfilter();
cookiebanner();
calendlyAgenda();
blogPhotoTransition();
readMore();
