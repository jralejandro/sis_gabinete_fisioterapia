<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class DischargeRecord extends Model
{
    use HasFactory;

    protected $fillable = [
        'evaluation_id',
        'fecha_alta',
        'responsable_alta',
        'evolucion_alta',
        'observaciones_alta',
        'recomendaciones_alta',
    ];

    public function evaluation()
    {
        return $this->belongsTo(Evaluation::class);
    }

    public function responsableUser()
    {
        return $this->belongsTo(User::class, 'responsable_alta');
    }
}
