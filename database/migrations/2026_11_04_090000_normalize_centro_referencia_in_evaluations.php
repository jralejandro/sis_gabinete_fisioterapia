<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

/**
 * Normaliza `evaluations.centro_referencia` al catálogo del frontend
 * (frontend-fisio/src/utils/constants.js → listaCentrosReferencia).
 *
 * - Solo corrige variantes reconocibles sin ambigüedad (errores de tipeo, siglas, mayúsculas, espacios).
 * - Los valores que no se reconocen se dejan intactos; en el formulario aparecen como "Otro".
 * - "Ninguno" se convierte en NULL (equivale a "sin referencia / particular").
 *
 * ⚠️ No es reversible automáticamente: genere un respaldo antes (php artisan backup:run --manual).
 */
return new class extends Migration
{
    /** Valores oficiales del catálogo (deben coincidir EXACTAMENTE con el `value` del frontend). */
    private const CATALOGO = [
        'Sistema Único de Salud (SUS)',
        'Hospital de Clínicas',
        'Hospital del Niño Dr. Ovidio Aliaga Uría',
        'Hospital de la Mujer',
        'Instituto Nacional de Tórax',
        'Hospital Arco Iris',
        'Hospital Municipal',
        'Centro de Salud (1er nivel)',
        'Caja Nacional de Salud (CNS)',
        'Caja Petrolera de Salud (CPS)',
        'Caja Bancaria Estatal de Salud (CBES)',
        'Caja de Salud de la Banca Privada (CSBP)',
        'Caja de Salud de Caminos (CSC)',
        'Caja de Salud CORDES',
        'Corporación del Seguro Social Militar (COSSMIL)',
        'Seguro Social Universitario (SSU)',
        'Clínica Privada',
        'Consultorio Médico Particular',
    ];

    /** Variantes encontradas en los datos históricos (clave normalizada => valor del catálogo). */
    private const VARIANTES = [
        // Caja Nacional de Salud
        'cns'                     => 'Caja Nacional de Salud (CNS)',
        'caja nacional de salud'  => 'Caja Nacional de Salud (CNS)',
        'caja nacional salud'     => 'Caja Nacional de Salud (CNS)',
        'caja nacionald de salud' => 'Caja Nacional de Salud (CNS)',
        'caja ncional de salud'   => 'Caja Nacional de Salud (CNS)',
        // SUS
        'sus'                     => 'Sistema Único de Salud (SUS)',
        'sistema unico de salud'  => 'Sistema Único de Salud (SUS)',
        // Seguro Universitario
        'ssu'                     => 'Seguro Social Universitario (SSU)',
        'seguro universitario'    => 'Seguro Social Universitario (SSU)',
        // Caja Petrolera
        'cps'                     => 'Caja Petrolera de Salud (CPS)',
        'caja petrolera'          => 'Caja Petrolera de Salud (CPS)',
        'caja petrolera de salud' => 'Caja Petrolera de Salud (CPS)',
        // Cajas bancarias (solo cuando se especifica cuál)
        'cbes'                    => 'Caja Bancaria Estatal de Salud (CBES)',
        'caja bancaria estatal'   => 'Caja Bancaria Estatal de Salud (CBES)',
        'csbp'                    => 'Caja de Salud de la Banca Privada (CSBP)',
        'caja bancaria privada'   => 'Caja de Salud de la Banca Privada (CSBP)',
        // Hospitales
        'hospital de clinicas'    => 'Hospital de Clínicas',
        // Sin referencia
        'ninguno'                 => null,
        'ninguna'                 => null,
    ];

    public function up(): void
    {
        $mapa = self::VARIANTES;
        foreach (self::CATALOGO as $oficial) {
            $mapa[$this->normalizar($oficial)] = $oficial;
        }

        // Se recorre fila por fila: un DISTINCT agruparía variantes que solo difieren en
        // mayúsculas/acentos (collation case-insensitive) y dejaría algunas sin corregir.
        DB::table('evaluations')
            ->select('id', 'centro_referencia')
            ->whereNotNull('centro_referencia')
            ->orderBy('id')
            ->chunkById(500, function ($filas) use ($mapa) {
                foreach ($filas as $fila) {
                    $original = $fila->centro_referencia;
                    $clave = $this->normalizar($original);

                    if ($clave === '') {
                        $nuevo = null;
                    } elseif (array_key_exists($clave, $mapa)) {
                        $nuevo = $mapa[$clave];
                    } else {
                        continue; // No reconocido: se conserva tal cual (se mostrará como "Otro").
                    }

                    if ($nuevo !== $original) {
                        DB::table('evaluations')
                            ->where('id', $fila->id)
                            ->update(['centro_referencia' => $nuevo]);
                    }
                }
            });
    }

    public function down(): void
    {
        // Normalización de datos no reversible: restaurar desde un respaldo si fuera necesario.
    }

    private function normalizar(?string $valor): string
    {
        $valor = Str::ascii(mb_strtolower((string) $valor));

        return trim(preg_replace('/\s+/', ' ', $valor));
    }
};
