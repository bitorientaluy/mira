<?php

namespace Tests\Feature\Incidencias;

use App\Enums\EstadoIncidencia;
use App\Models\Categoria;
use App\Models\Rol;
use App\Models\User;
use App\Services\FotoService;
use App\Services\IncidenciaService;
use Database\Seeders\AreaCategoriaSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Mockery;
use RuntimeException;
use Tests\TestCase;

/**
 * PU-05: IncidenciaService::crear() contra la base real (MySQL espacial).
 */
class IncidenciaServiceTest extends TestCase
{
    use RefreshDatabase;

    private User $ciudadano;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolSeeder::class, AreaCategoriaSeeder::class]);
        $this->ciudadano = User::factory()->conRol(Rol::CIUDADANO)->create();
    }

    private function datos(): array
    {
        return ['categoria_id' => Categoria::first()->id, 'descripcion' => '  Bolsas en la vereda  ', 'lat' => '-34.9011', 'lng' => '-56.1645'];
    }

    public function test_pu05_crea_pendiente_con_el_area_de_la_categoria_y_la_primera_fila_del_historial(): void
    {
        $incidencia = app(IncidenciaService::class)->crear($this->datos(), $this->ciudadano);

        $this->assertSame(EstadoIncidencia::Pendiente, $incidencia->estado);
        $this->assertSame(Categoria::first()->area_id, $incidencia->area_id);
        $this->assertSame($this->ciudadano->id, $incidencia->ciudadano_id);
        $this->assertSame('Bolsas en la vereda', $incidencia->descripcion);
        $this->assertSame(IncidenciaService::codigo($incidencia->id, (int) now()->format('Y')), $incidencia->codigo);

        $historial = $incidencia->historial;
        $this->assertCount(1, $historial);
        $this->assertNull($historial[0]->estado_anterior);
        $this->assertSame(EstadoIncidencia::Pendiente, $historial[0]->estado_nuevo);
        $this->assertSame($this->ciudadano->id, $historial[0]->usuario_id);
    }

    public function test_pu04_guarda_el_punto_con_srid_4326_en_orden_lat_lng(): void
    {
        $incidencia = app(IncidenciaService::class)->crear($this->datos(), $this->ciudadano);

        $punto = \DB::table('incidencia')->where('id', $incidencia->id)
            ->selectRaw('ST_SRID(ubicacion) AS srid, ST_Latitude(ubicacion) AS lat, ST_Longitude(ubicacion) AS lng')
            ->first();

        $this->assertSame(4326, (int) $punto->srid);
        $this->assertEqualsWithDelta(-34.9011, (float) $punto->lat, 0.000001);
        $this->assertEqualsWithDelta(-56.1645, (float) $punto->lng, 0.000001);
    }

    public function test_si_falla_el_guardado_de_la_foto_no_queda_ni_la_incidencia_ni_el_archivo(): void
    {
        $fotos = Mockery::mock(FotoService::class);
        $fotos->shouldReceive('guardar')->once()->andThrow(new RuntimeException('disco lleno'));
        $fotos->shouldReceive('borrar')->once()->with(null);
        $this->app->instance(FotoService::class, $fotos);

        try {
            app(IncidenciaService::class)->crear($this->datos(), $this->ciudadano, UploadedFile::fake()->image('f.jpg'));
            $this->fail('Se esperaba una excepción.');
        } catch (RuntimeException $e) {
            $this->assertSame('disco lleno', $e->getMessage());
        }

        $this->assertDatabaseCount('incidencia', 0);
        $this->assertDatabaseCount('historial_incidencia', 0);
    }

    public function test_si_falla_la_base_despues_de_guardar_la_foto_se_borra_el_archivo(): void
    {
        // Una ruta más larga que la columna foto_ruta (255) hace fallar el save() que viene después de guardar la foto
        $rutaLarga = 'incidencias/'.str_repeat('x', 300).'.jpg';
        $fotos = Mockery::mock(FotoService::class);
        $fotos->shouldReceive('guardar')->once()->andReturn($rutaLarga);
        $fotos->shouldReceive('borrar')->once()->with($rutaLarga);
        $this->app->instance(FotoService::class, $fotos);

        try {
            app(IncidenciaService::class)->crear($this->datos(), $this->ciudadano, UploadedFile::fake()->image('f.jpg'));
            $this->fail('Se esperaba una excepción de la base.');
        } catch (\Illuminate\Database\QueryException) {
            // esperado
        }

        $this->assertDatabaseCount('incidencia', 0);
    }
}
