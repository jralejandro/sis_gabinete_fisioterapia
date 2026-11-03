<?php

use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// 🔒 Respaldo automático diario (BD + archivos). Lo ejecuta el servicio `scheduler` de docker-compose.
Schedule::command('backup:run')
    ->dailyAt(config('backup.schedule_time', '02:00'))
    ->withoutOverlapping()
    ->appendOutputTo(storage_path('logs/backups.log'));
