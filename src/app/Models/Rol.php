<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

#[Fillable(['nombre'])]
class Rol extends Model
{
    const CIUDADANO = 'ciudadano';
    const OPERADOR = 'operador';
    const JEFE_AREA = 'jefe_area';
    const EJECUTOR = 'ejecutor';
    const ADMIN = 'admin';

    protected $table = 'rol';

    public $timestamps = false;

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class);
    }
}
