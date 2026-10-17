<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('notificacion', function (Blueprint $table) {
            $table->id();
            $table->foreignId('usuario_id')->constrained('usuario'); // destinatario
            $table->foreignId('incidencia_id')->constrained('incidencia');
            $table->string('mensaje');
            $table->dateTime('enviada_en')->nullable(); // la completa la cola al mandar el mail
            $table->boolean('leida')->default(false);
            $table->dateTime('creado_en')->useCurrent();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('notificacion');
    }
};
