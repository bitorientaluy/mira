<?php

use App\Enums\EstadoIncidencia;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('historial_incidencia', function (Blueprint $table) {
            $table->id();
            $table->foreignId('incidencia_id')->constrained('incidencia')->restrictOnDelete();
            $table->foreignId('usuario_id')->constrained('usuario'); // quién hizo el cambio
            $table->enum('estado_anterior', EstadoIncidencia::valores())->nullable(); // NULL en la creación
            $table->enum('estado_nuevo', EstadoIncidencia::valores());
            $table->text('comentario')->nullable();
            $table->dateTime('creado_en')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('historial_incidencia');
    }
};
