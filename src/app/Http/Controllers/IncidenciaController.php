<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreIncidenciaRequest;
use App\Models\Categoria;
use App\Models\Incidencia;
use App\Services\IncidenciaService;
use Illuminate\Http\RedirectResponse;
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

    // El acceso (solo el autor) lo controla IncidenciaPolicy desde la ruta
    public function confirmacion(Incidencia $incidencia): View
    {
        return view('ciudadano.confirmacion', ['incidencia' => $incidencia->load('categoria')]);
    }
}
