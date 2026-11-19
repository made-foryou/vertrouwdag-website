/*
 * Checklist-formulier: aanmelden zonder de pagina te verlaten (#25).
 *
 * De browser controleert eerst de verplichte velden en het e-mailadres;
 * pas als die kloppen komt het submit-event. Dan post deze module het
 * formulier op de achtergrond en vraagt om JSON.
 *
 * Gelukt: staat er een bedankblok (#bedankt) op de pagina, zoals op
 * /checklist, dan verschijnt dat en wordt het formulier leeggemaakt. De
 * focus gaat naar de kop van het bedankblok, zodat ook een schermlezer de
 * bevestiging hoort. Anders toont het formulier de melding uit zijn
 * data-bedankt in .form-msg.
 *
 * Niet gelukt: de eerste fout van de server, of de melding voor een
 * verlopen pagina (419), te veel aanvragen (429) of een storing, komt in
 * .form-msg. Een afgekeurd veld krijgt aria-invalid.
 *
 * Zonder JavaScript post het formulier gewoon en komt de bezoeker terug
 * op het formulier, met dezelfde meldingen.
 */
export default function checklistFormulier() {
    const forms = document.querySelectorAll('form[data-bedankt]');

    if (!forms.length) {
        return;
    }

    const prefersReducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    forms.forEach((form) => {
        form.addEventListener('submit', async (event) => {
            event.preventDefault();

            const button = form.querySelector('button[type="submit"]');
            button.disabled = true;
            clearInvalidFields(form);

            try {
                const response = await fetch(form.action, {
                    method: 'POST',
                    body: new FormData(form),
                    headers: { Accept: 'application/json' },
                });

                if (response.ok) {
                    showThanks(form, prefersReducedMotion);

                    return;
                }

                const body = await response.json().catch(() => ({}));

                markInvalidFields(form, body.errors ?? {});
                showMessage(form, messageFor(form, response.status, body));
            } catch {
                showMessage(form, form.dataset.messageFailed);
            } finally {
                button.disabled = false;
            }
        });
    });
}

function showThanks(form, prefersReducedMotion) {
    const thanks = document.getElementById('bedankt');

    if (thanks) {
        thanks.hidden = false;
        thanks.scrollIntoView({ behavior: prefersReducedMotion ? 'auto' : 'smooth', block: 'center' });
        thanks.querySelector('[data-cms-field="kop"]')?.focus({ preventScroll: true });
        form.reset();
        form.querySelector('.form-msg').hidden = true;

        return;
    }

    showMessage(form, form.dataset.bedankt);
}

function messageFor(form, status, body) {
    if (status === 422) {
        const [firstErrors] = Object.values(body.errors ?? {});

        return firstErrors?.[0] ?? form.dataset.messageFailed;
    }

    if (status === 419) {
        return form.dataset.messageExpired;
    }

    if (status === 429) {
        return body.message ?? form.dataset.messageThrottled;
    }

    return form.dataset.messageFailed;
}

function showMessage(form, text) {
    const message = form.querySelector('.form-msg');

    message.textContent = text;
    message.hidden = false;
}

function markInvalidFields(form, errors) {
    Object.keys(errors).forEach((name) => {
        form.querySelector(`[name="${CSS.escape(name)}"]`)?.setAttribute('aria-invalid', 'true');
    });
}

function clearInvalidFields(form) {
    form.querySelectorAll('[aria-invalid]').forEach((field) => field.removeAttribute('aria-invalid'));
}
