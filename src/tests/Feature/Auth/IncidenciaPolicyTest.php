<?php

namespace Tests\Feature\Auth;

use App\Models\Categoria;
use App\Models\Incidencia;
use App\Models\Rol;
use App\Models\User;
use App\Services\IncidenciaService;
use Database\Seeders\AreaCategoriaSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Log;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * T-201: IncidenciaPolicy (quién ve y quién cambia el estado) y registro de los 403 (CP-06).
 */
class IncidenciaPolicyTest extends TestCase
{
    use RefreshDatabase;

    private User $autor;

    private Incidencia $incidencia;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolSeeder::class, AreaCategoriaSeeder::class]);
        $this->autor = User::factory()->conRol(Rol::CIUDADANO)->create();
        $this->incidencia = app(IncidenciaService::class)->crear(
            ['categoria_id' => Categoria::first()->id, 'descripcion' => 'Basural', 'lat' => -34.9011, 'lng' => -56.1645],
            $this->autor,
        );
    }

    /**
     * Rol → [viewAny, view, cambiarEstado]
     */
    public static function permisosPorRol(): array
    {
        return [
            'operador' => [Rol::OPERADOR, true, true, true],
            'administrador' => [Rol::ADMIN, true, true, true],
            'jefe de área' => [Rol::JEFE_AREA, true, true, false],
            'ejecutor' => [Rol::EJECUTOR, true, true, false],
            'otro ciudadano' => [Rol::CIUDADANO, false, false, false],
        ];
    }

    #[DataProvider('permisosPorRol')]
    public function test_permisos_segun_el_rol(string $rol, bool $listado, bool $detalle, bool $cambiar): void
    {
        $usuario = User::factory()->conRol($rol)->create();

        $this->assertSame($listado, $usuario->can('viewAny', Incidencia::class));
        $this->assertSame($detalle, $usuario->can('view', $this->incidencia));
        $this->assertSame($cambiar, $usuario->can('cambiarEstado', $this->incidencia));
    }

    public function test_el_autor_ve_su_incidencia_pero_no_el_panel_ni_cambia_el_estado(): void
    {
        $this->assertTrue($this->autor->can('view', $this->incidencia));
        $this->assertFalse($this->autor->can('viewAny', Incidencia::class));
        $this->assertFalse($this->autor->can('cambiarEstado', $this->incidencia));
    }

    public function test_cp06_un_403_queda_en_el_log_con_usuario_rol_y_ruta(): void
    {
        $ciudadano = User::factory()->conRol(Rol::CIUDADANO)->create();
        Log::spy();

        $this->actingAs($ciudadano)->get('/panel')->assertForbidden();

        Log::shouldHaveReceived('warning')->once()->withArgs(
            fn (string $mensaje, array $contexto) => $mensaje === 'Acceso denegado'
                && $contexto['usuario_id'] === $ciudadano->id
                && $contexto['rol'] === Rol::CIUDADANO
                && $contexto['ruta'] === 'panel',
        );
    }

    public function test_el_rechazo_de_una_policy_se_muestra_en_espanol(): void
    {
        $this->actingAs(User::factory()->conRol(Rol::CIUDADANO)->create())
            ->get(route('incidencias.confirmacion', $this->incidencia))
            ->assertForbidden()
            ->assertSee('No tenés permiso para acceder a esta página.');
    }
}
