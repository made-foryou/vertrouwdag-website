/*
 * A page the browser prerenders ahead of a click (#38, components/speculation-rules.blade.php) may never be opened.
 * Anything that counts a visit or loads a third party, such as Google Analytics and Calendly, waits until the visitor
 * really opens it.
 */
export function whenActivated(callback) {
    if (document.prerendering) {
        document.addEventListener('prerenderingchange', callback, { once: true });

        return;
    }

    callback();
}
