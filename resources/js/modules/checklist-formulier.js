/*
 * Checklist-formulier: na versturen een bedankmelding.
 *
 * Het formulier verstuurt nog niets, dat komt in #25. De browser
 * controleert eerst de verplichte velden en het e-mailadres; pas als
 * die kloppen komt het submit-event. Staat er een bedankblok (#bedankt)
 * op de pagina, zoals op /checklist, dan verschijnt dat en wordt het
 * formulier leeggemaakt. De focus gaat naar de kop van het bedankblok,
 * zodat ook een schermlezer de bevestiging hoort. Anders toont het
 * formulier de melding uit zijn data-bedankt in .form-msg.
 */
export default function checklistFormulier() {
    const formulieren = document.querySelectorAll('form[data-bedankt]');

    if (!formulieren.length) {
        return;
    }

    const minderBeweging = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    formulieren.forEach((formulier) => {
        formulier.addEventListener('submit', (event) => {
            event.preventDefault();

            const bedankt = document.getElementById('bedankt');

            if (bedankt) {
                bedankt.hidden = false;
                bedankt.scrollIntoView({ behavior: minderBeweging ? 'auto' : 'smooth', block: 'center' });
                bedankt.querySelector('h2')?.focus({ preventScroll: true });
                formulier.reset();

                return;
            }

            const melding = formulier.querySelector('.form-msg');

            melding.textContent = formulier.dataset.bedankt;
            melding.hidden = false;
        });
    });
}
