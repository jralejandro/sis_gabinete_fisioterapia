<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

class ClubMiembro extends Model
{
    public const ESTADO_ACTIVO = 'ACTIVO';
    public const ESTADO_RETIRADO = 'RETIRADO';

    protected $table = 'club_miembros';

    protected $fillable = [
        'patient_id',
        'club_grupo_id',
        'fecha_inscripcion',
        'pago_matricula',
        'estado',
        'fecha_baja',
        'motivo_baja',
        'observaciones',
        'created_by',
    ];

    protected $casts = [
        'fecha_inscripcion' => 'date:Y-m-d',
        'fecha_baja' => 'date:Y-m-d',
        'pago_matricula' => 'integer',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function grupo()
    {
        return $this->belongsTo(ClubGrupo::class, 'club_grupo_id');
    }

    public function pagos()
    {
        return $this->hasMany(ClubPago::class, 'club_miembro_id')->orderBy('periodo');
    }

    public function tests()
    {
        return $this->hasMany(ClubTest::class, 'club_miembro_id')->orderByDesc('fecha')->orderByDesc('id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Último mes (inclusive) en que el participante debía pagar:
     * el mes actual si sigue activo, o el mes de su baja si se retiró.
     */
    public function ultimoPeriodoExigible(?Carbon $hoy = null): Carbon
    {
        $hoy = ($hoy ?? now())->copy()->startOfMonth();

        if ($this->estado === self::ESTADO_RETIRADO && $this->fecha_baja) {
            $baja = $this->fecha_baja->copy()->startOfMonth();
            return $baja->lessThan($hoy) ? $baja : $hoy;
        }

        return $hoy;
    }

    /**
     * Lista de periodos ("Y-m") que el participante debía pagar hasta hoy.
     */
    public function periodosExigibles(?Carbon $hoy = null): array
    {
        $inicio = $this->fecha_inscripcion->copy()->startOfMonth();
        $fin = $this->ultimoPeriodoExigible($hoy);

        $periodos = [];
        for ($p = $inicio->copy(); $p->lessThanOrEqualTo($fin); $p->addMonth()) {
            $periodos[] = $p->format('Y-m');
        }

        return $periodos;
    }

    /**
     * Periodos ("Y-m") pendientes de pago (mora). Requiere que la relación `pagos` esté cargada
     * o la consulta en el momento.
     */
    public function periodosPendientes(?Carbon $hoy = null): array
    {
        $pagados = $this->pagos->map(fn($p) => $p->periodo->format('Y-m'))->all();
        return array_values(array_diff($this->periodosExigibles($hoy), $pagados));
    }
}

