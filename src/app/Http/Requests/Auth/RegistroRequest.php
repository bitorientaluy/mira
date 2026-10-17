<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class RegistroRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['email' => mb_strtolower(trim((string) $this->input('email')))]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'email' => ['required', 'string', 'email', 'max:150', 'unique:usuario,email'],
            // Sin validar el dígito verificador por ahora; max:8 por el tamaño de la columna
            'cedula' => ['required', 'string', 'max:8', 'unique:usuario,cedula'],
            'password' => ['required', 'confirmed', Password::min(8)],
            'telefono' => ['nullable', 'string', 'max:20'],
        ];
    }
}
