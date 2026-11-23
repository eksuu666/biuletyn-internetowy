const reducedMotion = window.matchMedia('(prefers-reduced-motion: reduce)');

function animateFeedback(element, className) {
    if (reducedMotion.matches) return;
    element.classList.remove(className);
    requestAnimationFrame(() => {
        element.classList.add(className);
        element.addEventListener('animationend', () => element.classList.remove(className), {once: true});
    });
}

function enhanceForms(container, status, onSuccess) {
    container.addEventListener('submit', async (event) => {
        const form = event.target;
        if (form.matches('[data-comment-reactions]')) return;
        event.preventDefault();
        if (container.getAttribute('aria-busy') === 'true') return;

        const button = event.submitter;
        const body = new FormData(form);
        if (button?.name) body.append(button.name, button.value);
        container.setAttribute('aria-busy', 'true');
        button?.setAttribute('aria-disabled', 'true');
        status.textContent = '';
        form.querySelector('.discussion-error')?.remove();

        try {
            const response = await fetch(form.action, {
                method: 'POST',
                body,
                headers: {Accept: 'application/json'},
            });
            if (response.status === 422) {
                const {errors} = await response.json();
                const alert = document.createElement('ul');
                alert.className = 'discussion-error';
                alert.setAttribute('role', 'alert');
                for (const message of Object.values(errors).flat()) {
                    const item = document.createElement('li');
                    item.textContent = message;
                    alert.append(item);
                }
                form.prepend(alert);
                return;
            }
            if (!response.ok || !response.headers.get('content-type')?.includes('application/json')) {
                status.textContent = {
                    401: 'Zaloguj się ponownie.',
                    419: 'Sesja wygasła. Odśwież stronę.',
                    429: 'Spróbuj ponownie za chwilę.',
                }[response.status] ?? 'Nie udało się zapisać.';
                return;
            }
            onSuccess(await response.json(), form, button);
        } catch {
            status.textContent = 'Nie udało się połączyć z serwerem.';
        } finally {
            container.removeAttribute('aria-busy');
            button?.removeAttribute('aria-disabled');
        }
    });
}

const reactionForm = document.querySelector('.portal-reactions');
if (reactionForm) {
    const status = document.querySelector('[data-reaction-status]');
    enhanceForms(reactionForm, status, ({counts, selected}, form, button) => {
        for (const option of form.querySelectorAll('[data-reaction]')) {
            const key = option.dataset.reaction;
            const active = selected === key;
            const count = counts[key] ?? 0;
            option.setAttribute('aria-pressed', String(active));
            option.setAttribute('aria-label', `${option.title} (${count})`);
            option.value = active ? '' : key;
            option.querySelector('[data-reaction-count]').textContent = count;
        }
        if (button) animateFeedback(button, 'reaction-pop');
    });
}

const discussion = document.querySelector('#comments');
if (discussion) {
    discussion.addEventListener('submit', async (event) => {
        const form = event.target;
        if (!form.matches('[data-comment-reactions]')) return;
        event.preventDefault();
        if (form.getAttribute('aria-busy') === 'true') return;
        const status = form.querySelector('[data-comment-reaction-status]');
        const button = event.submitter;
        if (!button) return;
        const body = new FormData(form);
        body.append(button.name, button.value);
        form.setAttribute('aria-busy', 'true');
        status.textContent = '';
        try {
            const response = await fetch(form.action, {method: 'POST', body, headers: {Accept: 'application/json'}, signal: AbortSignal.timeout(15000)});
            if (!response.ok) {
                status.textContent = response.status === 429 ? 'Spróbuj ponownie za chwilę.' : response.status === 419 ? 'Sesja wygasła. Odśwież stronę.' : 'Nie udało się zapisać reakcji.';
                return;
            }
            const {counts, selected} = await response.json();
            for (const option of form.querySelectorAll('[data-comment-reaction]')) {
                const key = option.dataset.commentReaction;
                const active = selected === key;
                option.setAttribute('aria-pressed', String(active));
                option.setAttribute('aria-label', `${option.title} (${counts[key] ?? 0})`);
                option.value = active ? '' : key;
                option.querySelector('[data-comment-reaction-count]').textContent = counts[key] ?? 0;
            }
            animateFeedback(button, 'reaction-pop');
        } catch {
            status.textContent = 'Nie udało się połączyć z serwerem.';
        } finally {
            form.removeAttribute('aria-busy');
        }
    });
    const status = discussion.querySelector('[data-discussion-status]');
    const threads = discussion.querySelector('[data-comment-threads]');
    enhanceForms(discussion, status, ({html, count, commentId, message}, form) => {
        const isNew = form.closest('#discussion-new');
        if (isNew) form.reset();

        threads.innerHTML = html;
        for (const label of document.querySelectorAll('[data-comment-count]')) {
            label.textContent = count;
        }
        status.textContent = message;
        const comment = commentId && document.getElementById(`comment-${commentId}`);
        const target = comment || discussion.querySelector('#discussion-heading');
        target.focus({preventScroll: true});
        target.scrollIntoView({block: 'nearest'});
        if (comment) animateFeedback(comment, 'discussion-arrival');
    });
}
