<?php

// Configuración propia de MIRA. Se lee con config('mira.<clave>'), nunca con env() fuera de config/.
return [

    // Clave de los usuarios demo que crea UsuarioDemoSeeder (solo local y testing)
    'demo_password' => env('DEMO_PASSWORD', 'password'),

    // Recuadro que contiene el departamento de Montevideo: solo se aceptan reportes dentro (T-111)
    'limites' => [
        'lat_min' => -34.95,
        'lat_max' => -34.69,
        'lng_min' => -56.44,
        'lng_max' => -56.01,
    ],

    // Redes desde las que se puede abrir el panel interno (T-202), separadas por coma.
    // Local con Docker Desktop: 127.0.0.1/32,172.16.0.0/12. SRV-POSIX: 192.168.30.0/24 (VLAN 30).
    // Sin la variable, solo la propia máquina: es mejor que el panel falle cerrado.
    'redes_panel' => array_values(array_filter(array_map('trim', explode(',', (string) env('MIRA_REDES_PANEL', '127.0.0.1/32'))))),

    // Centro inicial del mapa (Plaza Independencia) y zoom
    'mapa' => [
        'lat' => -34.9067,
        'lng' => -56.1996,
        'zoom' => 14,
    ],

];
