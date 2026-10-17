<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre', 'email_contacto'])]
class Area extends Model
{
    // Única área del prototipo; el modelo admite varias
    const LIMPIEZA_URBANA = 'Limpieza Urbana';

    protected $table = 'area';

    public $timestamps = false;

    public function funcionarios(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'usuario_area', 'area_id', 'usuario_id');
    }

    public function categorias(): HasMany
    {
        return $this->hasMany(Categoria::class);
    }
}
