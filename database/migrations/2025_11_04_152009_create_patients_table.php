<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('patients', function (Blueprint $table) {
            $table->id();

            $table->string('ci');
            $table->string('complemento', 3)->nullable();

            // Unique compuesto
            $table->unique(['ci', 'complemento']);

            $table->string('nombres');
            $table->string('apellido_paterno')->nullable();
            $table->string('apellido_materno')->nullable();
            $table->string('sexo');
            $table->string('ocupacion')->nullable();
            $table->date('fecha_nacimiento');
            $table->string('domicilio')->nullable();
            $table->string('estado_civil')->nullable();
            $table->string('celular')->nullable();
            $table->string('persona_referencia')->nullable();
            $table->string('celular_referencia')->nullable();
            $table->string('centro_procendencia')->nullable();
            $table->string('responsable_centro')->nullable();
            $table->tinyInteger('estado')->default(1); // 1: activo, 0: inactivo

            $table->unsignedBigInteger('user_id')->nullable();
            $table->foreign('user_id')->references('id')->on('users')->nullOnDelete();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('patients');
    }
};
