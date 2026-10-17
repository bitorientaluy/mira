<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

#[Fillable(['nombre', 'area_id', 'radio_duplicado_m', 'activa'])]
class Categoria extends Model
{
    protected $table = 'categoria';

    public $timestamps = false;

    protected function casts(): array
    {
        return [
            'activa' => 'boolean',
        ];
    }

    public function area(): BelongsTo
    {
        return $this->belongsTo(Area::class);
    }
}
