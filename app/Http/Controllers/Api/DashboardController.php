<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Patient;
use App\Models\Evaluation;
use App\Models\TherapySession;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        /* ============================================================
         * 1. RESÚMENES GENERALES Y PROMEDIOS (100% en SQL sin gastar RAM)
         * ============================================================ */
        $totalPacientes    = Patient::count();
        $totalEvaluaciones = Evaluation::count();
        $totalSesiones     = TherapySession::count();

        $promedioSesionesPorEvaluacion = round(
            TherapySession::select('evaluation_id', DB::raw('count(*) as total'))
                ->groupBy('evaluation_id')
                ->pluck('total')
                ->avg() ?? 0,
            1
        );

        // 🔥 OPTIMIZADO: Calculamos en SQL sin descargar toda la base de datos con ->get()
        $promedioEvaluacionesPorPaciente = round(
            Evaluation::select('patient_id', DB::raw('count(*) as total'))
                ->groupBy('patient_id')
                ->pluck('total')
                ->avg() ?? 0,
            1
        );

        /* ============================================================
         * 2. PACIENTES POR SEXO
         * ============================================================ */
        $pacientesPorSexoRaw = Patient::select('sexo', DB::raw('count(*) as total'))
            ->groupBy('sexo')
            ->pluck('total', 'sexo');

        $pacientesPorSexo = [
            'Masculino' => $pacientesPorSexoRaw['Masculino'] ?? 0,
            'Femenino'  => $pacientesPorSexoRaw['Femenino'] ?? 0,
        ];

        /* ============================================================
         * 3. ÚLTIMOS 5 MESES (FECHAS BASE)
         * ============================================================ */
        $ultimos5Meses = collect();
        $ultimos5MesesLegibles = collect();
        $fechaInicio5Meses = now()->subMonths(4)->startOfMonth();

        for ($i = 4; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $ultimos5Meses->push($fecha->format('Y-m'));
            $ultimos5MesesLegibles->push(ucfirst($fecha->translatedFormat('M Y')));
        }

        /* ============================================================
         * 4. EVALUACIONES, SESIONES Y GANANCIAS POR MES (Agrupadas)
         * ============================================================ */
        $evaluacionesStats = Evaluation::select(
            DB::raw('DATE_FORMAT(fecha, "%Y-%m") as mes'),
            DB::raw('count(*) as total'),
            DB::raw('SUM(costo_evaluacion) as ingresos')
        )
            ->where('fecha', '>=', $fechaInicio5Meses)
            ->groupBy('mes')
            ->get()
            ->keyBy('mes');

        $sesionesStats = TherapySession::select(
            DB::raw('DATE_FORMAT(fecha, "%Y-%m") as mes'),
            DB::raw('count(*) as total'),
            DB::raw('SUM(costo_sesion) as ingresos')
        )
            ->where('fecha', '>=', $fechaInicio5Meses)
            ->groupBy('mes')
            ->get()
            ->keyBy('mes');

        $evaluacionesPorMes = $ultimos5Meses->map(fn($mes) => (int) ($evaluacionesStats[$mes]->total ?? 0))->values();
        $sesionesPorMes     = $ultimos5Meses->map(fn($mes) => (int) ($sesionesStats[$mes]->total ?? 0))->values();

        // 🔥 OPTIMIZADO: Sumamos ingresos de evaluaciones + sesiones sin hacer 10 consultas en bucle
        $gananciasPorMes    = $ultimos5Meses->map(function ($mes) use ($evaluacionesStats, $sesionesStats) {
            $ingEval   = (float) ($evaluacionesStats[$mes]->ingresos ?? 0);
            $ingSesion = (float) ($sesionesStats[$mes]->ingresos ?? 0);
            return round($ingEval + $ingSesion, 2);
        })->values();

        /* ============================================================
         * 5. PACIENTES POR RANGO DE EDAD
         * ============================================================ */
        $rangos = [
            '<20'   => [0, 19],
            '20-29' => [20, 29],
            '30-39' => [30, 39],
            '40-49' => [40, 49],
            '50+'   => [50, 200],
        ];

        $pacientesPorEdad = collect($rangos)->mapWithKeys(function ($rango, $label) {
            return [
                $label => Patient::whereBetween(DB::raw('TIMESTAMPDIFF(YEAR, fecha_nacimiento, CURDATE())'), $rango)->count()
            ];
        });

        $rangosEdad            = $pacientesPorEdad->keys()->values();
        $pacientesPorRangoEdad = $pacientesPorEdad->values();

        /* ============================================================
         * 6. ESTADO DE EVALUACIONES
         * ============================================================ */
        $estadosEvaluacion = Evaluation::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        $cantidadActivos  = $estadosEvaluacion['ACTIVO']   ?? 0;
        $cantidadAbandono = $estadosEvaluacion['ABANDONO'] ?? 0;
        $cantidadAlta     = $estadosEvaluacion['ALTA']     ?? 0;

        $totalEstados = $cantidadActivos + $cantidadAbandono + $cantidadAlta;

        $porcentajeActivos  = $totalEstados > 0 ? round(($cantidadActivos / $totalEstados) * 100, 1) : 0;
        $porcentajeAbandono = $totalEstados > 0 ? round(($cantidadAbandono / $totalEstados) * 100, 1) : 0;
        $porcentajeAlta     = $totalEstados > 0 ? round(($cantidadAlta / $totalEstados) * 100, 1) : 0;

        /* ============================================================
         * 7. TOP 10 OCUPACIONES
         * ============================================================ */
        $topOcupaciones = Patient::select('ocupacion', DB::raw('count(*) as total'))
            ->whereNotNull('ocupacion')
            ->where('ocupacion', '!=', '')
            ->groupBy('ocupacion')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $ocupacionesLabels = $topOcupaciones->pluck('ocupacion')->values();
        $ocupacionesData   = $topOcupaciones->pluck('total')->values();

        /* ============================================================
         * 7.5 TOP 10 CENTROS DE REFERENCIA (NUEVO)
         * ============================================================ */
        $topCentros = Evaluation::select('centro_referencia', DB::raw('count(*) as total'))
            ->whereNotNull('centro_referencia')
            ->where('centro_referencia', '!=', '')
            ->groupBy('centro_referencia')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $centrosLabels = $topCentros->pluck('centro_referencia')->values();
        $centrosData   = $topCentros->pluck('total')->values();

        /* ============================================================
         * 8. RESPUESTA API JSON PARA REACT
         * ============================================================ */
        return response()->json([
            'ultimos5MesesLegibles'           => $ultimos5MesesLegibles,
            'ultimos5Meses'                   => $ultimos5Meses,
            'totalPacientes'                  => $totalPacientes,
            'totalEvaluaciones'               => $totalEvaluaciones,
            'totalSesiones'                   => $totalSesiones,
            'pacientesPorSexo'                => $pacientesPorSexo,
            'evaluacionesPorMes'              => $evaluacionesPorMes,
            'sesionesPorMes'                  => $sesionesPorMes,
            'rangosEdad'                      => $rangosEdad,
            'pacientesPorRangoEdad'           => $pacientesPorRangoEdad,
            'promedioEvaluacionesPorPaciente' => $promedioEvaluacionesPorPaciente,
            'promedioSesionesPorEvaluacion'   => $promedioSesionesPorEvaluacion,
            'gananciasPorMes'                 => $gananciasPorMes,
            'cantidadActivos'                 => $cantidadActivos,
            'cantidadAbandono'                => $cantidadAbandono,
            'cantidadAlta'                    => $cantidadAlta,
            'porcentajeActivos'               => $porcentajeActivos,
            'porcentajeAbandono'              => $porcentajeAbandono,
            'porcentajeAlta'                  => $porcentajeAlta,
            'ocupacionesLabels'               => $ocupacionesLabels,
            'ocupacionesData'                 => $ocupacionesData,
            'centrosLabels'                   => $centrosLabels,
            'centrosData'                     => $centrosData,
        ], 200);
    }
}
