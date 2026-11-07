// Mapa chico del detalle de una incidencia en el panel (T-116): solo muestra dónde está.
(function () {
    'use strict';

    const contenedor = document.getElementById('mapa-detalle');
    if (!contenedor || typeof L === 'undefined') {
        return;
    }

    const punto = [parseFloat(contenedor.dataset.lat), parseFloat(contenedor.dataset.lng)];
    const mapa = L.map(contenedor, { scrollWheelZoom: false }).setView(punto, 16);

    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(mapa);

    L.marker(punto, {
        icon: L.divIcon({
            className: '',
            html: '<span class="pin-incidencia"><span class="icono">priority_high</span></span>',
            iconSize: [44, 52],
            iconAnchor: [22, 52],
        }),
        keyboard: false,
    }).addTo(mapa);
})();
