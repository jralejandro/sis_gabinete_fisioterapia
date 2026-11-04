<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ClubTest extends Model
{
    /** Disco privado (storage/app/private): los archivos NO son accesibles por URL pública. */
    public const DISK = 'local';
    public const DIRECTORIO = 'club_tests';

    protected $table = 'club_tests';

    protected $fillable = [
        'club_miembro_id',
        'fecha',
        'tipo_test',
        'descripcion',
        'archivo',
        'archivo_nombre_original',
        'created_by',
    ];

    protected $casts = [
        'fecha' => 'date:Y-m-d',
    ];

    /** Nunca exponemos la ruta interna del archivo en la API. */
    protected $hidden = ['archivo'];

    public function miembro()
    {
        return $this->belongsTo(ClubMiembro::class, 'club_miembro_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}

