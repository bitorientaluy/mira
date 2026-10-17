<?php

namespace Database\Seeders;

use App\Models\Area;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;

class UsuarioDemoSeeder extends Seeder
{
    /**
     * Un usuario por rol para la demo. Clave: config('mira.demo_password') (DEMO_PASSWORD del .env).
     */
    public function run(): void
    {
        $area = Area::where('nombre', Area::LIMPIEZA_URBANA)->firstOrFail();

        $usuarios = [
            ['nombre' => 'Ciudadano Demo', 'email' => 'ciudadano@mira.local', 'rol' => Rol::CIUDADANO, 'area' => false],
            ['nombre' => 'Operador Demo', 'email' => 'operador@mira.local', 'rol' => Rol::OPERADOR, 'area' => false],
            ['nombre' => 'Jefe de Área Demo', 'email' => 'jefe@mira.local', 'rol' => Rol::JEFE_AREA, 'area' => true],
            ['nombre' => 'Ejecutor Demo', 'email' => 'ejecutor@mira.local', 'rol' => Rol::EJECUTOR, 'area' => true],
            ['nombre' => 'Administrador Demo', 'email' => 'admin@mira.local', 'rol' => Rol::ADMIN, 'area' => false],
        ];

        foreach ($usuarios as $datos) {
            $usuario = User::firstOrNew(['email' => $datos['email']]);
            $usuario->fill(['nombre' => $datos['nombre'], 'password' => config('mira.demo_password')]);
            $usuario->rol_id = Rol::where('nombre', $datos['rol'])->value('id');
            $usuario->save();

            if ($datos['area']) {
                $usuario->areas()->syncWithoutDetaching([$area->id]);
            }
        }
    }
}
