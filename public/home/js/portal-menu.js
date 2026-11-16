const portalMenu = document.querySelector('[data-portal-menu]');

if (portalMenu) {
    document.addEventListener('click', (event) => {
        if (portalMenu.open && !portalMenu.contains(event.target)) {
            portalMenu.open = false;
        }
    });

    document.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && portalMenu.open) {
            event.preventDefault();
            portalMenu.open = false;
            portalMenu.querySelector('summary').focus();
        }
    });
}
