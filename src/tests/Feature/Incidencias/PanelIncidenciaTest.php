<?php

namespace Tests\Feature\Incidencias;

use App\Enums\EstadoIncidencia;
use App\Enums\MotivoDescarte;
use App\Models\Categoria;
use App\Models\Incidencia;
use App\Models\Rol;
use App\Models\User;
use App\Services\IncidenciaService;
use Database\Seeders\AreaCategoriaSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Panel interno (T-116): CP-03 de punta a punta, acceso por rol (CP-06) y foto privada (CP-12).
 */
class PanelIncidenciaTest extends TestCase
{
    use RefreshDatabase;

    private User $operador;

    private User $ciudadano;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolSeeder::class, AreaCategoriaSeeder::class]);
        Storage::fake('local');
        $this->operador = User::factory()->conRol(Rol::OPERADOR)->create();
        $this->ciudadano = User::factory()->conRol(Rol::CIUDADANO)->create();
    }

    private function nuevaIncidencia(?UploadedFile $foto = null): Incidencia
    {
        return app(IncidenciaService::class)->crear(
            ['categoria_id' => Categoria::first()->id, 'descripcion' => 'Basural en la esquina', 'lat' => -34.9011, 'lng' => -56.1645],
            $this->ciudadano,
            $foto,
        );
    }

    private function cambiar(Incidencia $incidencia, array $datos, ?User $usuario = null)
    {
        return $this->actingAs($usuario ?? $this->operador)
            ->from(route('panel.incidencias.show', $incidencia->id))
            ->patch(route('panel.incidencias.estado', $incidencia), $datos);
    }

    // ─── CP-03: cambio de estado por el operador ─────────────────────

    public function test_cp03_el_operador_lleva_la_incidencia_hasta_resuelto_y_queda_el_historial(): void
    {
        $incidencia = $this->nuevaIncidencia();

        foreach ([['Validada', 'Verificada'], ['En proceso', 'Cuadrilla asignada'], ['Resuelto', 'Retirado']] as [$estado, $comentario]) {
            $this->cambiar($incidencia, ['estado' => $estado, 'comentario' => $comentario])
                ->assertRedirect(route('panel.incidencias.show', $incidencia->id))
                ->assertSessionHas('exito', "La incidencia pasó a «{$estado}».");
        }

        $incidencia->refresh();
        $this->assertSame(EstadoIncidencia::Resuelto, $incidencia->estado);
        $this->assertNotNull($incidencia->resuelto_en);
        $this->assertCount(4, $incidencia->historial);
        $this->assertSame($this->operador->id, $incidencia->historial->last()->usuario_id);

        // El detalle muestra el historial con los comentarios
        $this->actingAs($this->operador)->get(route('panel.incidencias.show', $incidencia->id))
            ->assertOk()
            ->assertSeeInOrder(['Verificada', 'Cuadrilla asignada', 'Retirado']);
    }

    public function test_cp03_una_transicion_invalida_vuelve_con_el_error_y_no_cambia_nada(): void
    {
        $incidencia = $this->nuevaIncidencia();

        $this->cambiar($incidencia, ['estado' => 'Resuelto'])
            ->assertRedirect(route('panel.incidencias.show', $incidencia->id))
            ->assertSessionHasErrors(['estado' => 'Una incidencia en estado «Pendiente» no puede pasar a «Resuelto».']);

        $this->assertSame(EstadoIncidencia::Pendiente, $incidencia->refresh()->estado);
        $this->assertCount(1, $incidencia->historial);
    }

    public static function datosInvalidos(): array
    {
        return [
            'sin estado' => [['comentario' => 'x'], 'estado'],
            'estado inexistente' => [['estado' => 'Cerrada'], 'estado'],
            'descartar sin motivo' => [['estado' => 'Descartado'], 'motivo'],
            'motivo inexistente' => [['estado' => 'Descartado', 'motivo' => 'Aburrido'], 'motivo'],
            'motivo sin descartar' => [['estado' => 'Validada', 'motivo' => 'Falso'], 'motivo'],
            'comentario largo' => [['estado' => 'Validada', 'comentario' => str_repeat('a', 501)], 'comentario'],
        ];
    }

    #[DataProvider('datosInvalidos')]
    public function test_datos_invalidos_se_rechazan(array $datos, string $campo): void
    {
        $incidencia = $this->nuevaIncidencia();

        $this->cambiar($incidencia, $datos)->assertSessionHasErrors($campo);

        $this->assertSame(EstadoIncidencia::Pendiente, $incidencia->refresh()->estado);
    }

    public function test_el_detalle_ofrece_solo_los_estados_validos_desde_el_actual(): void
    {
        $incidencia = $this->nuevaIncidencia();

        $this->actingAs($this->operador)->get(route('panel.incidencias.show', $incidencia->id))
            ->assertOk()
            ->assertSee('value="Validada"', false)
            ->assertSee('value="Descartado"', false)
            ->assertDontSee('value="Resuelto"', false);
    }

    public function test_t201_el_operador_descarta_con_motivo_y_ya_no_hay_acciones(): void
    {
        $incidencia = $this->nuevaIncidencia();

        $this->cambiar($incidencia, ['estado' => 'Descartado', 'motivo' => 'Duplicado', 'comentario' => 'Ya estaba reportada'])
            ->assertSessionHasNoErrors()
            ->assertSessionHas('exito', 'La incidencia pasó a «Descartado».');

        $incidencia->refresh();
        $this->assertSame(EstadoIncidencia::Descartado, $incidencia->estado);
        $this->assertSame(MotivoDescarte::Duplicado, $incidencia->motivo_descarte);
        $this->assertSame('Ya estaba reportada', $incidencia->historial->last()->comentario);

        $this->actingAs($this->operador)->get(route('panel.incidencias.show', $incidencia->id))
            ->assertOk()
            ->assertSee('Duplicado')
            ->assertDontSee('name="estado"', false);
    }

    public function test_t201_en_proceso_ya_no_se_puede_descartar(): void
    {
        $incidencia = $this->nuevaIncidencia();
        $this->cambiar($incidencia, ['estado' => 'Validada']);
        $this->cambiar($incidencia, ['estado' => 'En proceso']);

        $this->actingAs($this->operador)->get(route('panel.incidencias.show', $incidencia->id))
            ->assertDontSee('value="Descartado"', false);

        $this->cambiar($incidencia, ['estado' => 'Descartado', 'motivo' => 'Falso'])->assertSessionHasErrors('estado');
        $this->assertSame(EstadoIncidencia::EnProceso, $incidencia->refresh()->estado);
    }

    public function test_una_incidencia_validada_ofrece_avanzar_o_volver_a_pendiente(): void
    {
        $incidencia = $this->nuevaIncidencia();
        $this->cambiar($incidencia, ['estado' => 'Validada']);

        $this->actingAs($this->operador)->get(route('panel.incidencias.show', $incidencia->id))
            ->assertOk()
            ->assertSeeInOrder(['Empezar trabajo', 'Volver a pendiente'])
            ->assertDontSee('Marcar como resuelta');

        $this->cambiar($incidencia, ['estado' => 'Pendiente'])->assertSessionHasNoErrors();
        $this->assertSame(EstadoIncidencia::Pendiente, $incidencia->refresh()->estado);
    }

    // ─── CP-06: acceso por rol ───────────────────────────────────────

    public static function rolesSinCambioDeEstado(): array
    {
        return ['jefe de área' => [Rol::JEFE_AREA], 'ejecutor' => [Rol::EJECUTOR]];
    }

    #[DataProvider('rolesSinCambioDeEstado')]
    public function test_cp06_jefe_y_ejecutor_ven_el_detalle_pero_no_cambian_el_estado(string $rol): void
    {
        $usuario = User::factory()->conRol($rol)->create();
        $incidencia = $this->nuevaIncidencia();

        $this->actingAs($usuario)->get(route('panel.incidencias.show', $incidencia->id))
            ->assertOk()
            ->assertDontSee('name="estado"', false);

        $this->cambiar($incidencia, ['estado' => 'Validada'], $usuario)->assertForbidden();
        $this->assertSame(EstadoIncidencia::Pendiente, $incidencia->refresh()->estado);
    }

    public function test_cp06_el_admin_puede_cambiar_el_estado(): void
    {
        $incidencia = $this->nuevaIncidencia();

        $this->cambiar($incidencia, ['estado' => 'Validada'], User::factory()->conRol(Rol::ADMIN)->create())
            ->assertSessionHasNoErrors();

        $this->assertSame(EstadoIncidencia::Validada, $incidencia->refresh()->estado);
    }

    public function test_cp06_el_ciudadano_no_entra_al_detalle_ni_a_la_foto_ni_cambia_el_estado(): void
    {
        $incidencia = $this->nuevaIncidencia(UploadedFile::fake()->image('foto.jpg'));

        $this->actingAs($this->ciudadano)->get(route('panel.incidencias.show', $incidencia->id))->assertForbidden();
        $this->actingAs($this->ciudadano)->get(route('panel.incidencias.foto', $incidencia))->assertForbidden();
        $this->cambiar($incidencia, ['estado' => 'Validada'], $this->ciudadano)->assertForbidden();

        $this->assertSame(EstadoIncidencia::Pendiente, $incidencia->refresh()->estado);
    }

    public function test_cp16_el_invitado_va_al_login(): void
    {
        $incidencia = $this->nuevaIncidencia();

        $this->get(route('panel.incidencias.show', $incidencia->id))->assertRedirect(route('login'));
        $this->get(route('panel.incidencias.foto', $incidencia))->assertRedirect(route('login'));
        $this->patch(route('panel.incidencias.estado', $incidencia), ['estado' => 'Validada'])->assertRedirect(route('login'));
    }

    public function test_una_incidencia_inexistente_da_404(): void
    {
        $this->actingAs($this->operador)->get(route('panel.incidencias.show', 999))->assertNotFound();
    }

    // ─── Listado ─────────────────────────────────────────────────────

    public function test_el_listado_cuenta_por_estado_y_filtra(): void
    {
        $pendiente = $this->nuevaIncidencia();
        $validada = $this->nuevaIncidencia();
        $this->cambiar($validada, ['estado' => 'Validada']);

        $this->actingAs($this->operador)->get(route('panel.inicio'))
            ->assertOk()
            ->assertViewHas('conteos', fn (array $c) => $c['Pendiente'] === 1 && $c['Validada'] === 1 && $c['Resuelto'] === 0)
            ->assertSee($pendiente->codigo)
            ->assertSee($validada->codigo);

        $this->actingAs($this->operador)->get(route('panel.inicio', ['estado' => 'Validada']))
            ->assertOk()
            ->assertSee($validada->codigo)
            ->assertDontSee($pendiente->codigo);
    }

    public function test_un_filtro_que_no_es_un_estado_muestra_todas(): void
    {
        $incidencia = $this->nuevaIncidencia();

        $this->actingAs($this->operador)->get(route('panel.inicio', ['estado' => "'; DROP TABLE incidencia; --"]))
            ->assertOk()
            ->assertViewHas('filtro', null)
            ->assertSee($incidencia->codigo);
    }

    // ─── CP-12: la foto solo se ve por el panel ──────────────────────

    public function test_cp12_el_operador_ve_la_foto_a_traves_del_panel(): void
    {
        $incidencia = $this->nuevaIncidencia(UploadedFile::fake()->image('foto.jpg'));

        $respuesta = $this->actingAs($this->operador)->get(route('panel.incidencias.foto', $incidencia));

        $respuesta->assertOk()->assertHeader('Content-Type', 'image/jpeg');
        $this->assertStringContainsString('private', $respuesta->headers->get('Cache-Control'));
    }

    public function test_sin_foto_da_404(): void
    {
        $incidencia = $this->nuevaIncidencia();

        $this->actingAs($this->operador)->get(route('panel.incidencias.foto', $incidencia))->assertNotFound();
    }
}
