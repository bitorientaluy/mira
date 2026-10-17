<?php

// Configuración propia de MIRA. Se lee con config('mira.<clave>'), nunca con env() fuera de config/.
return [

    // Clave de los usuarios demo que crea UsuarioDemoSeeder (solo local y testing)
    'demo_password' => env('DEMO_PASSWORD', 'password'),

];
