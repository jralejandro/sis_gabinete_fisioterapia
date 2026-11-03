<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Carpeta de respaldos
    |--------------------------------------------------------------------------
    | Se guardan en storage/app/private (NO es accesible públicamente).
    */
    'path' => storage_path('app/private/backups'),

    /*
    |--------------------------------------------------------------------------
    | Retención
    |--------------------------------------------------------------------------
    | Días que se conservan los respaldos. El más reciente nunca se elimina.
    | 0 = no eliminar nunca.
    */
    'keep_days' => (int) env('BACKUP_KEEP_DAYS', 30),

    /*
    |--------------------------------------------------------------------------
    | Hora del respaldo automático diario (zona horaria de config/app.php)
    |--------------------------------------------------------------------------
    */
    'schedule_time' => env('BACKUP_TIME', '02:00'),

    /*
    |--------------------------------------------------------------------------
    | Conexión de base de datos a respaldar
    |--------------------------------------------------------------------------
    */
    'connection' => env('DB_CONNECTION', 'mysql'),

    /*
    |--------------------------------------------------------------------------
    | Carpetas de archivos adicionales a incluir en el ZIP
    |--------------------------------------------------------------------------
    | alias => ruta absoluta. Se guardan dentro del ZIP en archivos/{alias}/...
    */
    'directories' => [
        'public' => storage_path('app/public'), // examenes_complementarios, etc.
    ],

    /*
    |--------------------------------------------------------------------------
    | Tablas de las que solo se respalda la estructura (sin datos)
    |--------------------------------------------------------------------------
    */
    'structure_only_tables' => [
        'cache',
        'cache_locks',
        'sessions',
        'jobs',
        'job_batches',
    ],

];

