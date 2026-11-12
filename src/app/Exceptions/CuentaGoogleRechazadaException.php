<?php

namespace App\Exceptions;

use RuntimeException;

/**
 * La cuenta de Google no puede usarse para entrar a MIRA. El mensaje se muestra tal cual en el login.
 */
class CuentaGoogleRechazadaException extends RuntimeException
{
    public static function emailSinVerificar(): self
    {
        return new self('Tu cuenta de Google no tiene el email verificado.');
    }

    public static function esFuncionario(): self
    {
        return new self('Los funcionarios ingresan con su email y contraseña.');
    }

    public static function inactiva(): self
    {
        return new self('Tu cuenta está desactivada.');
    }

    public static function vinculadaAOtraCuenta(): self
    {
        return new self('Ese email ya está vinculado a otra cuenta de Google.');
    }
}
