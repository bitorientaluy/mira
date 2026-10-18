<?php

namespace App\Services;

use App\Enums\EstadoIncidencia;
use App\Models\Categoria;
use App\Models\Incidencia;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/**
 * Toda la lógica de las incidencias pasa por acá (creación, cambios de estado e historial),
 * para no repetirla en los controladores.
 */
class IncidenciaService
{
    /**
     * Crea una incidencia en estado Pendiente y registra la primera fila del historial.
     *
     * @param  array{categoria_id: int|string, descripcion: string, lat: float|string, lng: float|string, direccion?: string|null}  $datos  datos ya validados
     */
    public function crear(array $datos, User $ciudadano): Incidencia
    {
        return DB::transaction(function () use ($datos, $ciudadano) {
            $categoria = Categoria::findOrFail($datos['categoria_id']);
            $punto = self::punto((float) $datos['lat'], (float) $datos['lng']);

            $incidencia = new Incidencia([
                'descripcion' => trim($datos['descripcion']),
                'direccion' => $datos['direccion'] ?? null,
            ]);
            $incidencia->ciudadano_id = $ciudadano->id;
            $incidencia->categoria_id = $categoria->id;
            $incidencia->area_id = $categoria->area_id; // el operador la confirma al validar
            $incidencia->barrio_id = $this->barrioDe($punto);
            $incidencia->estado = EstadoIncidencia::Pendiente;
            $incidencia->ubicacion = DB::raw("ST_GeomFromText('{$punto}', 4326, 'axis-order=long-lat')");
            $incidencia->save();

            // El código depende del id, que recién se conoce después del insert
            $incidencia->codigo = self::codigo($incidencia->id, (int) now()->format('Y'));
            $incidencia->save();

            $incidencia->historial()->create([
                'usuario_id' => $ciudadano->id,
                'estado_anterior' => null,
                'estado_nuevo' => EstadoIncidencia::Pendiente,
                'comentario' => 'Reporte creado por el ciudadano.',
            ]);

            return $incidencia->refresh();
        });
    }

    /**
     * Código de seguimiento visible para el ciudadano: MIR-AAAA-NNNNNN (PU-01).
     */
    public static function codigo(int $id, int $anio): string
    {
        return sprintf('MIR-%d-%06d', $anio, $id);
    }

    /**
     * Punto WKT en orden longitud latitud. Solo recibe números (float), así que el texto
     * no puede traer SQL; igual se usa con bindings donde la consulta lo permite.
     */
    public static function punto(float $lat, float $lng): string
    {
        return sprintf('POINT(%.7F %.7F)', $lng, $lat);
    }

    /**
     * Barrio que contiene el punto, o null si no hay límites cargados (T-306).
     */
    private function barrioDe(string $punto): ?int
    {
        return DB::table('barrio')
            ->whereRaw("ST_Contains(limite, ST_GeomFromText(?, 4326, 'axis-order=long-lat'))", [$punto])
            ->value('id');
    }
}
