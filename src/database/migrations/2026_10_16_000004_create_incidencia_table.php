<?php

use App\Enums\EstadoIncidencia;
use App\Enums\MotivoDescarte;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('incidencia', function (Blueprint $table) {
            $table->id();
            // MIR-AAAA-NNNNNN; se completa en la misma transacción del alta, porque depende del id
            $table->string('codigo', 20)->nullable()->unique();
            $table->foreignId('ciudadano_id')->constrained('usuario');
            $table->unsignedInteger('categoria_id')->index();
            $table->unsignedInteger('area_id')->index(); // se copia de la categoría al crear
            $table->foreignId('ejecutor_id')->nullable()->constrained('usuario');
            $table->unsignedInteger('barrio_id')->nullable(); // se calcula al guardar con ST_Contains
            $table->foreignId('duplicada_de_id')->nullable()->constrained('incidencia');
            $table->text('descripcion');
            $table->geometry('ubicacion', subtype: 'point', srid: 4326); // obligatoria (CP-02)
            $table->string('direccion', 200)->nullable(); // solo informativa
            $table->string('foto_ruta')->nullable(); // fuera de public/ y sin EXIF
            $table->enum('estado', EstadoIncidencia::valores())->default(EstadoIncidencia::Pendiente->value)->index();
            $table->enum('motivo_descarte', MotivoDescarte::valores())->nullable();
            $table->dateTime('creado_en')->useCurrent()->index();
            $table->dateTime('asignado_en')->nullable();
            $table->dateTime('resuelto_en')->nullable();

            $table->spatialIndex('ubicacion');
            $table->foreign('categoria_id')->references('id')->on('categoria');
            $table->foreign('area_id')->references('id')->on('area');
            $table->foreign('barrio_id')->references('id')->on('barrio');
        });

        // El motivo es obligatorio si y solo si la incidencia está descartada
        DB::statement("ALTER TABLE incidencia ADD CONSTRAINT chk_incidencia_motivo
            CHECK ((estado = 'Descartado') = (motivo_descarte IS NOT NULL))");
    }

    public function down(): void
    {
        Schema::dropIfExists('incidencia');
    }
};
