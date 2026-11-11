<?php

namespace App\Services;

use App\Data\CambioDeEstado;
use App\Enums\EstadoIncidencia;
use App\Exceptions\TransicionInvalidaException;
use App\Models\Categoria;
use App\Models\Incidencia;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Throwable;

/**
 * Toda la lógica de las incidencias pasa por acá (creación, cambios de estado e historial),
 * para no repetirla en los controladores.
 */
class IncidenciaService
{
    public function __construct(private FotoService $fotos) {}

    /**
     * Crea una incidencia en estado Pendiente, guarda la foto (si hay) y registra la primera fila del historial.
     *
     * @param  array{categoria_id: int|string, descripcion: string, lat: float|string, lng: float|string, direccion?: string|null}  $datos  datos ya validados
     */
    public function crear(array $datos, User $ciudadano, ?UploadedFile $foto = null): Incidencia
    {
        $rutaFoto = null;

        try {
            return DB::transaction(function () use ($datos, $ciudadano, $foto, &$rutaFoto) {
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
                if ($foto !== null) {
                    $rutaFoto = $this->fotos->guardar($foto, $incidencia->codigo);
                    $incidencia->foto_ruta = $rutaFoto;
                }
                $incidencia->save();

                $incidencia->historial()->create([
                    'usuario_id' => $ciudadano->id,
                    'estado_anterior' => null,
                    'estado_nuevo' => EstadoIncidencia::Pendiente,
                    'comentario' => 'Reporte creado por el ciudadano.',
                ]);

                return $incidencia->refresh();
            });
        } catch (Throwable $e) {
            // Si la base deshizo el alta, que no quede una foto huérfana en el disco
            $this->fotos->borrar($rutaFoto);
            throw $e;
        }
    }

    /**
     * Cambia el estado de una incidencia y registra el cambio en el historial (PU-06, CP-03).
     * Bloquea la fila para que dos funcionarios no cambien el estado a la vez a partir del mismo estado.
     * Al validar, el operador confirma o corrige el área (por defecto, la de la categoría).
     *
     * @throws TransicionInvalidaException si el flujo no permite el cambio, o el motivo o el área no corresponden
     */
    public function cambiarEstado(Incidencia $incidencia, CambioDeEstado $cambio, User $usuario): Incidencia
    {
        return DB::transaction(function () use ($incidencia, $cambio, $usuario) {
            $actual = Incidencia::with('area')->whereKey($incidencia->id)->lockForUpdate()->firstOrFail();
            $anterior = $actual->estado;
            $this->validarCambio($anterior, $cambio);

            $notaArea = null;
            if ($cambio->area !== null && $cambio->area->id !== $actual->area_id) {
                $notaArea = "Área cambiada de «{$actual->area->nombre}» a «{$cambio->area->nombre}».";
                $actual->area_id = $cambio->area->id;
            }

            $actual->estado = $cambio->estado;
            $actual->motivo_descarte = $cambio->motivo;
            // Al reabrir (Resuelto → Pendiente) se borra la fecha de resolución
            $actual->resuelto_en = $cambio->estado === EstadoIncidencia::Resuelto ? now() : null;
            $actual->save();

            $actual->historial()->create([
                'usuario_id' => $usuario->id,
                'estado_anterior' => $anterior,
                'estado_nuevo' => $cambio->estado,
                'comentario' => self::unirTextos($notaArea, $cambio->comentario),
            ]);

            return $actual->refresh();
        });
    }

    /**
     * Reglas del flujo, con el estado leído de la base (no el de la página abierta).
     */
    private function validarCambio(EstadoIncidencia $anterior, CambioDeEstado $cambio): void
    {
        $descarta = $cambio->estado === EstadoIncidencia::Descartado;

        if (! $anterior->puedePasarA($cambio->estado)) {
            throw TransicionInvalidaException::entre($anterior, $cambio->estado);
        }
        if ($descarta && $cambio->motivo === null) {
            throw TransicionInvalidaException::faltaMotivo();
        }
        if (! $descarta && $cambio->motivo !== null) {
            throw TransicionInvalidaException::motivoSobrante();
        }
        if ($cambio->area !== null && $cambio->estado !== EstadoIncidencia::Validada) {
            throw TransicionInvalidaException::areaSobrante();
        }
    }

    /**
     * Junta la nota automática y el comentario del funcionario; null si no queda texto.
     */
    private static function unirTextos(?string ...$partes): ?string
    {
        $texto = implode(' ', array_filter(array_map(fn ($p) => trim((string) $p), $partes), fn ($p) => $p !== ''));

        return $texto === '' ? null : $texto;
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
