<?php

namespace App\Models;

use App\Enums\EstadoIncidencia;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['usuario_id', 'estado_anterior', 'estado_nuevo', 'comentario'])]
class HistorialIncidencia extends Model
{
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $table = 'historial_incidencia';

    protected function casts(): array
    {
        return [
            'estado_anterior' => EstadoIncidencia::class,
            'estado_nuevo' => EstadoIncidencia::class,
            'creado_en' => 'datetime',
        ];
    }

    public function incidencia(): BelongsTo
    {
        return $this->belongsTo(Incidencia::class);
    }

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(User::class, 'usuario_id');
    }
}
