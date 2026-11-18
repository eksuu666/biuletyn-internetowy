(() => {
    const container = document.querySelector('#weather-map');
    if (!container) return;
    const status = document.querySelector('.portal-map-status');
    if (!window.L) {
        status.hidden = false;
        container.hidden = true;
        return;
    }

    const cities = JSON.parse(document.querySelector('#weather-map-data').textContent);
    const map = L.map(container, {scrollWheelZoom: false, minZoom: 5, maxZoom: 12})
        .setView([52.0, 19.2], 6);
    const tiles = L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19,
    }).addTo(map);
    tiles.on('tileerror', () => { status.hidden = false; });
    tiles.on('tileload', () => { status.hidden = true; });

    for (const city of cities) {
        const label = document.createElement('span');
        label.className = 'weather-map-label';
        label.textContent = city.temperature;
        const marker = L.marker(city.coordinates, {
            icon: L.divIcon({
                className: 'weather-map-marker' + (city.selected ? ' is-selected' : ''),
                html: label.outerHTML, iconSize: [58, 28], iconAnchor: [29, 14],
            }),
            title: city.name + ': ' + city.temperature,
            alt: city.name + ': ' + city.temperature,
            keyboard: true,
        }).addTo(map);
        const content = document.createElement('div');
        const heading = document.createElement('strong');
        heading.textContent = city.name + ' · ' + city.temperature;
        const link = document.createElement('a');
        link.href = city.url;
        link.textContent = 'Zobacz pomiary';
        content.append(heading, document.createElement('br'), link);
        marker.bindPopup(content);
    }
})();
