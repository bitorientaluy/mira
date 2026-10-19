<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Validator;

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
     * Además del tipo de archivo, la foto tiene que poder abrirse: así un archivo disfrazado o una imagen
     * corrupta dan un error de validación y no un 500 al procesarla (CP-14).
     *
     * @return array<int, \Closure>
     */
    public function after(): array
    {
        return [function (Validator $validador) {
            $foto = $this->file('foto');
            if ($foto === null || $validador->errors()->has('foto')) {
                return;
            }
            $imagen = @imagecreatefromstring((string) file_get_contents($foto->getRealPath()));
            if ($imagen === false) {
                $validador->errors()->add('foto', 'La foto no es una imagen válida.');
            }
        }];
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
