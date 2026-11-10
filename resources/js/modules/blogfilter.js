/*
 * Blogfilter: chips die de blogs in het archief op onderwerp filteren.
 *
 * Een chip met data-filter is een knop met aria-pressed. Een klik zet
 * hem aan en de andere uit, en verbergt elke blog ([data-post]) met een
 * andere data-cat; "alles" toont ze allemaal. Blijft er geen blog over,
 * dan verschijnt de melding .posts__empty.
 *
 * Een adres met een onderwerp als anker (/blog/archief#planning, zoals
 * de chips op /blog linken) zet dat filter aan, bij het laden en als het
 * anker verandert.
 *
 * Zonder JavaScript staan alle blogs er en doen de chips niets.
 */
export default function blogfilter() {
    const chips = [...document.querySelectorAll('[data-filter]')];

    if (!chips.length) {
        return;
    }

    const blogs = document.querySelectorAll('[data-post]');
    const leeg = document.querySelector('.posts__empty');

    const filteren = (gekozen) => {
        const onderwerp = gekozen.dataset.filter;
        let zichtbaar = 0;

        chips.forEach((chip) => chip.setAttribute('aria-pressed', String(chip === gekozen)));

        blogs.forEach((blog) => {
            const tonen = onderwerp === 'alles' || blog.dataset.cat === onderwerp;

            blog.hidden = !tonen;

            if (tonen) {
                zichtbaar++;
            }
        });

        if (leeg) {
            leeg.hidden = zichtbaar > 0;
        }
    };

    chips.forEach((chip) => chip.addEventListener('click', () => filteren(chip)));

    const naarAnker = () => {
        const chip = chips.find((kandidaat) => `#${kandidaat.dataset.filter}` === location.hash);

        if (chip) {
            filteren(chip);
        }
    };

    naarAnker();
    window.addEventListener('hashchange', naarAnker);
}
