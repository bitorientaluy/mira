<?php

namespace App\Http\Requests;

use App\Enums\EstadoIncidencia;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CambiarEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el acceso por rol lo controla el middleware de la ruta
    }

    /**
     * Si la transición está permitida desde el estado actual lo decide IncidenciaService,
     * que lo hace con la fila bloqueada. Descartar (con motivo) llega con T-201.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'estado' => ['required', Rule::enum(EstadoIncidencia::class)->except([EstadoIncidencia::Descartado])],
            'comentario' => ['nullable', 'string', 'max:500'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'estado.required' => 'Elegí el estado nuevo.',
            'estado.enum' => 'El estado elegido no es válido.',
            'comentario.max' => 'El comentario puede tener hasta 500 caracteres.',
        ];
    }

    public function estado(): EstadoIncidencia
    {
        return EstadoIncidencia::from($this->validated('estado'));
    }
}
