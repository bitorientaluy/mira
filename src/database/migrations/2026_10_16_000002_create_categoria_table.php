<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('categoria', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 60)->unique();
            $table->unsignedInteger('area_id'); // área sugerida; el operador la confirma o cambia
            $table->unsignedSmallInteger('radio_duplicado_m')->default(50); // metros para sugerir "ya existe un reporte cerca"
            $table->boolean('activa')->default(true);

            $table->foreign('area_id')->references('id')->on('area');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('categoria');
    }
};
