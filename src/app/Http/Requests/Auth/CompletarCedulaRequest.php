<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Http\FormRequest;

/**
 * Cédula del ciudadano que entró con Google por primera vez (Google no la da).
 */
class CompletarCedulaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el acceso lo controlan los middleware de la ruta (auth y rol de ciudadano)
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['cedula' => trim((string) $this->input('cedula'))]);
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return ['cedula' => RegistroRequest::reglasCedula()];
    }
}
