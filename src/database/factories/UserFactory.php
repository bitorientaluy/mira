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
            'password' => static::$password ??= Hash::make('password'),
            'rol_id' => fn () => Rol::firstOrCreate(['nombre' => Rol::CIUDADANO])->id,
            'remember_token' => Str::random(10),
        ];
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
