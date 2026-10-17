<?php

namespace Database\Seeders;

use App\Models\Rol;
use Illuminate\Database\Seeder;

class RolSeeder extends Seeder
{
    public function run(): void
    {
        foreach ([Rol::CIUDADANO, Rol::OPERADOR, Rol::JEFE_AREA, Rol::EJECUTOR, Rol::ADMIN] as $nombre) {
            Rol::firstOrCreate(['nombre' => $nombre]);
        }
    }
}
