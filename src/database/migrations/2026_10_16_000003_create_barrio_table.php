<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('barrio', function (Blueprint $table) {
            $table->increments('id');
            $table->string('nombre', 80)->unique();
            $table->geometry('limite', subtype: 'polygon', srid: 4326); // contorno para contar con ST_Contains
            $table->spatialIndex('limite');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('barrio');
    }
};
