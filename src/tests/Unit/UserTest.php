<?php

namespace Tests\Unit;

use App\Models\Rol;
use App\Models\User;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class UserTest extends TestCase
{
    public static function rutasPorRol(): array
    {
        return [
            'ciudadano' => [Rol::CIUDADANO, 'ciudadano.inicio'],
            'operador' => [Rol::OPERADOR, 'panel.inicio'],
            'jefe de área' => [Rol::JEFE_AREA, 'panel.inicio'],
            'ejecutor' => [Rol::EJECUTOR, 'panel.inicio'],
            'administrador' => [Rol::ADMIN, 'panel.inicio'],
        ];
    }

    private function usuarioConRol(string $rol): User
    {
        $usuario = new User;
        $usuario->setRelation('rol', new Rol(['nombre' => $rol]));

        return $usuario;
    }

    #[DataProvider('rutasPorRol')]
    public function test_ruta_de_inicio_segun_el_rol(string $rol, string $ruta): void
    {
        $this->assertSame($ruta, $this->usuarioConRol($rol)->rutaInicio());
    }

    public function test_es_rol(): void
    {
        $usuario = $this->usuarioConRol(Rol::OPERADOR);

        $this->assertTrue($usuario->esRol(Rol::OPERADOR));
        $this->assertFalse($usuario->esRol(Rol::ADMIN));
    }

    public function test_no_expone_datos_sensibles_al_serializar(): void
    {
        $usuario = new User(['nombre' => 'Vecina', 'email' => 'v@prueba.uy', 'cedula' => '12345678', 'password' => 'x']);

        $this->assertArrayNotHasKey('cedula', $usuario->toArray());
        $this->assertArrayNotHasKey('password', $usuario->toArray());
    }
}
