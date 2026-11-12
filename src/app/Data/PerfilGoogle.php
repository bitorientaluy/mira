<?php

namespace App\Data;

use Laravel\Socialite\Contracts\User as UsuarioSocialite;

/**
 * Los datos de la cuenta de Google que usa MIRA. Separa el servicio de Socialite:
 * CuentaGoogleService no depende del paquete y se prueba sin simular OAuth.
 */
final readonly class PerfilGoogle
{
    public function __construct(
        public string $id,
        public string $email,
        public string $nombre,
        public bool $emailVerificado,
    ) {}

    public static function desdeSocialite(UsuarioSocialite $usuario): self
    {
        // OpenID Connect manda "email_verified"; la API vieja de Google, "verified_email"
        $crudo = method_exists($usuario, 'getRaw') ? $usuario->getRaw() : [];
        $verificado = $crudo['email_verified'] ?? $crudo['verified_email'] ?? false;

        return new self(
            id: (string) $usuario->getId(),
            email: mb_strtolower(trim((string) $usuario->getEmail())),
            nombre: trim((string) ($usuario->getName() ?: $usuario->getEmail())),
            emailVerificado: filter_var($verificado, FILTER_VALIDATE_BOOL),
        );
    }
}
