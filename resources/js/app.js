/*
 * Entrypoint van de site.
 *
 * Elk gedrag uit het ontwerp is een eigen module in ./modules/{gedrag}.js
 * met een default export die hier wordt aangeroepen. Een module doet niets
 * als zijn elementen niet op de pagina staan.
 */

import mobielMenu from './modules/mobiel-menu';

mobielMenu();
