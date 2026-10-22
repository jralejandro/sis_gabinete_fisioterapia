<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('gyneco_obstetric_histories', function (Blueprint $table) {
            $table->id();

            // Relación uno a uno con Evaluation
            $table->foreignId('evaluation_id')
                ->constrained('evaluations')
                ->onDelete('cascade');

            // Campos gineco-obstétricos
            $table->unsignedTinyInteger('gestaciones')->nullable();
            $table->unsignedTinyInteger('partos')->nullable();
            $table->unsignedTinyInteger('abortos')->nullable();
            $table->unsignedTinyInteger('cesareas')->nullable();

            $table->date('fecha_ultima_menstruacion')->nullable();
            $table->date('fecha_ultimo_parto')->nullable();
            $table->date('fecha_ultima_cesarea')->nullable();
            $table->date('fecha_ultimo_aborto')->nullable();

            $table->unsignedTinyInteger('edad_menarca')->nullable();
            $table->string('patron_catamenial', 100)->nullable();
            $table->string('ciclo_menstrual', 100)->nullable();


            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('gyneco_obstetric_histories');
    }
};
