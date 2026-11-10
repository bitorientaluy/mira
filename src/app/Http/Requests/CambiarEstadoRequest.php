<?php

namespace App\Http\Requests;

use App\Enums\EstadoIncidencia;
use App\Enums\MotivoDescarte;
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
     * que lo hace con la fila bloqueada. El motivo es obligatorio al descartar y no va en otro caso.
     *
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'estado' => ['required', Rule::enum(EstadoIncidencia::class)],
            'motivo' => [
                'required_if:estado,'.EstadoIncidencia::Descartado->value,
                'prohibited_unless:estado,'.EstadoIncidencia::Descartado->value,
                'nullable',
                Rule::enum(MotivoDescarte::class),
            ],
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
            'motivo.required_if' => 'Elegí el motivo del descarte.',
            'motivo.prohibited_unless' => 'El motivo solo corresponde al descartar.',
            'motivo.enum' => 'El motivo elegido no es válido.',
            'comentario.max' => 'El comentario puede tener hasta 500 caracteres.',
        ];
    }

    public function estado(): EstadoIncidencia
    {
        return EstadoIncidencia::from($this->validated('estado'));
    }

    public function motivo(): ?MotivoDescarte
    {
        return MotivoDescarte::tryFrom((string) $this->validated('motivo'));
    }
}
