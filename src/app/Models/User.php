<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

// rol_id queda fuera de Fillable: nadie puede asignarse un rol desde un formulario.
// La cédula se oculta al serializar (JSON, API) por ser un dato personal.
#[Fillable(['nombre', 'email', 'cedula', 'password', 'telefono'])]
#[Hidden(['cedula', 'password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    const CREATED_AT = 'creado_en';
    const UPDATED_AT = null;

    protected $table = 'usuario';

    protected function casts(): array
    {
        return [
            'password' => 'hashed',
            'activo' => 'boolean',
            'creado_en' => 'datetime',
        ];
    }

    public function rol(): BelongsTo
    {
        return $this->belongsTo(Rol::class);
    }

    public function areas(): BelongsToMany
    {
        return $this->belongsToMany(Area::class, 'usuario_area', 'usuario_id', 'area_id');
    }

    public function esRol(string $nombre): bool
    {
        return $this->rol?->nombre === $nombre;
    }

    /**
     * Nombre de la ruta a la que va el usuario al iniciar sesión.
     */
    public function rutaInicio(): string
    {
        return $this->esRol(Rol::CIUDADANO) ? 'ciudadano.inicio' : 'panel.inicio';
    }
}
