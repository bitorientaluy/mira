<?php

namespace Tests\Feature\Incidencias;

use App\Data\CambioDeEstado;
use App\Enums\EstadoIncidencia;
use App\Enums\MotivoDescarte;
use App\Exceptions\TransicionInvalidaException;
use App\Models\Area;
use App\Models\Categoria;
use App\Models\Incidencia;
use App\Models\Rol;
use App\Models\User;
use App\Services\IncidenciaService;
use Database\Seeders\AreaCategoriaSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * PU-06 y la parte de servicio de CP-03: IncidenciaService::cambiarEstado() contra la base real.
 */
class CambioEstadoServiceTest extends TestCase
{
    use RefreshDatabase;

    private IncidenciaService $servicio;

    private User $operador;

    private Incidencia $incidencia;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolSeeder::class, AreaCategoriaSeeder::class]);
        $this->servicio = app(IncidenciaService::class);
        $this->operador = User::factory()->conRol(Rol::OPERADOR)->create();
        $ciudadano = User::factory()->conRol(Rol::CIUDADANO)->create();
        $this->incidencia = $this->servicio->crear(
            ['categoria_id' => Categoria::first()->id, 'descripcion' => 'Contenedor desbordado', 'lat' => -34.9011, 'lng' => -56.1645],
            $ciudadano,
        );
    }

    private function cambiar(EstadoIncidencia $nuevo, ?string $comentario = null, ?MotivoDescarte $motivo = null): Incidencia
    {
        return $this->servicio->cambiarEstado($this->incidencia, new CambioDeEstado($nuevo, $comentario, $motivo), $this->operador);
    }

    public function test_cp03_recorre_el_flujo_completo_y_registra_cada_paso_en_el_historial(): void
    {
        $this->cambiar(EstadoIncidencia::Validada, 'Verificada con la foto');
        $this->cambiar(EstadoIncidencia::EnProceso, 'Cuadrilla asignada');
        $resuelta = $this->cambiar(EstadoIncidencia::Resuelto, 'Retirado');

        $this->assertSame(EstadoIncidencia::Resuelto, $resuelta->estado);
        $this->assertNotNull($resuelta->resuelto_en);

        $historial = $resuelta->historial;
        $this->assertCount(4, $historial); // alta + 3 cambios
        $this->assertSame(
            [[null, 'Pendiente'], ['Pendiente', 'Validada'], ['Validada', 'En proceso'], ['En proceso', 'Resuelto']],
            $historial->map(fn ($h) => [$h->estado_anterior?->value, $h->estado_nuevo->value])->all(),
        );
        $this->assertSame($this->operador->id, $historial[3]->usuario_id);
        $this->assertSame('Retirado', $historial[3]->comentario);
    }

    public function test_transicion_invalida_se_rechaza_y_no_cambia_nada(): void
    {
        try {
            $this->cambiar(EstadoIncidencia::Resuelto);
            $this->fail('Se esperaba TransicionInvalidaException.');
        } catch (TransicionInvalidaException $e) {
            $this->assertStringContainsString('Pendiente', $e->getMessage());
        }

        $this->assertSame(EstadoIncidencia::Pendiente, $this->incidencia->refresh()->estado);
        $this->assertCount(1, $this->incidencia->historial);
    }

    public function test_usa_el_estado_de_la_base_y_no_el_del_objeto_en_memoria(): void
    {
        // Otro funcionario la validó mientras este tenía la página abierta con el estado viejo
        $copiaVieja = Incidencia::find($this->incidencia->id);
        $this->cambiar(EstadoIncidencia::Validada);

        $this->expectException(TransicionInvalidaException::class);
        $this->servicio->cambiarEstado($copiaVieja, new CambioDeEstado(EstadoIncidencia::Validada), $this->operador);
    }

    public function test_reabrir_vuelve_a_pendiente_y_borra_la_fecha_de_resolucion(): void
    {
        $this->cambiar(EstadoIncidencia::Validada);
        $this->cambiar(EstadoIncidencia::EnProceso);
        $this->cambiar(EstadoIncidencia::Resuelto);

        $reabierta = $this->cambiar(EstadoIncidencia::Pendiente, 'Volvieron a tirar basura');

        $this->assertSame(EstadoIncidencia::Pendiente, $reabierta->estado);
        $this->assertNull($reabierta->resuelto_en);
    }

    public function test_se_puede_volver_un_paso_atras_y_queda_en_el_historial(): void
    {
        $this->cambiar(EstadoIncidencia::Validada);
        $this->cambiar(EstadoIncidencia::EnProceso);

        $devuelta = $this->cambiar(EstadoIncidencia::Validada, 'Se asignó por error');

        $this->assertSame(EstadoIncidencia::Validada, $devuelta->estado);
        $ultimo = $devuelta->historial->last();
        $this->assertSame(EstadoIncidencia::EnProceso, $ultimo->estado_anterior);
        $this->assertSame(EstadoIncidencia::Validada, $ultimo->estado_nuevo);
        $this->assertSame('Se asignó por error', $ultimo->comentario);
    }

    public function test_descartar_exige_motivo_y_lo_guarda(): void
    {
        try {
            $this->cambiar(EstadoIncidencia::Descartado);
            $this->fail('Se esperaba TransicionInvalidaException por falta de motivo.');
        } catch (TransicionInvalidaException) {
            $this->assertSame(EstadoIncidencia::Pendiente, $this->incidencia->refresh()->estado);
        }

        $descartada = $this->cambiar(EstadoIncidencia::Descartado, null, MotivoDescarte::Duplicado);

        $this->assertSame(EstadoIncidencia::Descartado, $descartada->estado);
        $this->assertSame(MotivoDescarte::Duplicado, $descartada->motivo_descarte);
    }

    public function test_motivo_sin_descartar_se_rechaza(): void
    {
        $this->expectException(TransicionInvalidaException::class);
        $this->cambiar(EstadoIncidencia::Validada, null, MotivoDescarte::Falso);
    }

    public function test_t201_al_validar_se_puede_corregir_el_area_y_queda_en_el_historial(): void
    {
        $otra = Area::create(['nombre' => 'Arbolado']);
        $original = $this->incidencia->area->nombre;

        $validada = $this->servicio->cambiarEstado(
            $this->incidencia,
            new CambioDeEstado(EstadoIncidencia::Validada, 'Son ramas caídas', area: $otra),
            $this->operador,
        );

        $this->assertSame($otra->id, $validada->area_id);
        $this->assertSame("Área cambiada de «{$original}» a «Arbolado». Son ramas caídas", $validada->historial->last()->comentario);
    }

    public function test_t201_confirmar_la_misma_area_no_agrega_nota(): void
    {
        $validada = $this->servicio->cambiarEstado(
            $this->incidencia,
            new CambioDeEstado(EstadoIncidencia::Validada, area: $this->incidencia->area),
            $this->operador,
        );

        $this->assertSame($this->incidencia->area_id, $validada->area_id);
        $this->assertNull($validada->historial->last()->comentario);
    }

    public function test_t201_el_area_solo_se_cambia_al_validar(): void
    {
        $this->cambiar(EstadoIncidencia::Validada);
        $otra = Area::create(['nombre' => 'Arbolado']);

        $this->expectException(TransicionInvalidaException::class);
        $this->servicio->cambiarEstado(
            $this->incidencia,
            new CambioDeEstado(EstadoIncidencia::EnProceso, area: $otra),
            $this->operador,
        );
    }

    public function test_comentario_vacio_se_guarda_como_null(): void
    {
        $validada = $this->cambiar(EstadoIncidencia::Validada, '   ');

        $this->assertNull($validada->historial->last()->comentario);
    }
}
