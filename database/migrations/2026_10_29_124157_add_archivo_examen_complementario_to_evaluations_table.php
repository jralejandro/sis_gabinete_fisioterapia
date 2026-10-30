<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Se añade nullable() porque los registros antiguos no tendrán este archivo
            // Se coloca 'after' para mantener un orden lógico en la base de datos
            $table->string('archivo_examen_complementario')->nullable()->after('examenes_complementarios');
        });
    }

    public function down(): void
    {
        Schema::table('evaluations', function (Blueprint $table) {
            // Permite revertir la migración si hay algún error en el futuro
            $table->dropColumn('archivo_examen_complementario');
        });
    }
};
