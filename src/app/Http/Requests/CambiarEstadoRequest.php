<?php

namespace App\Http\Requests;

use App\Data\CambioDeEstado;
use App\Enums\EstadoIncidencia;
use App\Enums\MotivoDescarte;
use App\Models\Area;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class CambiarEstadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // quién puede cambiar el estado lo decide IncidenciaPolicy desde la ruta (->can)
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
            // El formulario manda el área con cualquier botón: solo se tiene en cuenta al validar
            'area_id' => [
                'exclude_unless:estado,'.EstadoIncidencia::Validada->value,
                'nullable',
                'integer',
                Rule::exists('area', 'id'),
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
            'area_id.exists' => 'El área elegida no existe.',
            'area_id.integer' => 'El área elegida no es válida.',
            'comentario.max' => 'El comentario puede tener hasta 500 caracteres.',
        ];
    }

    /**
     * El pedido ya validado, listo para IncidenciaService::cambiarEstado().
     */
    public function cambio(): CambioDeEstado
    {
        $areaId = $this->validated('area_id');

        return new CambioDeEstado(
            estado: EstadoIncidencia::from($this->validated('estado')),
            comentario: $this->validated('comentario'),
            motivo: MotivoDescarte::tryFrom((string) $this->validated('motivo')),
            area: $areaId !== null ? Area::find($areaId) : null,
        );
    }
}
