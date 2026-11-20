<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\CompletarCedulaRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

/**
 * Primer ingreso con Google: el ciudadano completa la cédula antes de reportar (T-411).
 */
class CompletarCedulaController extends Controller
{
    public function create(Request $request): View|RedirectResponse
    {
        if ($request->user()->cedula !== null) {
            return redirect()->route('ciudadano.inicio');
        }

        return view('auth.completar-cedula');
    }

    public function store(CompletarCedulaRequest $request): RedirectResponse
    {
        $usuario = $request->user();
        if ($usuario->cedula === null) {
            $usuario->cedula = $request->validated('cedula');
            $usuario->save();
        }

        return redirect()->intended(route('ciudadano.inicio'));
    }
}
