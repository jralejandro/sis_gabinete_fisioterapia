<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TherapySession extends Model
{
    protected $fillable = [
        'evaluation_id',
        'responsable_sesion',
        'fecha',
        'evolucion',
        'observaciones',
        'que_se_trabajo',
        'costo_sesion',
        'created_by'
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    protected $casts = [
        'fecha' => 'date',
    ];

    public function responsable()
    {
        return $this->belongsTo(User::class, 'responsable_sesion');
    }
}
