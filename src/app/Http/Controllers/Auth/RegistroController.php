<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\RegistroRequest;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class RegistroController extends Controller
{
    public function create(): View
    {
        return view('auth.registro');
    }

    /**
     * El registro público siempre crea ciudadanos: el rol se fija acá, nunca desde el formulario.
     */
    public function store(RegistroRequest $request): RedirectResponse
    {
        $usuario = new User($request->validated());
        $usuario->rol_id = Rol::where('nombre', Rol::CIUDADANO)->value('id');
        $usuario->save();

        Auth::login($usuario);
        $request->session()->regenerate();

        return redirect()->route($usuario->rutaInicio());
    }
}
