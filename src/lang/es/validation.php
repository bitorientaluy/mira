<?php

// Mensajes de las reglas que usa MIRA. Las que falten se muestran en inglés (APP_FALLBACK_LOCALE=en).
return [

    'between' => [
        'numeric' => 'El campo :attribute debe estar entre :min y :max.',
    ],
    'boolean' => 'El campo :attribute debe ser verdadero o falso.',
    'confirmed' => 'La confirmación de :attribute no coincide.',
    'email' => 'El campo :attribute debe ser un email válido.',
    'exists' => 'El valor elegido en :attribute no es válido.',
    'image' => 'El campo :attribute debe ser una imagen.',
    'in' => 'El valor elegido en :attribute no es válido.',
    'integer' => 'El campo :attribute debe ser un número entero.',
    'max' => [
        'file' => 'El archivo :attribute no puede superar los :max kilobytes.',
        'numeric' => 'El campo :attribute no puede ser mayor que :max.',
        'string' => 'El campo :attribute no puede tener más de :max caracteres.',
    ],
    'mimes' => 'El campo :attribute debe ser un archivo de tipo: :values.',
    'min' => [
        'numeric' => 'El campo :attribute debe ser al menos :min.',
        'string' => 'El campo :attribute debe tener al menos :min caracteres.',
    ],
    'numeric' => 'El campo :attribute debe ser un número.',
    'password' => [
        'letters' => 'La :attribute debe contener al menos una letra.',
        'mixed' => 'La :attribute debe contener al menos una mayúscula y una minúscula.',
        'numbers' => 'La :attribute debe contener al menos un número.',
        'symbols' => 'La :attribute debe contener al menos un símbolo.',
        'uncompromised' => 'La :attribute apareció en una filtración de datos. Elegí otra.',
    ],
    'required' => 'El campo :attribute es obligatorio.',
    'string' => 'El campo :attribute debe ser texto.',
    'unique' => 'Ya existe una cuenta con esa :attribute.',

    'attributes' => [
        'nombre' => 'nombre',
        'email' => 'dirección de email',
        'cedula' => 'cédula',
        'password' => 'contraseña',
        'telefono' => 'teléfono',
        'descripcion' => 'descripción',
        'categoria_id' => 'categoría',
        'lat' => 'latitud',
        'lng' => 'longitud',
        'foto' => 'foto',
    ],

];
