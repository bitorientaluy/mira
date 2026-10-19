<?php

namespace Tests\Feature\Incidencias;

use App\Services\FotoService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;
use Tests\TestCase;

/**
 * Fotos sin metadatos (Programación 4.i, CP-12, CP-14 y PU-03).
 */
class FotoServiceTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        Storage::fake('local');
    }

    /**
     * JPEG generado con GD al que se le inserta un segmento EXIF con Make y Orientation.
     */
    private function jpegConExif(int $ancho, int $alto, int $orientacion): UploadedFile
    {
        $imagen = imagecreatetruecolor($ancho, $alto);
        ob_start();
        imagejpeg($imagen);
        $jpeg = (string) ob_get_clean();

        $make = "CAMARA-GPS\0";
        $tiff = "MM\x00\x2A".pack('N', 8).pack('n', 2)
            .pack('nnN', 0x010F, 2, strlen($make)).pack('N', 38)
            .pack('nnN', 0x0112, 3, 1).pack('n', $orientacion)."\0\0"
            .pack('N', 0).$make;
        $app1 = "\xFF\xE1".pack('n', 2 + 6 + strlen($tiff))."Exif\0\0".$tiff;

        $ruta = tempnam(sys_get_temp_dir(), 'mira').'.jpg';
        file_put_contents($ruta, substr($jpeg, 0, 2).$app1.substr($jpeg, 2));

        return new UploadedFile($ruta, 'foto.jpg', 'image/jpeg', null, true);
    }

    public function test_elimina_el_exif_endereza_la_foto_y_la_achica(): void
    {
        $foto = $this->jpegConExif(3000, 2000, 6); // 6 = girada 90°
        $this->assertSame('CAMARA-GPS', exif_read_data($foto->getRealPath())['Make']);

        $ruta = app(FotoService::class)->guardar($foto, 'MIR-2026-000001');

        $this->assertSame('incidencias/MIR-2026-000001.jpg', $ruta);
        $guardada = Storage::disk('local')->get($ruta);
        $this->assertStringNotContainsString("Exif\0\0", $guardada);
        $this->assertStringNotContainsString('CAMARA-GPS', $guardada);

        [$ancho, $alto] = getimagesizefromstring($guardada);
        $this->assertSame([1280, 1920], [$ancho, $alto]); // girada y con el lado mayor en 1920 px
    }

    public function test_convierte_un_png_a_jpeg(): void
    {
        $ruta = app(FotoService::class)->guardar(UploadedFile::fake()->image('foto.png', 400, 300), 'MIR-2026-000002');

        $this->assertSame('image/jpeg', getimagesizefromstring(Storage::disk('local')->get($ruta))['mime']);
    }

    public function test_rechaza_un_archivo_que_no_es_una_imagen(): void
    {
        $this->expectException(RuntimeException::class);

        app(FotoService::class)->guardar(UploadedFile::fake()->createWithContent('x.jpg', '<?php echo 1;'), 'X');
    }

    public function test_borrar_quita_el_archivo_y_tolera_null(): void
    {
        $servicio = app(FotoService::class);
        $ruta = $servicio->guardar(UploadedFile::fake()->image('f.jpg'), 'MIR-2026-000003');

        $servicio->borrar($ruta);
        $servicio->borrar(null);

        Storage::disk('local')->assertMissing($ruta);
    }
}
