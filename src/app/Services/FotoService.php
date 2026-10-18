<?php

namespace App\Services;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

/**
 * Guarda las fotos de las incidencias sin metadatos (Programación 4.i, CP-12 y CP-14).
 *
 * La imagen se re-codifica con GD: el archivo nuevo solo tiene los píxeles, así que se pierden
 * el EXIF (incluida la ubicación GPS) y cualquier contenido escondido en el original.
 * Se guarda en el disco "local" (storage/app/private), nunca en public/.
 */
class FotoService
{
    private const DISCO = 'local';
    private const LADO_MAXIMO = 1920;  // px; las fotos de celular suelen ser mucho más grandes
    private const CALIDAD_JPEG = 85;

    /**
     * Devuelve la ruta relativa dentro del disco (ej. incidencias/MIR-2026-000123.jpg).
     */
    public function guardar(UploadedFile $foto, string $nombre): string
    {
        $imagen = @imagecreatefromstring((string) file_get_contents($foto->getRealPath()));
        if ($imagen === false) {
            throw new RuntimeException('La foto no es una imagen válida.');
        }

        $imagen = $this->enderezar($imagen, $foto);
        $imagen = $this->achicar($imagen);
        $imagen = $this->sobreFondoBlanco($imagen); // un PNG con transparencia no queda negro en JPEG

        ob_start();
        imagejpeg($imagen, null, self::CALIDAD_JPEG);
        $jpeg = (string) ob_get_clean();
        imagedestroy($imagen);

        $ruta = "incidencias/{$nombre}.jpg";
        Storage::disk(self::DISCO)->put($ruta, $jpeg);

        return $ruta;
    }

    public function borrar(?string $ruta): void
    {
        if ($ruta !== null) {
            Storage::disk(self::DISCO)->delete($ruta);
        }
    }

    /**
     * Aplica la orientación EXIF antes de descartarla; si no, las fotos de celular quedan giradas.
     */
    private function enderezar(\GdImage $imagen, UploadedFile $foto): \GdImage
    {
        if (! in_array($foto->getMimeType(), ['image/jpeg', 'image/jpg'], true) || ! function_exists('exif_read_data')) {
            return $imagen;
        }

        $exif = @exif_read_data($foto->getRealPath());
        $angulo = match ((int) ($exif['Orientation'] ?? 1)) {
            3 => 180,
            6 => -90,
            8 => 90,
            default => 0,
        };

        return $angulo === 0 ? $imagen : imagerotate($imagen, $angulo, 0);
    }

    private function achicar(\GdImage $imagen): \GdImage
    {
        $ancho = imagesx($imagen);
        $alto = imagesy($imagen);
        $escala = self::LADO_MAXIMO / max($ancho, $alto);
        if ($escala >= 1) {
            return $imagen;
        }

        return imagescale($imagen, (int) round($ancho * $escala), (int) round($alto * $escala));
    }

    private function sobreFondoBlanco(\GdImage $imagen): \GdImage
    {
        $lienzo = imagecreatetruecolor(imagesx($imagen), imagesy($imagen));
        imagefill($lienzo, 0, 0, imagecolorallocate($lienzo, 255, 255, 255));
        imagecopy($lienzo, $imagen, 0, 0, 0, 0, imagesx($imagen), imagesy($imagen));
        imagedestroy($imagen);

        return $lienzo;
    }
}
