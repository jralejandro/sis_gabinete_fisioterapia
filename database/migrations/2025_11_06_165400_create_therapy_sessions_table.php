<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('therapy_sessions', function (Blueprint $table) {
            $table->id();

            // Relación con evaluación
            $table->foreignId('evaluation_id')->constrained('evaluations')->onDelete('cascade');

            // Datos de la sesión
            $table->integer('responsable_sesion')->nullable();
            $table->date('fecha')->required();
            $table->text('evolucion')->nullable();
            $table->text('observaciones')->required();
            $table->text('que_se_trabajo')->required();
            $table->decimal('costo_sesion', 10, 2)->nullable();

            // Control interno
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('therapy_sessions');
    }
};
