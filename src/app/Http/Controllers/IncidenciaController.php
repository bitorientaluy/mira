<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidenciaRequest;
use App\Models\Categoria;
use App\Models\Incidencia;
use App\Services\IncidenciaService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class IncidenciaController extends Controller
{
    public function create(): View
    {
        return view('ciudadano.reportar', [
            'categorias' => Categoria::where('activa', true)->orderBy('id')->get(),
            'mapa' => config('mira.mapa'),
            'limites' => config('mira.limites'),
        ]);
    }

    public function store(StoreIncidenciaRequest $request, IncidenciaService $servicio): RedirectResponse
    {
        $incidencia = $servicio->crear(
            $request->safe()->except('foto'),
            $request->user(),
            $request->file('foto'),
        );

        return redirect()->route('incidencias.confirmacion', $incidencia);
    }

    public function confirmacion(Request $request, Incidencia $incidencia): View
    {
        // Solo el autor ve la confirmación (las Policies llegan en T-201)
        abort_unless($incidencia->ciudadano_id === $request->user()->id, 403);

        return view('ciudadano.confirmacion', ['incidencia' => $incidencia->load('categoria')]);
    }
}
