(() => {
    const input = document.querySelector('[data-avatar-input]');
    const preview = document.querySelector('[data-avatar-preview]');
    if (!input || !preview) return;
    const original = preview.src;
    const error = document.querySelector('[data-avatar-error]');
    let url;
    input.addEventListener('change', () => {
        if (url) URL.revokeObjectURL(url);
        if (error) error.textContent = '';
        const file = input.files?.[0];
        preview.src = original;
        if (!file) return;
        if (!file.type.startsWith('image/') || file.size > 5000 * 1024) {
            if (error) error.textContent = 'Wybierz zdjęcie do 5 MB.';
            input.value = '';
            return;
        }
        url = URL.createObjectURL(file);
        preview.src = url;
    });
    window.addEventListener('pagehide', () => { if (url) URL.revokeObjectURL(url); });
})();
