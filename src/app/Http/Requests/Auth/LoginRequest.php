<?php

namespace App\Http\Requests\Auth;

use Illuminate\Auth\Events\Lockout;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class LoginRequest extends FormRequest
{
    private const MAX_INTENTOS = 5;

    public function authorize(): bool
    {
        return true;
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'email' => ['required', 'string', 'email'],
            'password' => ['required', 'string'],
        ];
    }

    /**
     * Inicia sesión solo si las credenciales son correctas y el usuario está activo.
     * Después de MAX_INTENTOS fallidos seguidos (por email e IP) bloquea por un minuto.
     */
    public function authenticate(): void
    {
        $this->asegurarQueNoEstaBloqueado();

        $credenciales = [
            'email' => mb_strtolower(trim($this->string('email'))),
            'password' => $this->string('password')->value(),
            'activo' => true,
        ];

        if (! Auth::attempt($credenciales, $this->boolean('recordarme'))) {
            RateLimiter::hit($this->claveDeBloqueo());

            throw ValidationException::withMessages(['email' => __('auth.failed')]);
        }

        RateLimiter::clear($this->claveDeBloqueo());
    }

    private function asegurarQueNoEstaBloqueado(): void
    {
        if (! RateLimiter::tooManyAttempts($this->claveDeBloqueo(), self::MAX_INTENTOS)) {
            return;
        }

        event(new Lockout($this));

        throw ValidationException::withMessages([
            'email' => __('auth.throttle', ['seconds' => RateLimiter::availableIn($this->claveDeBloqueo())]),
        ]);
    }

    private function claveDeBloqueo(): string
    {
        return Str::transliterate(Str::lower($this->string('email')).'|'.$this->ip());
    }
}
