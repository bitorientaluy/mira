<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

/**
 * Login con Google (T-411): la cuenta se identifica por google_id (el email puede cambiar, el id no)
 * y quien entra con Google no tiene contraseña en MIRA (NULL: no hay nada que adivinar).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->string('google_id', 64)->nullable()->unique()->after('email');
            $table->string('foto', 500)->nullable()->after('google_id'); // URL del avatar de Google
            $table->string('password')->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::table('usuario', function (Blueprint $table) {
            $table->dropUnique(['google_id']);
            $table->dropColumn(['google_id', 'foto']);
            $table->string('password')->nullable(false)->change();
        });
    }
};
