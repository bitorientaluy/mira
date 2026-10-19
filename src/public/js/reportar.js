// Pantalla "Nuevo reporte" (T-111): marcar el punto en el mapa, usar la ubicación del dispositivo
// y previsualizar la foto. La validación real la hace el servidor (StoreIncidenciaRequest).
(function () {
    const contenedor = document.getElementById('mapa');
    if (!contenedor || typeof L === 'undefined') return;

    const d = contenedor.dataset;
    const limites = L.latLngBounds([+d.latMin, +d.lngMin], [+d.latMax, +d.lngMax]);
    const campoLat = document.getElementById('lat');
    const campoLng = document.getElementById('lng');
    const aviso = document.getElementById('aviso-mapa');
    const estado = document.getElementById('estado-ubicacion');
    const textoUbicacion = document.getElementById('texto-ubicacion');

    const mapa = L.map(contenedor, {
        center: [+d.lat, +d.lng],
        zoom: +d.zoom,
        minZoom: 11,
        maxBounds: limites.pad(0.1),
        zoomControl: false,
    });
    L.control.zoom({ position: 'topright' }).addTo(mapa);
    L.tileLayer('https://tile.openstreetmap.org/{z}/{x}/{y}.png', {
        maxZoom: 19,
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a>',
    }).addTo(mapa);

    const icono = L.divIcon({
        className: '',
        html: '<span class="pin-incidencia"><span class="icono">priority_high</span></span>',
        iconSize: [44, 52],
        iconAnchor: [22, 52],
    });
    let marcador = null;

    function avisar(texto, esError) {
        aviso.innerHTML = '<span class="icono" aria-hidden="true">' + (esError ? 'error' : 'check_circle') + '</span> ';
        aviso.appendChild(document.createTextNode(texto));
        aviso.classList.toggle('aviso-error', !!esError);
    }

    function marcar(latlng, centrar) {
        if (!limites.contains(latlng)) {
            avisar('Ese punto está fuera de Montevideo', true);
            return;
        }
        if (marcador) {
            marcador.setLatLng(latlng);
        } else {
            marcador = L.marker(latlng, { icon: icono, draggable: true, keyboard: false }).addTo(mapa);
            marcador.on('dragend', () => marcar(marcador.getLatLng(), false));
        }
        campoLat.value = latlng.lat.toFixed(7);
        campoLng.value = latlng.lng.toFixed(7);
        estado.classList.add('ubicacion-lista');
        estado.classList.remove('entrada-invalida');
        textoUbicacion.textContent = 'Punto marcado en el mapa';
        avisar('Punto marcado. Podés arrastrarlo para ajustarlo', false);
        if (centrar) mapa.setView(latlng, Math.max(mapa.getZoom(), 17));
    }

    mapa.on('click', (e) => marcar(e.latlng, false));

    // Si el formulario volvió con errores, se conserva el punto marcado (CP-02)
    if (campoLat.value && campoLng.value) {
        marcar(L.latLng(+campoLat.value, +campoLng.value), true);
    }

    // Geolocalización: solo funciona en HTTPS o localhost; si no, se marca tocando el mapa
    document.getElementById('ubicarme').addEventListener('click', () => {
        if (!navigator.geolocation || !window.isSecureContext) {
            avisar('No se puede usar tu ubicación desde acá. Tocá el mapa', true);
            return;
        }
        avisar('Buscando tu ubicación…', false);
        navigator.geolocation.getCurrentPosition(
            (pos) => marcar(L.latLng(pos.coords.latitude, pos.coords.longitude), true),
            () => avisar('No se pudo obtener tu ubicación. Tocá el mapa', true),
            { enableHighAccuracy: true, timeout: 10000 }
        );
    });

    // Vista previa de la foto elegida
    const foto = document.getElementById('foto');
    const vista = document.getElementById('vista-foto');
    const textoFoto = document.getElementById('texto-foto');
    foto.addEventListener('change', () => {
        const archivo = foto.files[0];
        if (!archivo) return;
        textoFoto.textContent = 'Cambiar foto';
        vista.src = URL.createObjectURL(archivo);
        vista.hidden = false;
    });
})();
