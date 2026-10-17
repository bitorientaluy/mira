<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CP-15 (registro de ciudadano) y PU-15 (almacenamiento de contraseñas).
 */
class RegistroTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RolSeeder::class;

    private function datosValidos(array $cambios = []): array
    {
        return array_merge([
            'nombre' => 'Vecina de Prueba',
            'email' => 'vecina@prueba.uy',
            'cedula' => '12345678',
            'password' => 'clave1234',
            'password_confirmation' => 'clave1234',
        ], $cambios);
    }

    public function test_cp15_crea_un_ciudadano_con_contrasena_bcrypt_y_cedula(): void
    {
        $this->post('/registro', $this->datosValidos())
            ->assertRedirect(route('ciudadano.inicio'));

        $usuario = User::where('email', 'vecina@prueba.uy')->firstOrFail();
        $this->assertSame(Rol::CIUDADANO, $usuario->rol->nombre);
        $this->assertSame('12345678', $usuario->cedula);
        $this->assertStringStartsWith('$2y$', $usuario->password); // PU-15: BCrypt, nunca texto plano
        $this->assertAuthenticatedAs($usuario);
    }

    public function test_ignora_un_rol_enviado_desde_el_formulario(): void
    {
        $idAdmin = Rol::where('nombre', Rol::ADMIN)->value('id');

        $this->post('/registro', $this->datosValidos(['rol_id' => $idAdmin]));

        $usuario = User::where('email', 'vecina@prueba.uy')->firstOrFail();
        $this->assertSame(Rol::CIUDADANO, $usuario->rol->nombre);
    }

    public function test_guarda_el_email_en_minusculas_y_sin_espacios(): void
    {
        $this->post('/registro', $this->datosValidos(['email' => '  Vecina@Prueba.UY ']));

        $this->assertDatabaseHas('usuario', ['email' => 'vecina@prueba.uy']);
    }

    public function test_rechaza_un_email_repetido(): void
    {
        User::factory()->create(['email' => 'vecina@prueba.uy']);

        $this->post('/registro', $this->datosValidos())
            ->assertSessionHasErrors(['email' => 'Ya existe una cuenta con esa dirección de email.']);

        $this->assertDatabaseCount('usuario', 1);
        $this->assertGuest();
    }

    public function test_rechaza_una_cedula_repetida(): void
    {
        User::factory()->create(['cedula' => '12345678']);

        $this->post('/registro', $this->datosValidos())
            ->assertSessionHasErrors(['cedula' => 'Ya existe una cuenta con esa cédula.']);

        $this->assertDatabaseCount('usuario', 1);
        $this->assertGuest();
    }

    public function test_valida_los_datos_con_mensajes_en_espanol(): void
    {
        $this->post('/registro', [
            'cedula' => '123456789',
            'password' => '123',
            'password_confirmation' => '456',
        ])->assertSessionHasErrors([
            'nombre' => 'El campo nombre es obligatorio.',
            'email' => 'El campo dirección de email es obligatorio.',
            'cedula' => 'El campo cédula no puede tener más de 8 caracteres.',
        ]);

        $this->assertDatabaseCount('usuario', 0);
    }

    public function test_la_contrasena_debe_tener_al_menos_8_caracteres_y_coincidir(): void
    {
        $this->post('/registro', $this->datosValidos(['password' => 'corta', 'password_confirmation' => 'otra']))
            ->assertSessionHasErrors('password');

        $this->assertDatabaseCount('usuario', 0);
    }
}
