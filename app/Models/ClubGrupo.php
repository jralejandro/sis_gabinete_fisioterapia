<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubGrupo extends Model
{
    protected $table = 'club_grupos';

    protected $fillable = [
        'nombre',
        'horario',
        'dias',
        'cupo_maximo',
        'orden',
        'estado',
    ];

    protected $casts = [
        'estado' => 'boolean',
        'cupo_maximo' => 'integer',
        'orden' => 'integer',
    ];

    public function miembros()
    {
        return $this->hasMany(ClubMiembro::class, 'club_grupo_id');
    }

    public function miembrosActivos()
    {
        return $this->hasMany(ClubMiembro::class, 'club_grupo_id')
            ->where('estado', ClubMiembro::ESTADO_ACTIVO);
    }
}

