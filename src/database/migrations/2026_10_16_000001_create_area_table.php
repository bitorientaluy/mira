<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('area', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 80)->unique();
            $table->string('email_contacto', 150)->nullable();
        });

        // N:M: un jefe o ejecutor puede estar en varias áreas y un área tiene varios funcionarios
        Schema::create('usuario_area', function (Blueprint $table) {
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->unsignedInteger('area_id');
            $table->primary(['usuario_id', 'area_id']);

            $table->foreign('area_id')->references('id')->on('area')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('usuario_area');
        Schema::dropIfExists('area');
    }
};
