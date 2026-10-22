<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Evaluation;
use App\Models\TherapySession;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        /* ============================================================
         * RESÚMENES GENERALES
         * ============================================================ */
        $totalPacientes    = Patient::count();
        $totalEvaluaciones = Evaluation::count();
        $totalSesiones     = TherapySession::count();

        /* ============================================================
         * PACIENTES POR SEXO
         * ============================================================ */
        $pacientesPorSexoRaw = Patient::select('sexo', DB::raw('count(*) as total'))
            ->groupBy('sexo')
            ->pluck('total', 'sexo');

        $pacientesPorSexo = [
            'Masculino' => $pacientesPorSexoRaw['Masculino'] ?? 0,
            'Femenino'  => $pacientesPorSexoRaw['Femenino'] ?? 0,
        ];

        /* ============================================================
         * ÚLTIMOS 5 MESES
         * ============================================================ */
        $ultimos5Meses = collect();
        $ultimos5MesesLegibles = collect();
        for ($i = 4; $i >= 0; $i--) {
            $fecha = now()->subMonths($i);
            $ultimos5Meses->push($fecha->format('Y-m')); // Para consultas
            $ultimos5MesesLegibles->push($fecha->translatedFormat('M Y')); // Para labels de gráficos
        }

        /* ============================================================
         * EVALUACIONES POR MES
         * ============================================================ */
        $evaluacionesSerie = Evaluation::select(DB::raw('DATE_FORMAT(fecha, "%Y-%m") as mes'), DB::raw('count(*) as total'))
            ->where('fecha', '>=', now()->subMonths(4)->startOfMonth())
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $evaluacionesPorMes = $ultimos5Meses->map(fn($mes) => $evaluacionesSerie->get($mes, 0));

        /* ============================================================
         * SESIONES POR MES
         * ============================================================ */
        $sesionesSerie = TherapySession::select(DB::raw('DATE_FORMAT(fecha, "%Y-%m") as mes'), DB::raw('count(*) as total'))
            ->where('fecha', '>=', now()->subMonths(4)->startOfMonth())
            ->groupBy('mes')
            ->pluck('total', 'mes');

        $sesionesPorMes = $ultimos5Meses->map(fn($mes) => $sesionesSerie->get($mes, 0));

        /* ============================================================
 * GANANCIAS POR MES
 * ============================================================ */
        $gananciasPorMes = collect();

        foreach ($ultimos5Meses as $mes) {

            $inicio = Carbon::createFromFormat('Y-m', $mes)->startOfMonth();
            $fin    = Carbon::createFromFormat('Y-m', $mes)->endOfMonth();

            $gananciasEvaluacionesMes = Evaluation::whereBetween('fecha', [$inicio, $fin])
                ->sum('costo_evaluacion');

            $gananciasSesionesMes = TherapySession::whereBetween('fecha', [$inicio, $fin])
                ->sum('costo_sesion');

            $ganancia = $gananciasEvaluacionesMes + $gananciasSesionesMes;

            $gananciasPorMes->push($ganancia);
        }



        /* ============================================================
         * PACIENTES POR RANGO DE EDAD
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

        $rangosEdad           = $pacientesPorEdad->keys();
        $pacientesPorRangoEdad = $pacientesPorEdad->values();

        /* ============================================================
         * PROMEDIOS
         * ============================================================ */
        $promedioSesionesPorEvaluacion = round(
            TherapySession::select('evaluation_id', DB::raw('count(*) as total'))
                ->groupBy('evaluation_id')
                ->pluck('total')
                ->avg() ?? 0,
            1
        );

        $sesionesPorPacienteCollection = TherapySession::with('evaluation.patient')
            ->get()
            ->groupBy(fn($s) => $s->evaluation->patient->id ?? null)
            ->map->count()
            ->filter(); // elimina claves nulas

        $evaluacionesPorPacienteCollection = Evaluation::with('patient')
            ->get()
            ->groupBy(fn($e) => $e->patient->id ?? null)
            ->map->count()
            ->filter();

        $promedioEvaluacionesPorPaciente = $evaluacionesPorPacienteCollection->isEmpty()
            ? 0
            : round($evaluacionesPorPacienteCollection->avg(), 0);


        /* ============================================================
 * ESTADO DE EVALUACIONES
 * ============================================================ */
        $estadosEvaluacion = Evaluation::select('estado', DB::raw('COUNT(*) as total'))
            ->groupBy('estado')
            ->pluck('total', 'estado');

        // Asignar valores garantizando que existan
        $cantidadActivos   = $estadosEvaluacion['ACTIVO']   ?? 0;
        $cantidadAbandono  = $estadosEvaluacion['ABANDONO'] ?? 0;
        $cantidadAlta      = $estadosEvaluacion['ALTA']     ?? 0;

        // Calcular total
        $totalEstados = $cantidadActivos + $cantidadAbandono + $cantidadAlta;

        // Calcular porcentajes seguro (evitar división por cero)
        $porcentajeActivos   = $totalEstados > 0 ? round(($cantidadActivos / $totalEstados) * 100, 1) : 0;
        $porcentajeAbandono  = $totalEstados > 0 ? round(($cantidadAbandono / $totalEstados) * 100, 1) : 0;
        $porcentajeAlta      = $totalEstados > 0 ? round(($cantidadAlta / $totalEstados) * 100, 1) : 0;

        /* ============================================================
         * TOP 6 OCUPACIONES (NUEVO CÓDIGO)
         * ============================================================ */
        $topOcupaciones = Patient::select('ocupacion', DB::raw('count(*) as total'))
            ->whereNotNull('ocupacion') // Ignorar nulos
            ->where('ocupacion', '!=', '') // Ignorar vacíos
            ->groupBy('ocupacion')
            ->orderByDesc('total')
            ->limit(10)
            ->get();

        $ocupacionesLabels = $topOcupaciones->pluck('ocupacion');
        $ocupacionesData   = $topOcupaciones->pluck('total');

        /* ============================================================
         * PASAR VARIABLES A LA VISTA
         * ============================================================ */
        return view('dashboard', compact(
            'ultimos5MesesLegibles',
            'ultimos5Meses',
            'totalPacientes',
            'totalEvaluaciones',
            'totalSesiones',
            'pacientesPorSexo',
            'evaluacionesPorMes',
            'sesionesPorMes',
            'rangosEdad',
            'pacientesPorRangoEdad',
            'promedioEvaluacionesPorPaciente',
            'promedioSesionesPorEvaluacion',
            'gananciasPorMes',
            // 🔥 NUEVOS DATOS
            'cantidadActivos',
            'cantidadAbandono',
            'cantidadAlta',
            'porcentajeActivos',
            'porcentajeAbandono',
            'porcentajeAlta',
            // 🔥 AGREGAR ESTAS DOS NUEVAS VARIABLES:
            'ocupacionesLabels',
            'ocupacionesData'
        ));
    }
}
