/*
 * Whether the site may move (#35).
 *
 * The inline script in <head> (components/motion.blade.php) decides once for the whole page and puts the class motion
 * on <html>: not with prefers-reduced-motion: reduce, and not in the edit mode of the CMS. Scripts ask here instead of
 * reading the media query themselves, so they follow the same decision as the stylesheet.
 */
export function isMotionAllowed() {
    return document.documentElement.classList.contains('motion');
}
