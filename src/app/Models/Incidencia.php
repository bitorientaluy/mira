<?php

namespace App\Models;

use App\Enums\EstadoIncidencia;
use App\Enums\MotivoDescarte;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

// Las columnas de estado, área, ciudadano y ubicación las asigna IncidenciaService, nunca un formulario.
// La ubicación se oculta: es binario de MySQL; se lee con el scope conCoordenadas().
#[Fillable(['descripcion', 'direccion'])]
#[Hidden(['ubicacion'])]
class Incidencia extends Model
{
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $table = 'incidencia';

    protected function casts(): array
    {
        return [
            'estado' => EstadoIncidencia::class,
            'motivo_descarte' => MotivoDescarte::class,
            'creado_en' => 'datetime',
            'asignado_en' => 'datetime',
            'resuelto_en' => 'datetime',
        ];
    }

    /**
     * Agrega las columnas lat y lng (sin traer el binario de la ubicación).
     */
    public function scopeConCoordenadas(Builder $consulta): Builder
    {
        return $consulta->select('incidencia.*')
            ->selectRaw("ST_Latitude(ubicacion) AS lat, ST_Longitude(ubicacion) AS lng");
    }

    public function ciudadano(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ciudadano_id');
    }

    public function ejecutor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'ejecutor_id');
    }

    public function categoria(): BelongsTo
    {
        return $this->belongsTo(Categoria::class);
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }

    public function historial(): HasMany
    {
        return $this->hasMany(HistorialIncidencia::class)->orderBy('creado_en')->orderBy('id');
    }
}
