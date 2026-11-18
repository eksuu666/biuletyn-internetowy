(() => {
    const grid = document.querySelector('[data-home-feed]');
    const sentinel = document.querySelector('[data-feed-sentinel]');
    if (!grid || !sentinel) return;
    const rail = document.querySelector('.home-rail-follow');
    if (rail) {
        const positionRail = () => rail.style.setProperty('--rail-top', `${Math.min(24, innerHeight - rail.offsetHeight - 24)}px`);
        positionRail();
        window.addEventListener('resize', positionRail, {passive: true});
        if ('ResizeObserver' in window) new ResizeObserver(positionRail).observe(rail);
    }
    const status = sentinel.querySelector('[data-feed-status]');
    const retry = sentinel.querySelector('[data-feed-retry]');
    const seen = new Set([...grid.querySelectorAll('[data-stream-id]')].map(el => el.dataset.streamId));
    let next = sentinel.dataset.nextUrl;
    let loading = false;
    let failed = false;
    let observer;
    const finish = () => {observer?.disconnect(); status.textContent = ''; sentinel.hidden = true;};
    async function load() {
        if (loading || failed || !next) return;
        loading = true;
        grid.setAttribute('aria-busy', 'true');
        status.textContent = 'Wczytywanie artykułów…';
        retry.hidden = true;
        try {
            const response = await fetch(next, {headers: {'Accept': 'application/json'}, credentials: 'same-origin', signal: AbortSignal.timeout(20000)});
            if (!response.ok) throw new Error('feed');
            const data = await response.json();
            if (typeof data.html !== 'string' || !(data.nextUrl === null || typeof data.nextUrl === 'string')) throw new Error('feed');
            const template = document.createElement('template');
            template.innerHTML = data.html;
            for (const card of template.content.querySelectorAll('[data-stream-id]')) {
                if (seen.has(card.dataset.streamId)) card.remove();
                else seen.add(card.dataset.streamId);
            }
            grid.append(template.content);
            next = data.nextUrl;
            status.textContent = '';
            if (!next) finish();
        } catch (_) {
            failed = true;
            status.textContent = 'Nie udało się wczytać kolejnych artykułów.';
            retry.hidden = false;
        } finally {
            loading = false;
            grid.removeAttribute('aria-busy');
            if (next && !failed && sentinel.getBoundingClientRect().top < innerHeight + 550) requestAnimationFrame(load);
        }
    }
    retry.addEventListener('click', () => {failed = false; load();});
    if (!next) return finish();
    if ('IntersectionObserver' in window) {
        observer = new IntersectionObserver(entries => {if (entries.some(entry => entry.isIntersecting)) load();}, {rootMargin: '0px 0px 550px 0px'});
        observer.observe(sentinel);
    } else window.addEventListener('scroll', () => {if (sentinel.getBoundingClientRect().top < innerHeight + 550) load();}, {passive: true});
})();
