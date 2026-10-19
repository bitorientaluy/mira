<?php

namespace Tests\Feature\Incidencias;

use App\Enums\EstadoIncidencia;
use App\Models\Categoria;
use App\Models\Incidencia;
use App\Models\Rol;
use App\Models\User;
use Database\Seeders\AreaCategoriaSeeder;
use Database\Seeders\RolSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

/**
 * Reporte de incidencia desde la web (T-111): CP-01, 02, 09, 10, 11, 12, 13, 14 y 16.
 */
class ReporteIncidenciaTest extends TestCase
{
    use RefreshDatabase;

    private User $ciudadano;

    protected function setUp(): void
    {
        parent::setUp();
        $this->seed([RolSeeder::class, AreaCategoriaSeeder::class]);
        Storage::fake('local');
        $this->ciudadano = User::factory()->conRol(Rol::CIUDADANO)->create();
    }

    private function datosValidos(array $cambios = []): array
    {
        return array_merge([
            'categoria_id' => Categoria::first()->id,
            'descripcion' => 'Contenedor desbordado en la esquina',
            'lat' => -34.9011,
            'lng' => -56.1645,
        ], $cambios);
    }

    private function reportar(array $datos)
    {
        return $this->actingAs($this->ciudadano)->from('/reportar')->post('/incidencias', $datos);
    }

    public function test_cp01_crea_la_incidencia_con_codigo_estado_pendiente_y_el_area_de_la_categoria(): void
    {
        $categoria = Categoria::first();

        $respuesta = $this->reportar($this->datosValidos(['categoria_id' => $categoria->id]));

        $incidencia = Incidencia::conCoordenadas()->sole();
        $respuesta->assertRedirect(route('incidencias.confirmacion', $incidencia));
        $this->assertMatchesRegularExpression('/^MIR-\d{4}-\d{6}$/', $incidencia->codigo);
        $this->assertSame(EstadoIncidencia::Pendiente, $incidencia->estado);
        $this->assertSame($this->ciudadano->id, $incidencia->ciudadano_id);
        $this->assertSame($categoria->area_id, $incidencia->area_id);
        $this->assertEqualsWithDelta(-34.9011, (float) $incidencia->lat, 0.000001);
        $this->assertEqualsWithDelta(-56.1645, (float) $incidencia->lng, 0.000001);
        $this->assertDatabaseHas('historial_incidencia', [
            'incidencia_id' => $incidencia->id,
            'estado_anterior' => null,
            'estado_nuevo' => EstadoIncidencia::Pendiente->value,
        ]);
    }

    public function test_cp01_la_confirmacion_muestra_el_codigo(): void
    {
        $this->reportar($this->datosValidos());
        $incidencia = Incidencia::sole();

        $this->actingAs($this->ciudadano)
            ->get(route('incidencias.confirmacion', $incidencia))
            ->assertOk()
            ->assertSee($incidencia->codigo);
    }

    public function test_cp12_guarda_la_foto_en_el_disco_privado(): void
    {
        $this->reportar($this->datosValidos(['foto' => UploadedFile::fake()->image('foto.jpg', 800, 600)]));

        $incidencia = Incidencia::sole();
        $this->assertSame("incidencias/{$incidencia->codigo}.jpg", $incidencia->foto_ruta);
        Storage::disk('local')->assertExists($incidencia->foto_ruta);
    }

    public function test_cp02_sin_ubicacion_rechaza_con_un_mensaje_claro_y_conserva_los_datos(): void
    {
        $this->reportar($this->datosValidos(['lat' => null, 'lng' => null]))
            ->assertRedirect('/reportar')
            ->assertSessionHasErrors(['lat' => 'Marcá en el mapa dónde está la incidencia.'])
            ->assertSessionHasInput('descripcion', 'Contenedor desbordado en la esquina');

        $this->assertDatabaseCount('incidencia', 0);
    }

    public function test_cp09_rechaza_la_descripcion_vacia_o_con_solo_espacios(): void
    {
        foreach (['', '     '] as $descripcion) {
            $this->reportar($this->datosValidos(['descripcion' => $descripcion]))
                ->assertSessionHasErrors(['descripcion' => 'El campo descripción es obligatorio.']);
        }

        $this->assertDatabaseCount('incidencia', 0);
    }

    public function test_cp10_rechaza_una_categoria_inexistente_inactiva_o_no_numerica(): void
    {
        $inactiva = Categoria::first();
        $inactiva->update(['activa' => false]);

        foreach ([999, 'abc', $inactiva->id] as $categoria) {
            $this->reportar($this->datosValidos(['categoria_id' => $categoria]))
                ->assertSessionHasErrors('categoria_id');
        }

        $this->assertDatabaseCount('incidencia', 0);
    }

    public static function coordenadasInvalidas(): array
    {
        return [
            'fuera del planeta' => [95, -200, ['lat', 'lng']],
            'no numéricas' => ['abc', 'xyz', ['lat', 'lng']],
            'Buenos Aires' => [-34.6037, -58.3816, ['lat', 'lng']],
            'Maldonado (misma latitud, otra longitud)' => [-34.9087, -54.9592, ['lng']],
        ];
    }

    #[DataProvider('coordenadasInvalidas')]
    public function test_cp11_rechaza_coordenadas_invalidas_o_fuera_de_montevideo(mixed $lat, mixed $lng, array $camposConError): void
    {
        $this->reportar($this->datosValidos(['lat' => $lat, 'lng' => $lng]))
            ->assertSessionHasErrors($camposConError);

        $this->assertDatabaseCount('incidencia', 0);
    }

    public function test_cp13_rechaza_una_foto_de_mas_de_5_mb_sin_guardar_nada(): void
    {
        $foto = UploadedFile::fake()->image('grande.jpg', 800, 600)->size(5200); // KB

        $this->reportar($this->datosValidos(['foto' => $foto]))
            ->assertSessionHasErrors(['foto' => 'La foto no puede superar los 5 MB.'])
            ->assertSessionHasInput('descripcion');

        $this->assertDatabaseCount('incidencia', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public static function archivosNoPermitidos(): array
    {
        return [
            'script PHP con extensión .jpg' => ['falsa.jpg', '<?php echo "hola"; ?>'],
            'PDF' => ['documento.pdf', "%PDF-1.4\n%fin"],
            'texto plano' => ['nota.png', 'no soy una imagen'],
            'JPEG corrupto (cabecera válida, resto basura)' => ['rota.jpg', "\xFF\xD8\xFF\xE0".str_repeat('x', 200)],
        ];
    }

    #[DataProvider('archivosNoPermitidos')]
    public function test_cp14_rechaza_archivos_que_no_son_imagenes_por_su_contenido(string $nombre, string $contenido): void
    {
        $archivo = UploadedFile::fake()->createWithContent($nombre, $contenido);

        $this->reportar($this->datosValidos(['foto' => $archivo]))->assertSessionHasErrors('foto');

        $this->assertDatabaseCount('incidencia', 0);
        $this->assertSame([], Storage::disk('local')->allFiles());
    }

    public function test_cp16_un_invitado_no_puede_reportar(): void
    {
        $this->get('/reportar')->assertRedirect(route('login'));
        $this->post('/incidencias', $this->datosValidos())->assertRedirect(route('login'));

        $this->assertDatabaseCount('incidencia', 0);
    }

    public function test_un_operador_no_puede_reportar(): void
    {
        $operador = User::factory()->conRol(Rol::OPERADOR)->create();

        $this->actingAs($operador)->get('/reportar')->assertForbidden();
        $this->actingAs($operador)->post('/incidencias', $this->datosValidos())->assertForbidden();
    }

    public function test_otro_ciudadano_no_ve_una_confirmacion_ajena(): void
    {
        $this->reportar($this->datosValidos());
        $otro = User::factory()->conRol(Rol::CIUDADANO)->create();

        $this->actingAs($otro)
            ->get(route('incidencias.confirmacion', Incidencia::sole()))
            ->assertForbidden();
    }
}
