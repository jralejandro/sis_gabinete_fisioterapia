<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // 1. Eliminamos las columnas innecesarias
            $table->dropColumn(['centro_procendencia', 'responsable_centro']);

            // 2. Agregamos el nuevo campo de marketing/contacto (nullable por los pacientes antiguos)
            $table->string('medio_contacto')->nullable()->after('celular_referencia');
        });
    }

    public function down(): void
    {
        Schema::table('patients', function (Blueprint $table) {
            // Revertir la eliminación
            $table->string('centro_procendencia')->nullable();
            $table->string('responsable_centro')->nullable();

            // Revertir la adición
            $table->dropColumn('medio_contacto');
        });
    }
};
