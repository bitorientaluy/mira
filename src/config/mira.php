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

    // Centro inicial del mapa (Plaza Independencia) y zoom
    'mapa' => [
        'lat' => -34.9067,
        'lng' => -56.1996,
        'zoom' => 14,
    ],

];
