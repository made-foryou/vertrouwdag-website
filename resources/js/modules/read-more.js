/*
 * Read more: a long text that folds in on a phone (feedback from Romy, 2026-11-28).
 *
 * Every [data-read-more] is followed by its button (<x-read-more>). The stylesheet sets after how many lines a text
 * folds in (--read-more-lines); where it sets none, as on a wide screen, nothing folds in. A longer text gets
 * data-state="closed" and its button appears; the button opens and closes it (aria-expanded). A shorter text gets no
 * state and no button, so the button is only there where it does something. The check runs again whenever the text
 * changes: another width, the fonts arriving, an edit in the frame of the CMS.
 *
 * Closing keeps the start of the text in view, so a reader who scrolled down is not left further down the page.
 * Focus on a link in a closed text opens it, so the focus is never hidden.
 *
 * In a swipe deck (swipe-stapel.js) only the card at the front opens: a tap on the button of a card behind it is left
 * to the deck, which brings that card forward, and an open card closes when another card comes to the front.
 */
import { isMotionAllowed } from '../motion';

export default function readMore() {
    document.querySelectorAll('[data-read-more]').forEach(setUp);
}

function setUp(text, index) {
    const toggle = text.nextElementSibling;

    if (!toggle?.classList.contains('read-more__toggle')) {
        return;
    }

    text.id ||= `read-more-${index + 1}`;
    toggle.setAttribute('aria-controls', text.id);

    const card = text.closest('[data-deck] > *');

    const foldHeight = () => {
        const style = getComputedStyle(text);
        const lines = parseFloat(style.getPropertyValue('--read-more-lines'));
        const lineHeight = parseFloat(style.lineHeight);

        return lines > 0 && lineHeight > 0 ? lines * lineHeight : Infinity;
    };

    const setState = (state) => {
        if (state) {
            text.dataset.state = state;
        } else {
            delete text.dataset.state;
        }

        toggle.setAttribute('aria-expanded', String(state === 'open'));
        toggle.hidden = !state;
    };

    // scrollHeight is the height of the whole text, also while the stylesheet cuts it off.
    const check = () => {
        if (text.scrollHeight <= foldHeight() + 1) {
            setState(null);
        } else if (!text.dataset.state) {
            setState('closed');
        }
    };

    let checkPlanned = false;

    const planCheck = () => {
        if (!checkPlanned) {
            checkPlanned = true;
            requestAnimationFrame(() => {
                checkPlanned = false;
                check();
            });
        }
    };

    const keepStartInView = () => {
        const headerSpace = parseFloat(getComputedStyle(document.documentElement).scrollPaddingTop) || 0;
        const above = text.getBoundingClientRect().top - headerSpace;

        if (above < 0) {
            window.scrollBy({ top: above, behavior: isMotionAllowed() ? 'smooth' : 'instant' });
        }
    };

    toggle.addEventListener('click', (event) => {
        // A keyboard click (Enter, space) has detail 0: the focus already brought the card forward.
        if (card && !card.classList.contains('is-active') && event.detail !== 0) {
            return;
        }

        if (text.dataset.state === 'open') {
            setState('closed');
            keepStartInView();
        } else {
            setState('open');
        }
    });

    text.addEventListener('focusin', () => {
        if (text.dataset.state === 'closed') {
            setState('open');
        }
    });

    if (card) {
        new MutationObserver(() => {
            if (!card.classList.contains('is-active') && text.dataset.state === 'open') {
                setState('closed');
            }
        }).observe(card, { attributes: true, attributeFilter: ['class'] });
    }

    new ResizeObserver(planCheck).observe(text);
    new MutationObserver(planCheck).observe(text, { childList: true, characterData: true, subtree: true });
    document.fonts?.ready.then(planCheck);
    check();
}
