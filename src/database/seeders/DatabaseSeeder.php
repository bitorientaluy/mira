<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            RolSeeder::class,
            AreaCategoriaSeeder::class,
        ]);

        // Usuarios demo solo en desarrollo y pruebas, nunca en producción
        if (app()->environment(['local', 'testing'])) {
            $this->call(UsuarioDemoSeeder::class);
        }
    }
}
