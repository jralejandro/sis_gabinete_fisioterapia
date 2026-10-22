<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('evaluations', function (Blueprint $table) {
            $table->id();

            // Relación con paciente
            $table->foreignId('patient_id')->constrained()->onDelete('cascade');

            // Campos de referencia
            $table->text('medico_referencia')->nullable();
            $table->text('centro_referencia')->nullable();
            $table->text('diagnostico_medico')->nullable();
            $table->text('diagnostico_medico_otro')->nullable();

            $table->integer('responsable_evaluacion')->nullable();
            $table->decimal('costo_evaluacion', 10, 2)->nullable();

            // Campos clínicos principales
            $table->text('motivo_consulta')->nullable();
            $table->longText('historia_enfermedad_actual')->nullable();
            $table->integer('frecuencia_cardiaca')->nullable();
            $table->integer('frecuencia_respiratoria')->nullable();
            $table->string('presion_arterial', 50)->nullable();
            $table->decimal('temperatura', 4, 1)->nullable();

            // ⚠️ Campos eliminados: aparicion, localizacion, intensidad, caracteristicas, irritacion, atenuantes, inspeccion

            // 🔥 CAMPOS NUEVOS SEGÚN EL FORMULARIO
            $table->text('caracteristicas_dolor')->nullable();
            $table->text('inspeccion')->nullable();
            $table->text('evaluacion_postural')->nullable();

            // Palpación / Movilización
            $table->text('contractura_muscular')->nullable();
            $table->text('puntos_dolorosos')->nullable();
            $table->text('puntos_gatillo')->nullable();
            $table->text('retreaccion_acortamiento')->nullable();

            // Tono muscular
            $table->string('tono', 50)->nullable();
            $table->text('tono_detalle')->nullable();

            // Trofismo
            $table->string('trofismo', 50)->nullable();
            $table->text('trofismo_detalle')->nullable();

            // Evaluación funcional
            $table->text('evaluacion_marcha')->nullable();
            $table->text('balance_articular')->nullable();
            $table->text('balance_muscular')->nullable();
            $table->text('sensibilidad')->nullable();

            // Otros campos clínicos
            $table->text('examenes_complementarios')->nullable();
            $table->text('diagnostico_kinesico')->nullable();
            $table->text('objetivos_tratamiento')->nullable();
            $table->text('plan_tratamiento')->nullable();

            $table->date('fecha')->nullable();
            $table->text('estado')->nullable();

            // Control interno
            $table->unsignedBigInteger('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('evaluations');
    }
};
