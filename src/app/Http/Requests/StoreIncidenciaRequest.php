<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreIncidenciaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true; // el acceso por rol lo controla el middleware de la ruta
    }

    /**
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        $l = config('mira.limites');

        return [
            'categoria_id' => ['required', 'integer', Rule::exists('categoria', 'id')->where('activa', true)],
            'descripcion' => ['required', 'string', 'max:1000'],
            'lat' => ['required', 'numeric', "between:{$l['lat_min']},{$l['lat_max']}"],
            'lng' => ['required', 'numeric', "between:{$l['lng_min']},{$l['lng_max']}"],
            // Valida el contenido real del archivo, no solo la extensión (CP-14)
            'foto' => ['nullable', 'file', 'image', 'mimes:jpg,jpeg,png', 'max:5120'],
        ];
    }

    /**
     * @return array<string, string>
     */
    public function messages(): array
    {
        $fuera = 'La ubicación tiene que estar dentro de Montevideo.';
        $sinPunto = 'Marcá en el mapa dónde está la incidencia.';

        return [
            'lat.required' => $sinPunto,
            'lng.required' => $sinPunto,
            'lat.between' => $fuera,
            'lng.between' => $fuera,
            'categoria_id.required' => 'Elegí el tipo de problema.',
            'categoria_id.exists' => 'La categoría elegida no es válida.',
            'foto.max' => 'La foto no puede superar los 5 MB.',
        ];
    }
}
