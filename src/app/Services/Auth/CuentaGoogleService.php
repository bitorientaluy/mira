<?php

namespace App\Services\Auth;

use App\Data\PerfilGoogle;
use App\Exceptions\CuentaGoogleRechazadaException;
use App\Models\Rol;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Decide qué usuario de MIRA corresponde a una cuenta de Google (T-411). Registro y login son lo mismo:
 * si la cuenta ya está, entra; si no, se crea un ciudadano.
 *
 * Decisiones del equipo:
 * - Solo ciudadanos: los funcionarios siguen con email y contraseña.
 * - Se identifica por google_id; el email solo se usa para vincular una cuenta de ciudadano
 *   que ya existía, y únicamente si Google garantiza que está verificado.
 * - La cédula no la da Google: se pide en el primer ingreso.
 * - No se guarda la foto de Google: MIRA no la usa (minimización de datos personales).
 */
class CuentaGoogleService
{
    /**
     * Sin GOOGLE_CLIENT_ID en el .env, el botón no aparece y las rutas dan 404.
     */
    public static function habilitado(): bool
    {
        return filled(config('services.google.client_id')) && filled(config('services.google.client_secret'));
    }

    /**
     * @throws CuentaGoogleRechazadaException
     */
    public function usuarioPara(PerfilGoogle $perfil): User
    {
        if (! $perfil->emailVerificado) {
            throw CuentaGoogleRechazadaException::emailSinVerificar();
        }

        return DB::transaction(function () use ($perfil) {
            $usuario = User::where('google_id', $perfil->id)->first()
                ?? $this->vincularPorEmail($perfil)
                ?? $this->crearCiudadano($perfil);

            if (! $usuario->esRol(Rol::CIUDADANO)) {
                throw CuentaGoogleRechazadaException::esFuncionario();
            }
            if (! $usuario->activo) {
                throw CuentaGoogleRechazadaException::inactiva();
            }

            // El nombre no se pisa en los ingresos siguientes, por si el ciudadano lo corrigió
            $usuario->save();

            return $usuario;
        });
    }

    /**
     * Ciudadano que se había registrado con email y contraseña y ahora entra con Google.
     */
    private function vincularPorEmail(PerfilGoogle $perfil): ?User
    {
        $usuario = User::where('email', $perfil->email)->first();
        if ($usuario === null) {
            return null;
        }
        if (! $usuario->esRol(Rol::CIUDADANO)) {
            throw CuentaGoogleRechazadaException::esFuncionario();
        }
        if ($usuario->google_id !== null) {
            throw CuentaGoogleRechazadaException::vinculadaAOtraCuenta();
        }

        $usuario->google_id = $perfil->id;

        return $usuario;
    }

    private function crearCiudadano(PerfilGoogle $perfil): User
    {
        $usuario = new User([
            'nombre' => Str::limit($perfil->nombre, 100, ''),
            'email' => $perfil->email,
        ]);
        // Fuera de Fillable a propósito: nunca llegan desde un formulario
        $usuario->google_id = $perfil->id;
        $usuario->rol_id = Rol::where('nombre', Rol::CIUDADANO)->value('id');
        $usuario->password = null;
        $usuario->activo = true; // el modelo sin guardar no trae el valor por defecto de la base

        return $usuario;
    }
}
