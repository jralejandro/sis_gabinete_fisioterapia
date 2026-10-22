<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('discharge_records', function (Blueprint $table) {
            $table->id();

            $table->date('fecha_alta'); // FECHA
            $table->integer('responsable_alta'); // RESPONSABLE
            $table->text('evolucion_alta')->nullable(); // EVOLUCIÓN
            $table->text('observaciones_alta')->nullable(); // OBSERVACIONES
            $table->text('recomendaciones_alta')->nullable(); // RECOMENDACIONES
            $table->foreignId('evaluation_id')->constrained()->onDelete('cascade');


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('discharge_records');
    }
};
