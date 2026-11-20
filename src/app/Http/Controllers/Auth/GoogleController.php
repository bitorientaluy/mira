<?php

namespace App\Http\Controllers\Auth;

use App\Data\PerfilGoogle;
use App\Exceptions\CuentaGoogleRechazadaException;
use App\Http\Controllers\Controller;
use App\Services\Auth\CuentaGoogleService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse as RedireccionExterna;
use Throwable;

/**
 * Login y registro de ciudadanos con Google (T-411). Socialite arma la URL, valida el state
 * (defensa contra CSRF), canjea el code y pide el perfil; la decisión de qué usuario es
 * la toma CuentaGoogleService.
 */
class GoogleController extends Controller
{
    public function redirigir(): RedireccionExterna
    {
        abort_unless(CuentaGoogleService::habilitado(), 404);

        return Socialite::driver('google')->redirect();
    }

    public function callback(Request $request, CuentaGoogleService $cuentas): RedirectResponse
    {
        abort_unless(CuentaGoogleService::habilitado(), 404);

        try {
            $perfil = PerfilGoogle::desdeSocialite(Socialite::driver('google')->user());
        } catch (Throwable $e) {
            // La persona tocó "Cancelar", se perdió el state o Google no respondió
            Log::notice('Login con Google fallido', ['motivo' => $e->getMessage()]);

            return redirect()->route('login')->withErrors(['google' => 'No se pudo iniciar sesión con Google. Probá de nuevo.']);
        }

        try {
            $usuario = $cuentas->usuarioPara($perfil);
        } catch (CuentaGoogleRechazadaException $e) {
            return redirect()->route('login')->withErrors(['google' => $e->getMessage()]);
        }

        Auth::login($usuario);
        // Nuevo id de sesión para evitar la fijación de sesión
        $request->session()->regenerate();

        return redirect()->intended(route($usuario->rutaInicio()));
    }
}
