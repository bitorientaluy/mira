<?php

namespace Database\Factories;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

/**
 * @extends Factory<User>
 */
class UserFactory extends Factory
{
    /**
     * The current password being used by the factory.
     */
    protected static ?string $password;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'nombre' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            // Todo usuario registrado con el formulario tiene cédula (8 dígitos, sin puntos ni guion)
            'cedula' => fake()->unique()->numerify('########'),
            'password' => static::$password ??= Hash::make('password'),
            'rol_id' => fn () => Rol::firstOrCreate(['nombre' => Rol::CIUDADANO])->id,
            'remember_token' => Str::random(10),
        ];
    }

    /**
     * Ciudadano que entró con Google y todavía no cargó la cédula (T-411).
     */
    public function sinCedula(): static
    {
        return $this->state(fn (array $attributes) => ['cedula' => null]);
    }

    /**
     * Usuario con el rol indicado (ciudadano, operador, jefe_area, ejecutor o admin).
     */
    public function conRol(string $rol): static
    {
        return $this->state(fn (array $attributes) => [
            'rol_id' => Rol::firstOrCreate(['nombre' => $rol])->id,
        ]);
    }
}
