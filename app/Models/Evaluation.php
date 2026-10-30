<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Evaluation extends Model
{
    protected $fillable = [
        'patient_id',
        'fecha',
        'estado',
        'medico_referencia',
        'centro_referencia',
        'diagnostico_medico',
        'diagnostico_medico_otro',
        'responsable_evaluacion',
        'costo_evaluacion',
        'motivo_consulta',
        'historia_enfermedad_actual',
        'frecuencia_cardiaca',
        'frecuencia_respiratoria',
        'presion_arterial',
        'temperatura',
        'caracteristicas_dolor',
        'inspeccion',
        'evaluacion_postural',
        'contractura_muscular',
        'puntos_dolorosos',
        'puntos_gatillo',
        'retreaccion_acortamiento',
        'tono',
        'tono_detalle',
        'trofismo',
        'trofismo_detalle',
        'evaluacion_marcha',
        'balance_articular',
        'balance_muscular',
        'sensibilidad',
        'examenes_complementarios',
        'archivo_examen_complementario',
        'diagnostico_kinesico',
        'objetivos_tratamiento',
        'plan_tratamiento',
        'created_by'
    ];

    protected $attributes = [
        'estado' => 'ACTIVO',
    ];

    protected $casts = [
        'fecha' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function therapySessions()
    {
        return $this->hasMany(TherapySession::class);
    }

    public function gynecoObstetricHistory()
    {
        return $this->hasOne(GynecoObstetricHistory::class);
    }

    public function dischargeRecord()
    {
        return $this->hasOne(DischargeRecord::class);
    }
}
