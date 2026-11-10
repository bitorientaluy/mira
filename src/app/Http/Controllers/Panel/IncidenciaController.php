<?php

namespace App\Http\Controllers\Panel;

use App\Enums\EstadoIncidencia;
use App\Exceptions\TransicionInvalidaException;
use App\Http\Controllers\Controller;
use App\Http\Requests\CambiarEstadoRequest;
use App\Models\Incidencia;
use App\Services\FotoService;
use App\Services\IncidenciaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

/**
 * Panel interno: listado, detalle y cambio de estado de las incidencias (T-116).
 */
class IncidenciaController extends Controller
{
    private const POR_PAGINA = 15;

    public function index(Request $request): View
    {
        // Un valor que no es un estado se ignora y se muestran todas
        $filtro = EstadoIncidencia::tryFrom((string) $request->query('estado'));

        $incidencias = Incidencia::query()
            ->with('categoria')
            ->when($filtro, fn ($consulta) => $consulta->where('estado', $filtro))
            ->latest('creado_en')
            ->orderByDesc('id')
            ->paginate(self::POR_PAGINA)
            ->withQueryString();

        return view('panel.inicio', [
            'incidencias' => $incidencias,
            'filtro' => $filtro,
            'conteos' => $this->conteosPorEstado(),
        ]);
    }

    // El acceso lo controla IncidenciaPolicy desde la ruta
    public function show(Incidencia $incidencia): View
    {
        // Se vuelve a leer con el scope para tener lat y lng (la ubicación es binaria y está oculta)
        $incidencia = Incidencia::conCoordenadas()
            ->with(['categoria', 'area', 'ciudadano', 'historial.usuario'])
            ->findOrFail($incidencia->id);

        return view('panel.incidencia', [
            'incidencia' => $incidencia,
            // Descartar (con motivo) se agrega en T-201
            'opciones' => array_values(array_filter(
                $incidencia->estado->siguientes(),
                fn (EstadoIncidencia $e) => $e !== EstadoIncidencia::Descartado,
            )),
        ]);
    }

    public function cambiarEstado(CambiarEstadoRequest $request, Incidencia $incidencia, IncidenciaService $servicio): RedirectResponse
    {
        try {
            $actualizada = $servicio->cambiarEstado($incidencia, $request->estado(), $request->user(), $request->validated('comentario'));
        } catch (TransicionInvalidaException $e) {
            return back()->withErrors(['estado' => $e->getMessage()])->withInput();
        }

        return redirect()
            ->route('panel.incidencias.show', $actualizada->id)
            ->with('exito', "La incidencia pasó a «{$actualizada->estado->value}».");
    }

    public function foto(Incidencia $incidencia, FotoService $fotos): StreamedResponse
    {
        return $fotos->respuesta($incidencia->foto_ruta);
    }

    /**
     * Cantidad de incidencias por estado, con cero en los estados que no tienen ninguna.
     *
     * @return array<string, int>
     */
    private function conteosPorEstado(): array
    {
        // toBase(): sin el cast del modelo, para que el estado quede como texto y sirva de clave
        $porEstado = Incidencia::query()->toBase()
            ->selectRaw('estado, COUNT(*) AS total')
            ->groupBy('estado')
            ->pluck('total', 'estado');

        return collect(EstadoIncidencia::cases())
            ->mapWithKeys(fn (EstadoIncidencia $e) => [$e->value => (int) ($porEstado[$e->value] ?? 0)])
            ->all();
    }
}
