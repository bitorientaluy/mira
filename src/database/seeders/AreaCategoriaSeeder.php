<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Categoria;
use Illuminate\Database\Seeder;

class AreaCategoriaSeeder extends Seeder
{
    public function run(): void
    {
        // Prototipo: una única área atiende todas las categorías. El modelo admite varias.
        $area = Area::firstOrCreate(['nombre' => Area::LIMPIEZA_URBANA]);

        $categorias = [
            'Contenedor desbordado',
            'Residuos fuera del contenedor',
            'Acumulación de basura',
            'Contenedor fuera de lugar',
            'Ramas y malezas',
        ];

        foreach ($categorias as $nombre) {
            Categoria::firstOrCreate(['nombre' => $nombre], ['area_id' => $area->id]);
        }
    }
}
