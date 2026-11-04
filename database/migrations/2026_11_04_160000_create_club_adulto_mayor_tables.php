<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Módulo "Club del Adulto Mayor".
 *
 * - club_grupos:   Catálogo de grupos/horarios (CRUD, con los 5 grupos iniciales).
 * - club_miembros: Cada INSCRIPCIÓN de un paciente al club (una persona puede tener varias
 *                  inscripciones a lo largo del tiempo, pero solo una ACTIVA).
 * - club_tests:    Tests/evaluaciones manuales del participante (descripción + archivo privado).
 * - club_pagos:    Mensualidades pagadas (un registro por mes; nunca se cobra dos veces el mismo mes).
 */
return new class extends Migration
{
    public function up(): void
    {
        Schema::create('club_grupos', function (Blueprint $table) {
            $table->id();
            $table->string('nombre', 100);
            $table->string('horario', 50);
            $table->string('dias', 100)->nullable();
            $table->unsignedSmallInteger('cupo_maximo')->nullable();
            $table->unsignedSmallInteger('orden')->default(0);
            $table->boolean('estado')->default(true);
            $table->timestamps();
        });

        Schema::create('club_miembros', function (Blueprint $table) {
            $table->id();
            $table->foreignId('patient_id')->constrained('patients')->cascadeOnDelete();
            $table->foreignId('club_grupo_id')->constrained('club_grupos')->restrictOnDelete();
            $table->date('fecha_inscripcion');
            $table->unsignedInteger('pago_matricula')->default(50);
            $table->string('estado', 20)->default('ACTIVO')->index();
            $table->date('fecha_baja')->nullable();
            $table->string('motivo_baja')->nullable();
            $table->text('observaciones')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->index(['patient_id', 'estado']);
        });

        Schema::create('club_tests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_miembro_id')->constrained('club_miembros')->cascadeOnDelete();
            $table->date('fecha');
            $table->string('tipo_test', 100);
            $table->text('descripcion');
            $table->string('archivo');
            $table->string('archivo_nombre_original')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('club_pagos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('club_miembro_id')->constrained('club_miembros')->cascadeOnDelete();
            $table->date('periodo'); // Siempre el día 1 del mes pagado (ej. 2026-11-01)
            $table->unsignedInteger('monto')->default(30);
            $table->date('fecha_pago');
            $table->string('nro_recibo', 50)->nullable();
            $table->string('observaciones')->nullable();
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['club_miembro_id', 'periodo']);
        });

        // Grupos iniciales del club
        $now = now();
        DB::table('club_grupos')->insert([
            ['nombre' => 'Grupo 1', 'horario' => '08:30 - 09:30', 'orden' => 1, 'estado' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Grupo 2', 'horario' => '10:30 - 11:30', 'orden' => 2, 'estado' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Grupo 3', 'horario' => '15:00 - 16:00', 'orden' => 3, 'estado' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Grupo 4', 'horario' => '16:00 - 17:00', 'orden' => 4, 'estado' => true, 'created_at' => $now, 'updated_at' => $now],
            ['nombre' => 'Grupo 5', 'horario' => '17:00 - 18:00', 'orden' => 5, 'estado' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('club_pagos');
        Schema::dropIfExists('club_tests');
        Schema::dropIfExists('club_miembros');
        Schema::dropIfExists('club_grupos');
    }
};

