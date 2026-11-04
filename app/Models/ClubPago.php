<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubPago extends Model
{
    protected $table = 'club_pagos';

    protected $fillable = [
        'club_miembro_id',
        'periodo',
        'monto',
        'fecha_pago',
        'nro_recibo',
        'observaciones',
        'created_by',
    ];

    protected $casts = [
        'periodo' => 'date:Y-m',
        'fecha_pago' => 'date:Y-m-d',
        'monto' => 'integer',
    ];

    public function miembro()
    {
        return $this->belongsTo(ClubMiembro::class, 'club_miembro_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

