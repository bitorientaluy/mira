<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Tabla en lugar de ENUM: sirve en cualquier motor y se amplía sin ALTER TABLE
        Schema::create('rol', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 30)->unique();
        });

        Schema::create('usuario', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('email', 150)->unique();
            $table->string('password'); // BCrypt; Laravel espera este nombre de columna
            $table->string('telefono', 20)->nullable();
            $table->unsignedInteger('rol_id');
            $table->boolean('activo')->default(true); // baja lógica sin perder el historial
            $table->rememberToken();
            $table->dateTime('creado_en')->useCurrent();

            $table->foreign('rol_id')->references('id')->on('rol');
        });

        Schema::create('password_reset_tokens', function (Blueprint $table) {
            $table->string('email')->primary();
            $table->string('token');
            $table->timestamp('created_at')->nullable();
        });

        Schema::create('sessions', function (Blueprint $table) {
            $table->string('id')->primary();
            $table->foreignId('user_id')->nullable()->index();
            $table->string('ip_address', 45)->nullable();
            $table->text('user_agent')->nullable();
            $table->longText('payload');
            $table->integer('last_activity')->index();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sessions');
        Schema::dropIfExists('password_reset_tokens');
        Schema::dropIfExists('usuario');
        Schema::dropIfExists('rol');
    }
};
