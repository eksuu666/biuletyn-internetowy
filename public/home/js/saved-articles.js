(() => {
    const key = 'slodziaki:saved-articles';
    const limit = 50;
    const toggle = document.querySelector('[data-save-article]');
    const saved = document.querySelector('[data-saved-list]');

    function readIds() {
        const stored = window.localStorage.getItem(key);
        let parsed;
        try {
            parsed = JSON.parse(stored ?? '[]');
        } catch {
            return [];
        }
        return Array.isArray(parsed)
            ? [...new Set(parsed.filter(id => typeof id === 'string' && /^[1-9][0-9]{0,15}$/.test(id)))].slice(0, limit)
            : [];
    }

    function updateToggle() {
        const active = readIds().includes(toggle.dataset.saveArticle);
        toggle.setAttribute('aria-pressed', String(active));
        toggle.querySelector('i').className = active ? 'fa fa-bookmark' : 'fa fa-bookmark-o';
        toggle.querySelector('span').textContent = active ? 'Zapisano' : 'Zapisz na później';
    }

    if (toggle) {
        const feedback = document.createElement('span');
        feedback.className = 'portal-save-feedback';
        feedback.setAttribute('role', 'status');
        toggle.after(feedback);
        try {
            updateToggle();
            toggle.hidden = false;
        } catch {
            toggle.disabled = true;
            toggle.hidden = false;
            feedback.textContent = 'Przeglądarka blokuje zapis.';
        }
        toggle.addEventListener('click', () => {
            try {
                const id = toggle.dataset.saveArticle;
                const ids = readIds();
                if (!ids.includes(id) && ids.length >= limit) {
                    feedback.textContent = 'Lista jest pełna. Usuń z niej jeden artykuł.';
                    return;
                }
                const next = ids.includes(id) ? ids.filter(value => value !== id) : [id, ...ids];
                localStorage.setItem(key, JSON.stringify(next));
                feedback.textContent = next.includes(id) ? 'Dodano do zapisanych.' : 'Usunięto z zapisanych.';
                updateToggle();
            } catch {
                feedback.textContent = 'Nie udało się zapisać zmiany w przeglądarce.';
            }
        });
    }

    if (saved) {
        const list = saved.querySelector('ul');
        const status = saved.querySelector('[role="status"]');
        let version = 0;

        function addRow(article) {
            const row = document.createElement('li');
            const copy = document.createElement('div');
            const category = document.createElement('span');
            category.className = 'portal-kicker';
            category.textContent = article.category;
            const title = document.createElement('h2');
            const link = document.createElement('a');
            link.href = article.url;
            link.textContent = article.title;
            title.append(link);
            copy.append(category, title);
            const remove = document.createElement('button');
            remove.type = 'button';
            remove.className = 'portal-outline';
            remove.textContent = 'Usuń';
            remove.setAttribute('aria-label', 'Usuń z zapisanych: ' + article.title);
            remove.addEventListener('click', () => {
                try {
                    localStorage.setItem(key, JSON.stringify(readIds().filter(id => id !== article.id)));
                    refresh(true);
                } catch {
                    status.hidden = false;
                    status.textContent = 'Nie udało się usunąć artykułu.';
                }
            });
            row.append(copy, remove);
            list.append(row);
        }

        async function refresh(focus = false) {
            const request = ++version;
            try {
                const ids = readIds();
                let articles = [];
                if (ids.length) {
                    const url = new URL(saved.dataset.savedList);
                    ids.forEach(id => url.searchParams.append('ids[]', id));
                    const response = await fetch(url, {headers: {Accept: 'application/json'}});
                    if (!response.ok) throw new Error();
                    const byId = new Map((await response.json()).map(article => [article.id, article]));
                    articles = ids.map(id => byId.get(id)).filter(Boolean);
                }
                if (request !== version) return;
                list.replaceChildren();
                articles.forEach(addRow);
                list.hidden = !articles.length;
                status.hidden = Boolean(articles.length);
                status.textContent = 'Nie masz zapisanych artykułów.';
                if (focus) {
                    status.tabIndex = -1;
                    (list.querySelector('a') ?? status).focus();
                }
            } catch {
                if (request !== version) return;
                status.hidden = false;
                status.textContent = 'Nie udało się wczytać zapisanych artykułów.';
            }
        }

        refresh();
        window.addEventListener('pageshow', event => { if (event.persisted) refresh(); });
        window.addEventListener('storage', event => { if (event.key === key) refresh(); });
    }

    if (toggle) {
        const syncToggle = () => {
            if (!toggle.disabled) updateToggle();
        };
        window.addEventListener('pageshow', syncToggle);
        window.addEventListener('storage', event => { if (event.key === key) syncToggle(); });
    }
})();
