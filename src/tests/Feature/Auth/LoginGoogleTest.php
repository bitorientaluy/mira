<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Database\Seeders\AreaCategoriaSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Two\User as UsuarioSocialite;
use RuntimeException;
use Tests\TestCase;

/**
 * T-411: login y registro de ciudadanos con Google (Socialite simulado) y cédula en el primer ingreso.
 */
class LoginGoogleTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolSeeder::class, AreaCategoriaSeeder::class]);
        config(['services.google.client_id' => 'id-de-prueba', 'services.google.client_secret' => 'secreto-de-prueba']);
    }

    private function googleDevuelve(array $datos = [], bool $verificado = true): void
    {
        $usuario = (new UsuarioSocialite)
            ->setRaw(['email_verified' => $verificado])
            ->map(array_merge(['id' => 'g-123', 'email' => 'ana@gmail.com', 'name' => 'Ana Pérez'], $datos));

        Socialite::shouldReceive('driver->user')->andReturn($usuario);
    }

    // ─── Botón y redirección ─────────────────────────────────────────

    public function test_el_login_muestra_el_boton_de_google_si_esta_configurado(): void
    {
        $this->get('/login')->assertOk()->assertSee('Continuar con Google')->assertSee(route('login.google'), false);
        $this->get('/registro')->assertOk()->assertSee('Continuar con Google');
    }

    public function test_sin_credenciales_no_hay_boton_y_las_rutas_dan_404(): void
    {
        config(['services.google.client_id' => null]);

        $this->get('/login')->assertOk()->assertDontSee('Continuar con Google');
        $this->get('/auth/google')->assertNotFound();
        $this->get('/auth/google/callback')->assertNotFound();
    }

    public function test_el_boton_lleva_a_google(): void
    {
        Socialite::shouldReceive('driver->redirect')->andReturn(redirect('https://accounts.google.com/o/oauth2/v2/auth?state=x'));

        $this->get('/auth/google')->assertRedirect('https://accounts.google.com/o/oauth2/v2/auth?state=x');
    }

    // ─── Vuelta de Google ────────────────────────────────────────────

    public function test_la_primera_vez_registra_al_ciudadano_y_le_pide_la_cedula(): void
    {
        $this->googleDevuelve();

        $this->get('/auth/google/callback')->assertRedirect(route('ciudadano.inicio'));

        $usuario = User::where('email', 'ana@gmail.com')->sole();
        $this->assertAuthenticatedAs($usuario);
        $this->assertTrue($usuario->esRol(Rol::CIUDADANO));
        $this->get('/inicio')->assertRedirect(route('perfil.cedula'));
    }

    public function test_un_ciudadano_que_ya_entro_vuelve_a_su_cuenta(): void
    {
        $this->googleDevuelve();
        $this->get('/auth/google/callback');
        auth()->logout();

        $this->get('/auth/google/callback')->assertRedirect(route('ciudadano.inicio'));
        $this->assertSame(1, User::where('email', 'ana@gmail.com')->count());
    }

    public function test_el_email_de_un_funcionario_vuelve_al_login_con_el_motivo(): void
    {
        User::factory()->conRol(Rol::OPERADOR)->create(['email' => 'ana@gmail.com']);
        $this->googleDevuelve();

        $this->get('/auth/google/callback')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['google' => 'Los funcionarios ingresan con su email y contraseña.']);
        $this->assertGuest();
    }

    public function test_un_email_sin_verificar_no_entra(): void
    {
        $this->googleDevuelve(verificado: false);

        $this->get('/auth/google/callback')->assertRedirect(route('login'))->assertSessionHasErrors('google');
        $this->assertGuest();
        $this->assertSame(0, User::where('email', 'ana@gmail.com')->count());
    }

    public function test_si_la_persona_cancela_en_google_vuelve_al_login_con_un_aviso(): void
    {
        Socialite::shouldReceive('driver->user')->andThrow(new RuntimeException('access_denied'));

        $this->get('/auth/google/callback')
            ->assertRedirect(route('login'))
            ->assertSessionHasErrors(['google' => 'No se pudo iniciar sesión con Google. Probá de nuevo.']);
        $this->assertGuest();
    }

    public function test_con_sesion_iniciada_las_rutas_de_google_no_se_usan(): void
    {
        $this->actingAs(User::factory()->conRol(Rol::CIUDADANO)->create())
            ->get('/auth/google')
            ->assertRedirect(route('ciudadano.inicio'));
    }

    // ─── Cédula en el primer ingreso ─────────────────────────────────

    public function test_sin_cedula_reportar_lleva_a_completarla_y_despues_vuelve(): void
    {
        $ciudadano = User::factory()->conRol(Rol::CIUDADANO)->sinCedula()->create();

        $this->actingAs($ciudadano)->get('/reportar')->assertRedirect(route('perfil.cedula'));
        $this->actingAs($ciudadano)->get('/perfil/cedula')->assertOk()->assertSee('Un paso más');

        $this->actingAs($ciudadano)->post('/perfil/cedula', ['cedula' => ' 12345678 '])->assertRedirect('/reportar');

        $this->assertSame('12345678', $ciudadano->refresh()->cedula);
        $this->actingAs($ciudadano)->get('/reportar')->assertOk();
    }

    public function test_la_cedula_no_puede_repetirse(): void
    {
        User::factory()->conRol(Rol::CIUDADANO)->create(['cedula' => '12345678']);
        $ciudadano = User::factory()->conRol(Rol::CIUDADANO)->sinCedula()->create();

        $this->actingAs($ciudadano)->post('/perfil/cedula', ['cedula' => '12345678'])->assertSessionHasErrors('cedula');
        $this->assertNull($ciudadano->refresh()->cedula);
    }

    public function test_con_cedula_cargada_no_se_puede_cambiar_desde_esa_pantalla(): void
    {
        $ciudadano = User::factory()->conRol(Rol::CIUDADANO)->create(['cedula' => '11111111']);

        $this->actingAs($ciudadano)->get('/perfil/cedula')->assertRedirect(route('ciudadano.inicio'));
        $this->actingAs($ciudadano)->post('/perfil/cedula', ['cedula' => '22222222']);

        $this->assertSame('11111111', $ciudadano->refresh()->cedula);
    }

    public function test_un_funcionario_no_usa_la_pantalla_de_cedula(): void
    {
        $this->actingAs(User::factory()->conRol(Rol::OPERADOR)->sinCedula()->create())
            ->get('/perfil/cedula')
            ->assertForbidden();
    }
}
