<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * CP-15 (inicio y cierre de sesión).
 */
class LoginTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RolSeeder::class;

    private const ERROR_GENERICO = 'El email o la contraseña no son correctos.';

    private function usuario(string $rol, array $atributos = []): User
    {
        return User::factory()->conRol($rol)->create(array_merge([
            'email' => "{$rol}@prueba.uy",
            'password' => 'clave1234',
        ], $atributos));
    }

    private function login(string $email, string $password = 'clave1234')
    {
        return $this->post('/login', ['email' => $email, 'password' => $password]);
    }

    public function test_cp15_el_ciudadano_entra_a_su_inicio(): void
    {
        $ciudadano = $this->usuario(Rol::CIUDADANO);

        $this->login($ciudadano->email)->assertRedirect(route('ciudadano.inicio'));

        $this->assertAuthenticatedAs($ciudadano);
    }

    public function test_cp15_el_operador_entra_al_panel(): void
    {
        $operador = $this->usuario(Rol::OPERADOR);

        $this->login($operador->email)->assertRedirect(route('panel.inicio'));

        $this->assertAuthenticatedAs($operador);
    }

    public function test_cp15_el_mensaje_no_revela_si_el_email_existe(): void
    {
        $this->usuario(Rol::CIUDADANO);

        $this->login('ciudadano@prueba.uy', 'incorrecta')
            ->assertSessionHasErrors(['email' => self::ERROR_GENERICO]);
        $this->login('no-existe@prueba.uy')
            ->assertSessionHasErrors(['email' => self::ERROR_GENERICO]);

        $this->assertGuest();
    }

    public function test_un_usuario_inactivo_no_inicia_sesion(): void
    {
        $this->usuario(Rol::CIUDADANO, ['activo' => false]);

        $this->login('ciudadano@prueba.uy')
            ->assertSessionHasErrors(['email' => self::ERROR_GENERICO]);

        $this->assertGuest();
    }

    public function test_bloquea_el_sexto_intento_fallido_aunque_la_clave_sea_correcta(): void
    {
        $this->usuario(Rol::CIUDADANO);

        for ($i = 0; $i < 5; $i++) {
            $this->login('ciudadano@prueba.uy', 'incorrecta');
        }
        $this->login('ciudadano@prueba.uy')->assertSessionHasErrors('email');

        $this->assertStringStartsWith('Demasiados intentos.', session('errors')->first('email'));
        $this->assertGuest();
    }

    public function test_regenera_la_sesion_al_iniciar(): void
    {
        $ciudadano = $this->usuario(Rol::CIUDADANO);
        $this->get('/login');
        $idAnterior = session()->getId();

        $this->login($ciudadano->email);

        $this->assertNotSame($idAnterior, session()->getId());
    }

    public function test_cp15_el_logout_cierra_la_sesion(): void
    {
        $this->actingAs($this->usuario(Rol::CIUDADANO))
            ->post('/logout')
            ->assertRedirect(route('login'));

        $this->assertGuest();
    }

    public function test_un_usuario_con_sesion_que_abre_el_login_va_a_su_inicio(): void
    {
        $this->actingAs($this->usuario(Rol::OPERADOR))
            ->get('/login')
            ->assertRedirect(route('panel.inicio'));
    }
}
