<?php

namespace Tests\Feature\Auth;

use App\Models\Rol;
use App\Models\User;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * CP-06 (permisos por rol) y CP-16 (acceso sin autenticación).
 */
class AccesoPorRolTest extends TestCase
{
    use RefreshDatabase;

    protected bool $seed = true;

    protected string $seeder = RolSeeder::class;

    public static function rutasProtegidas(): array
    {
        return [
            'inicio del ciudadano' => ['/inicio'],
            'panel interno' => ['/panel'],
        ];
    }

    public static function funcionarios(): array
    {
        return [
            'operador' => [Rol::OPERADOR],
            'jefe de área' => [Rol::JEFE_AREA],
            'ejecutor' => [Rol::EJECUTOR],
            'administrador' => [Rol::ADMIN],
        ];
    }

    #[DataProvider('rutasProtegidas')]
    public function test_cp16_un_invitado_es_redirigido_al_login(string $ruta): void
    {
        $this->get($ruta)->assertRedirect(route('login'));
    }

    public function test_cp06_un_ciudadano_no_accede_al_panel_interno(): void
    {
        $this->actingAs(User::factory()->conRol(Rol::CIUDADANO)->create())
            ->get('/panel')
            ->assertForbidden()
            ->assertSee('No tenés permiso para acceder a esta página.')
            ->assertDontSee('Panel interno');
    }

    public function test_cp06_un_operador_no_accede_al_inicio_del_ciudadano(): void
    {
        $this->actingAs(User::factory()->conRol(Rol::OPERADOR)->create())
            ->get('/inicio')
            ->assertForbidden();
    }

    #[DataProvider('funcionarios')]
    public function test_cp06_los_funcionarios_acceden_al_panel(string $rol): void
    {
        $this->actingAs(User::factory()->conRol($rol)->create())
            ->get('/panel')
            ->assertOk()
            ->assertSee('Panel interno');
    }
}
