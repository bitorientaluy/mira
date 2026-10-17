<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // N:M: los vecinos se suman a un reporte existente en lugar de duplicarlo
        Schema::create('apoyo_incidencia', function (Blueprint $table) {
            $table->foreignId('usuario_id')->constrained('usuario')->cascadeOnDelete();
            $table->foreignId('incidencia_id')->constrained('incidencia')->cascadeOnDelete();
            $table->dateTime('creado_en')->useCurrent();
            $table->primary(['usuario_id', 'incidencia_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('apoyo_incidencia');
    }
};
