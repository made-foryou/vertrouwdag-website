/*
 * Entrypoint van de site.
 *
 * Elk gedrag uit het ontwerp is een eigen module in ./modules/{gedrag}.js
 * met een default export die hier wordt aangeroepen. Een module doet niets
 * als zijn elementen niet op de pagina staan. naarBoven gaat eerst, zodat
 * de pagina zo vroeg mogelijk bovenaan staat. fotosLaden gaat direct
 * daarna, want tot hij draait staan de foto's onzichtbaar.
 */

import naarBoven from './modules/naar-boven';
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

naarBoven();
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
