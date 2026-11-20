<?php

namespace Tests\Feature\Auth;

use App\Data\PerfilGoogle;
use App\Exceptions\CuentaGoogleRechazadaException;
use App\Models\Rol;
use App\Models\User;
use App\Services\Auth\CuentaGoogleService;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Socialite\Two\User as UsuarioSocialite;
use Tests\TestCase;

/**
 * T-411: qué usuario de MIRA corresponde a una cuenta de Google (registro y login son lo mismo).
 */
class CuentaGoogleServiceTest extends TestCase
{
    use RefreshDatabase;

    private CuentaGoogleService $servicio;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed(RolSeeder::class);
        $this->servicio = app(CuentaGoogleService::class);
    }

    private function perfil(array $cambios = []): PerfilGoogle
    {
        return new PerfilGoogle(...array_merge([
            'id' => '1098765432109876543210',
            'email' => 'ana@gmail.com',
            'nombre' => 'Ana Pérez',
            'emailVerificado' => true,
        ], $cambios));
    }

    public function test_la_primera_vez_crea_un_ciudadano_sin_contrasena_ni_cedula(): void
    {
        $usuario = $this->servicio->usuarioPara($this->perfil());

        $this->assertTrue($usuario->exists);
        $this->assertTrue($usuario->esRol(Rol::CIUDADANO));
        $this->assertSame('ana@gmail.com', $usuario->email);
        $this->assertSame('1098765432109876543210', $usuario->google_id);
        $this->assertNull($usuario->getRawOriginal('password'));
        $this->assertNull($usuario->cedula);
    }

    public function test_la_segunda_vez_reutiliza_el_usuario_sin_pisar_el_nombre(): void
    {
        $primero = $this->servicio->usuarioPara($this->perfil());
        $segundo = $this->servicio->usuarioPara($this->perfil(['nombre' => 'Otro']));

        $this->assertSame($primero->id, $segundo->id);
        $this->assertSame('Ana Pérez', $segundo->nombre);
        $this->assertSame(1, User::count());
    }

    public function test_identifica_por_google_id_aunque_cambie_el_email(): void
    {
        $primero = $this->servicio->usuarioPara($this->perfil());
        $segundo = $this->servicio->usuarioPara($this->perfil(['email' => 'ana.perez@gmail.com']));

        $this->assertSame($primero->id, $segundo->id);
    }

    public function test_vincula_un_ciudadano_que_ya_tenia_cuenta_con_ese_email(): void
    {
        $existente = User::factory()->conRol(Rol::CIUDADANO)->create(['email' => 'ana@gmail.com']);

        $usuario = $this->servicio->usuarioPara($this->perfil());

        $this->assertSame($existente->id, $usuario->id);
        $this->assertSame('1098765432109876543210', $usuario->refresh()->google_id);
        $this->assertSame(1, User::count());
    }

    public function test_rechaza_un_email_sin_verificar_y_no_crea_nada(): void
    {
        $this->expectExceptionObject(CuentaGoogleRechazadaException::emailSinVerificar());

        try {
            $this->servicio->usuarioPara($this->perfil(['emailVerificado' => false]));
        } finally {
            $this->assertSame(0, User::count());
        }
    }

    public function test_rechaza_el_email_de_un_funcionario(): void
    {
        User::factory()->conRol(Rol::OPERADOR)->create(['email' => 'ana@gmail.com']);

        $this->expectExceptionObject(CuentaGoogleRechazadaException::esFuncionario());
        $this->servicio->usuarioPara($this->perfil());
    }

    public function test_rechaza_una_cuenta_desactivada(): void
    {
        User::factory()->conRol(Rol::CIUDADANO)->create(['email' => 'ana@gmail.com', 'activo' => false]);

        $this->expectExceptionObject(CuentaGoogleRechazadaException::inactiva());
        $this->servicio->usuarioPara($this->perfil());
    }

    public function test_no_vincula_un_email_que_ya_tiene_otra_cuenta_de_google(): void
    {
        $this->servicio->usuarioPara($this->perfil());

        $this->expectExceptionObject(CuentaGoogleRechazadaException::vinculadaAOtraCuenta());
        $this->servicio->usuarioPara($this->perfil(['id' => '999']));
    }

    public function test_quien_entro_con_google_no_puede_entrar_con_contrasena(): void
    {
        $this->servicio->usuarioPara($this->perfil());

        $this->post('/login', ['email' => 'ana@gmail.com', 'password' => ''])->assertSessionHasErrors();
        $this->post('/login', ['email' => 'ana@gmail.com', 'password' => 'cualquiera'])->assertSessionHasErrors();
        $this->assertGuest();
    }

    public function test_el_perfil_se_arma_desde_socialite_con_el_email_en_minusculas(): void
    {
        $socialite = (new UsuarioSocialite)
            ->setRaw(['email_verified' => true])
            ->map(['id' => '123', 'email' => ' Ana@Gmail.com ', 'name' => 'Ana', 'avatar' => 'https://foto']);

        $perfil = PerfilGoogle::desdeSocialite($socialite);

        $this->assertSame('123', $perfil->id);
        $this->assertSame('ana@gmail.com', $perfil->email);
        $this->assertTrue($perfil->emailVerificado);
    }

    public function test_sin_el_dato_de_verificacion_el_email_cuenta_como_no_verificado(): void
    {
        $socialite = (new UsuarioSocialite)->setRaw([])->map(['id' => '123', 'email' => 'ana@gmail.com', 'name' => 'Ana']);

        $this->assertFalse(PerfilGoogle::desdeSocialite($socialite)->emailVerificado);
    }
}
