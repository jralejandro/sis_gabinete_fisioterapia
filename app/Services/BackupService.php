<?php

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use PDO;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;
use ZipArchive;

/**
 * Genera, lista y depura respaldos (BD + archivos adicionales) en un único ZIP.
 *
 * El volcado SQL se genera en PHP puro (PDO), por lo que NO requiere mysqldump
 * dentro del contenedor. El .sql resultante se puede restaurar con:
 *   mysql -u USER -p gabinete_fisio < database.sql   (o importarlo en phpMyAdmin)
 */
class BackupService
{
    /** backup_gabinete_2026-11-03_020000_auto.zip */
    public const FILENAME_REGEX = '/^backup_gabinete_(\d{4}-\d{2}-\d{2}_\d{6})_(auto|manual)\.zip$/';

    private const INSERT_BATCH_ROWS = 500;
    private const INSERT_BATCH_BYTES = 1048576; // 1 MB por sentencia INSERT

    private const BINARY_TYPES = [
        'binary', 'varbinary', 'tinyblob', 'blob', 'mediumblob', 'longblob',
        'bit', 'geometry', 'point', 'linestring', 'polygon',
        'multipoint', 'multilinestring', 'multipolygon', 'geometrycollection',
    ];

    public function directory(): string
    {
        $path = config('backup.path');
        File::ensureDirectoryExists($path, 0775);

        return $path;
    }

    // =====================================================================
    // CREAR RESPALDO
    // =====================================================================

    /**
     * Crea un respaldo completo y devuelve su información.
     *
     * @param  string  $type  'auto' | 'manual'
     */
    public function create(string $type = 'manual'): array
    {
        $type = $type === 'auto' ? 'auto' : 'manual';

        // Evita que dos respaldos corran al mismo tiempo (cron + botón manual).
        $lock = Cache::lock('backup:running', 1800);
        if (! $lock->get()) {
            throw new RuntimeException('Ya hay un respaldo en proceso. Intente nuevamente en unos minutos.');
        }

        @set_time_limit(0);

        $dir = $this->directory();
        $stamp = now()->format('Y-m-d_His');
        $filename = "backup_gabinete_{$stamp}_{$type}.zip";
        $sqlTmp = $dir . "/.{$stamp}_database.sql.tmp";
        $zipTmp = $dir . "/.{$filename}.partial";

        try {
            $this->dumpDatabase($sqlTmp);
            $this->buildZip($zipTmp, $sqlTmp, $type);

            if (! rename($zipTmp, $dir . '/' . $filename)) {
                throw new RuntimeException('No se pudo mover el respaldo a su ubicación final.');
            }
            @chmod($dir . '/' . $filename, 0664);

            return $this->info($filename);
        } finally {
            @unlink($sqlTmp);
            @unlink($zipTmp);
            $lock->release();
        }
    }

    // =====================================================================
    // LISTAR / OBTENER / ELIMINAR
    // =====================================================================

    public function list(): array
    {
        $files = [];
        foreach (File::files($this->directory()) as $file) {
            if (preg_match(self::FILENAME_REGEX, $file->getFilename())) {
                $files[] = $this->info($file->getFilename());
            }
        }

        usort($files, fn ($a, $b) => strcmp($b['created_at'], $a['created_at']));

        return $files;
    }

    /**
     * Devuelve la ruta absoluta de un respaldo validando el nombre
     * (protege contra path traversal: solo se aceptan nombres generados por el sistema).
     */
    public function path(string $filename): ?string
    {
        if (! preg_match(self::FILENAME_REGEX, $filename)) {
            return null;
        }

        $path = $this->directory() . '/' . $filename;

        return is_file($path) ? $path : null;
    }

    public function delete(string $filename): bool
    {
        $path = $this->path($filename);

        return $path ? @unlink($path) : false;
    }

    /**
     * Elimina respaldos con más de N días. Siempre conserva el más reciente.
     *
     * @return int Cantidad de archivos eliminados
     */
    public function prune(?int $keepDays = null): int
    {
        $keepDays ??= (int) config('backup.keep_days');
        if ($keepDays <= 0) {
            return 0;
        }

        $limit = now()->subDays($keepDays);
        $backups = $this->list();
        array_shift($backups); // el más reciente nunca se borra

        $deleted = 0;
        foreach ($backups as $backup) {
            if (Carbon::parse($backup['created_at'])->lt($limit) && $this->delete($backup['name'])) {
                $deleted++;
            }
        }

        return $deleted;
    }

    private function info(string $filename): array
    {
        preg_match(self::FILENAME_REGEX, $filename, $m);
        $path = $this->directory() . '/' . $filename;

        return [
            'name' => $filename,
            'type' => $m[2],
            'size' => filesize($path),
            'created_at' => Carbon::createFromFormat('Y-m-d_His', $m[1])->toIso8601String(),
        ];
    }

    // =====================================================================
    // VOLCADO SQL (PHP puro)
    // =====================================================================

    private function dumpDatabase(string $target): void
    {
        $connection = DB::connection(config('backup.connection'));
        $pdo = $connection->getPdo();
        $database = $connection->getDatabaseName();
        $structureOnly = config('backup.structure_only_tables', []);

        $fh = fopen($target, 'wb');
        if (! $fh) {
            throw new RuntimeException("No se pudo escribir el archivo temporal: {$target}");
        }

        // Fechas TIMESTAMP en UTC para que la restauración sea independiente de la zona horaria.
        $previousTz = $pdo->query('SELECT @@session.time_zone')->fetchColumn();
        $previousBuffered = $pdo->getAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY);
        $pdo->exec("SET time_zone = '+00:00'");

        // Instantánea consistente: todos los datos corresponden al mismo instante.
        $pdo->exec('START TRANSACTION WITH CONSISTENT SNAPSHOT');

        try {
            $serverVersion = $pdo->getAttribute(PDO::ATTR_SERVER_VERSION);
            fwrite($fh, "-- Respaldo Gabinete de Fisioterapia (UMSA)\n");
            fwrite($fh, "-- Base de datos: `{$database}`\n");
            fwrite($fh, '-- Generado: ' . now()->toDateTimeString() . ' (' . config('app.timezone') . ")\n");
            fwrite($fh, "-- Servidor MySQL: {$serverVersion}\n\n");
            fwrite($fh, "SET NAMES utf8mb4;\n");
            fwrite($fh, "SET time_zone = '+00:00';\n");
            fwrite($fh, "SET FOREIGN_KEY_CHECKS = 0;\n");
            fwrite($fh, "SET UNIQUE_CHECKS = 0;\n");
            fwrite($fh, "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n\n");

            $objects = $pdo->prepare(
                'SELECT TABLE_NAME, TABLE_TYPE FROM information_schema.TABLES
                 WHERE TABLE_SCHEMA = ? ORDER BY TABLE_TYPE, TABLE_NAME'
            );
            $objects->execute([$database]);
            $objects = $objects->fetchAll(PDO::FETCH_NUM);

            $views = [];
            foreach ($objects as [$name, $kind]) {
                if ($kind === 'VIEW') {
                    $views[] = $name;
                    continue;
                }

                $this->dumpTableStructure($pdo, $fh, $name);

                if (! in_array($name, $structureOnly, true)) {
                    $this->dumpTableData($pdo, $fh, $database, $name);
                }
            }

            foreach ($views as $view) {
                $create = $pdo->query('SHOW CREATE VIEW ' . $this->ident($view))->fetch(PDO::FETCH_NUM)[1];
                $create = preg_replace('/\sDEFINER=`[^`]*`@`[^`]*`/', '', $create);
                fwrite($fh, "-- Vista {$view}\nDROP VIEW IF EXISTS " . $this->ident($view) . ";\n{$create};\n\n");
            }

            fwrite($fh, "SET UNIQUE_CHECKS = 1;\n");
            fwrite($fh, "SET FOREIGN_KEY_CHECKS = 1;\n");
            fwrite($fh, "-- Fin del respaldo\n");
        } finally {
            $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, $previousBuffered);
            $pdo->exec('COMMIT');
            $pdo->exec('SET time_zone = ' . $pdo->quote($previousTz));
            fclose($fh);
        }
    }

    private function dumpTableStructure(PDO $pdo, $fh, string $table): void
    {
        $create = $pdo->query('SHOW CREATE TABLE ' . $this->ident($table))->fetch(PDO::FETCH_NUM)[1];

        fwrite($fh, "-- --------------------------------------------------------\n");
        fwrite($fh, "-- Tabla {$table}\n");
        fwrite($fh, "-- --------------------------------------------------------\n");
        fwrite($fh, 'DROP TABLE IF EXISTS ' . $this->ident($table) . ";\n");
        fwrite($fh, $create . ";\n\n");
    }

    private function dumpTableData(PDO $pdo, $fh, string $database, string $table): void
    {
        // Columnas binarias se exportan en hexadecimal para no corromperlas.
        $stmt = $pdo->prepare(
            'SELECT COLUMN_NAME, DATA_TYPE, EXTRA FROM information_schema.COLUMNS
             WHERE TABLE_SCHEMA = ? AND TABLE_NAME = ? ORDER BY ORDINAL_POSITION'
        );
        $stmt->execute([$database, $table]);

        $columns = [];
        $binary = [];
        foreach ($stmt->fetchAll(PDO::FETCH_NUM) as [$column, $dataType, $extra]) {
            // Las columnas generadas no admiten INSERT.
            if (stripos($extra, 'GENERATED') !== false) {
                continue;
            }
            $binary[] = in_array(strtolower($dataType), self::BINARY_TYPES, true);
            $columns[] = $column;
        }

        if (! $columns) {
            return;
        }

        $columnList = implode(', ', array_map(fn ($c) => $this->ident($c), $columns));
        $insertHead = 'INSERT INTO ' . $this->ident($table) . " ({$columnList}) VALUES\n";

        // Lectura sin buffer: no carga toda la tabla en memoria.
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, false);
        $rows = $pdo->query("SELECT {$columnList} FROM " . $this->ident($table));

        $batch = [];
        $batchBytes = 0;
        $flush = function () use (&$batch, &$batchBytes, $fh, $insertHead) {
            if ($batch) {
                fwrite($fh, $insertHead . implode(",\n", $batch) . ";\n");
                $batch = [];
                $batchBytes = 0;
            }
        };

        while ($row = $rows->fetch(PDO::FETCH_NUM)) {
            $values = [];
            foreach ($row as $i => $value) {
                if ($value === null) {
                    $values[] = 'NULL';
                } elseif ($binary[$i]) {
                    $values[] = $value === '' ? "''" : '0x' . bin2hex($value);
                } elseif (is_int($value)) {
                    $values[] = (string) $value;
                } else {
                    $values[] = $pdo->quote((string) $value);
                }
            }

            $line = '(' . implode(',', $values) . ')';
            $batch[] = $line;
            $batchBytes += strlen($line);

            if (count($batch) >= self::INSERT_BATCH_ROWS || $batchBytes >= self::INSERT_BATCH_BYTES) {
                $flush();
            }
        }
        $flush();

        $rows->closeCursor();
        $pdo->setAttribute(PDO::MYSQL_ATTR_USE_BUFFERED_QUERY, true);
        fwrite($fh, "\n");
    }

    private function ident(string $name): string
    {
        return '`' . str_replace('`', '``', $name) . '`';
    }

    // =====================================================================
    // EMPAQUETADO ZIP
    // =====================================================================

    private function buildZip(string $zipPath, string $sqlPath, string $type): void
    {
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            throw new RuntimeException('No se pudo crear el archivo ZIP del respaldo.');
        }

        $zip->addFile($sqlPath, 'database.sql');

        $totalFiles = 0;
        foreach (config('backup.directories', []) as $alias => $dir) {
            $zip->addEmptyDir("archivos/{$alias}");

            if (! is_dir($dir)) {
                continue;
            }

            $iterator = new RecursiveIteratorIterator(
                new RecursiveDirectoryIterator($dir, RecursiveDirectoryIterator::SKIP_DOTS),
                RecursiveIteratorIterator::SELF_FIRST
            );

            foreach ($iterator as $file) {
                if ($file->getFilename() === '.gitignore') {
                    continue;
                }
                $relative = ltrim(str_replace('\\', '/', substr($file->getPathname(), strlen($dir))), '/');
                if ($file->isDir()) {
                    $zip->addEmptyDir("archivos/{$alias}/{$relative}");
                } elseif ($file->isFile()) {
                    $zip->addFile($file->getPathname(), "archivos/{$alias}/{$relative}");
                    $totalFiles++;
                }
            }
        }

        $zip->addFromString('LEEME.txt', implode("\n", [
            'RESPALDO - SISTEMA GABINETE DE FISIOTERAPIA (UMSA)',
            '==================================================',
            'Fecha:            ' . now()->toDateTimeString() . ' (' . config('app.timezone') . ')',
            'Tipo:             ' . ($type === 'auto' ? 'Automático (programado)' : 'Manual'),
            'Archivos incluidos: ' . $totalFiles,
            '',
            'CONTENIDO',
            '  database.sql        Volcado completo de la base de datos.',
            '  archivos/public/    Copia de storage/app/public (exámenes complementarios, etc.).',
            '',
            'RESTAURAR LA BASE DE DATOS',
            '  docker compose exec -T db mysql -u root -p gabinete_fisio < database.sql',
            '  (o importar database.sql desde phpMyAdmin)',
            '',
            'RESTAURAR LOS ARCHIVOS',
            '  Copiar el contenido de archivos/public/ a storage/app/public/',
            '',
        ]));

        if (! $zip->close()) {
            throw new RuntimeException('No se pudo finalizar el archivo ZIP del respaldo.');
        }
    }
}

