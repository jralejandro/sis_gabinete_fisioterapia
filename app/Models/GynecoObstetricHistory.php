<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GynecoObstetricHistory extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'gestaciones',
        'partos',
        'abortos',
        'cesareas',
        'fecha_ultima_menstruacion',
        'fecha_ultimo_parto',
        'fecha_ultima_cesarea',
        'fecha_ultimo_aborto',
        'edad_menarca',
        'patron_catamenial',
        'ciclo_menstrual',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    
    protected $casts = [
        'fecha_ultima_menstruacion' => 'date',
        'fecha_ultimo_parto' => 'date',
        'fecha_ultima_cesarea' => 'date',
        'fecha_ultimo_aborto' => 'date',
    ];
}
