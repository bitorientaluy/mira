<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            // Una cuenta por persona. NULL en funcionarios; no se usa para el login
            $table->char('cedula', 8)->nullable()->unique()->after('email');
        });
    }

    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropUnique(['cedula']);
            $table->dropColumn('cedula');
        });
    }
};
