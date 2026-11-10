/*
 * Entrypoint van de site.
 *
 * Elk gedrag uit het ontwerp is een eigen module in ./modules/{gedrag}.js
 * met een default export die hier wordt aangeroepen. Een module doet niets
 * als zijn elementen niet op de pagina staan. naarBoven gaat eerst, zodat
 * de pagina zo vroeg mogelijk bovenaan staat.
 */

import naarBoven from './modules/naar-boven';
import mobielMenu from './modules/mobiel-menu';
import inschuiven from './modules/inschuiven';
import swipeStapel from './modules/swipe-stapel';
import lopendeBand from './modules/lopende-band';
import checklistFormulier from './modules/checklist-formulier';

naarBoven();
mobielMenu();
inschuiven();
swipeStapel();
lopendeBand();
checklistFormulier();
