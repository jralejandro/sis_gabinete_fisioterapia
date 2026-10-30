<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Testing\Fluent\Concerns\Has;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Carbon\Carbon;


class Patient extends Model
{
    use HasFactory;

    protected $fillable = [
        'ci',
        'complemento',
        'nombres',
        'apellido_paterno',
        'apellido_materno',
        'sexo',
        'ocupacion',
        'fecha_nacimiento',
        'domicilio',
        'estado_civil',
        'celular',
        'persona_referencia',
        'celular_referencia',
        'medio_contacto', // 🔥 AGREGAMOS ESTE
        'estado',
        'user_id'
    ];
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function antecedent()
    {
        return $this->hasOne(Antecedent::class);
    }

    public function evaluations()
    {
        return $this->hasMany(Evaluation::class);
    }

    public function getEdadAttribute()
    {
        return Carbon::parse($this->fecha_nacimiento)->age;
    }
}
