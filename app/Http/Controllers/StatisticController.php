<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Evaluation;
use App\Models\TherapySession;
use Intervention\Image\ImageManagerStatic as Image;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;

class StatisticController extends Controller
{
    public function costosForm()
    {
        $yearsEval = Evaluation::selectRaw('YEAR(fecha) as year')->distinct()->pluck('year');
        $yearsSession = TherapySession::selectRaw('YEAR(fecha) as year')->distinct()->pluck('year');

        $years = $yearsEval->merge($yearsSession)->unique()->sortDesc();

        return view('statistics.costos-form', compact('years'));
    }

    public function reporte_costos(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:2000',
            'mes' => 'nullable|integer|min:1|max:12',
        ]);

        $year = (int) $request->year;
        $mes = $request->mes ? (int) $request->mes : null;

        // Nombres de meses
        $monthsName = [
            1 => 'Enero',
            2 => 'Febrero',
            3 => 'Marzo',
            4 => 'Abril',
            5 => 'Mayo',
            6 => 'Junio',
            7 => 'Julio',
            8 => 'Agosto',
            9 => 'Septiembre',
            10 => 'Octubre',
            11 => 'Noviembre',
            12 => 'Diciembre'
        ];

        // Título del periodo para el reporte
        $periodoTexto = $mes ? strtoupper($monthsName[$mes]) . " $year" : "ANUAL $year";

        // ==========================================
        // 1. CONSULTA DE TOTALES GENERALES
        // ==========================================
        $evalQuery = Evaluation::whereYear('fecha', $year);
        $sessQuery = TherapySession::whereYear('fecha', $year);

        if ($mes) {
            $evalQuery->whereMonth('fecha', $mes);
            $sessQuery->whereMonth('fecha', $mes);
        }

        $totalEvalYear = $evalQuery->sum('costo_evaluacion');
        $totalSessYear = $sessQuery->sum('costo_sesion');
        $totalGeneral = $totalEvalYear + $totalSessYear;

        // ==========================================
        // 2. DESGLOSE DE DATOS (MENSUAL O DIARIO)
        // ==========================================
        $breakdown = collect([]);
        $labels = [];
        $data = [];

        if ($mes) {
            // --- LOGICA DIARIA (Si seleccionó mes) ---
            $daysInMonth = \Carbon\Carbon::createFromDate($year, $mes)->daysInMonth;

            for ($d = 1; $d <= $daysInMonth; $d++) {
                $e = Evaluation::whereYear('fecha', $year)->whereMonth('fecha', $mes)->whereDay('fecha', $d)->sum('costo_evaluacion');
                $s = TherapySession::whereYear('fecha', $year)->whereMonth('fecha', $mes)->whereDay('fecha', $d)->sum('costo_sesion');

                $totalDia = $e + $s;

                $breakdown->push([
                    'label' => $d, // Día
                    'eval' => $e,
                    'sess' => $s,
                    'total' => $totalDia
                ]);

                $labels[] = "$d";
                $data[] = $totalDia;
            }
            $labelEje = "Días del Mes";
        } else {
            // --- LOGICA MENSUAL (Si es anual) ---
            for ($m = 1; $m <= 12; $m++) {
                $e = Evaluation::whereYear('fecha', $year)->whereMonth('fecha', $m)->sum('costo_evaluacion');
                $s = TherapySession::whereYear('fecha', $year)->whereMonth('fecha', $m)->sum('costo_sesion');

                $totalMes = $e + $s;

                $breakdown->push([
                    'label' => $monthsName[$m],
                    'eval' => $e,
                    'sess' => $s,
                    'total' => $totalMes
                ]);

                $labels[] = substr($monthsName[$m], 0, 3); // Ene, Feb...
                $data[] = $totalMes;
            }
            $labelEje = "Meses";
        }

        // ==========================================
        // 3. GENERAR GRÁFICO (QuickChart v3)
        // ==========================================
        $chartConfig = [
            'type' => 'line', // Línea se ve mejor para tendencias financieras
            'data' => [
                'labels' => $labels,
                'datasets' => [[
                    'label' => 'Ingresos (Bs)',
                    'data' => $data,
                    'borderColor' => '#0056b3',
                    'backgroundColor' => 'rgba(0, 86, 179, 0.1)',
                    'borderWidth' => 2,
                    'pointRadius' => 3,
                    'fill' => true,
                    'tension' => 0.3 // Curvas suaves
                ]]
            ],
            'options' => [
                'layout' => ['padding' => 20],
                'plugins' => [
                    'legend' => ['display' => false],
                    'title' => [
                        'display' => true,
                        'text' => 'Evolución de Ingresos - ' . $periodoTexto,
                        'font' => ['size' => 14],
                        'padding' => 20
                    ],
                    'datalabels' => [
                        'display' => true,
                        'align' => 'top',
                        'font' => ['size' => 8, 'weight' => 'bold'],
                        'color' => '#555',
                        // Solo mostrar si valor > 0 para no ensuciar
                        'formatter' => "(val) => val > 0 ? val : ''"
                    ]
                ],
                'scales' => [
                    'y' => [
                        'beginAtZero' => true,
                        'grid' => ['color' => '#eee']
                    ],
                    'x' => [
                        'grid' => ['display' => false]
                    ]
                ]
            ]
        ];

        // Codificar JS function para QuickChart
        $chartJson = json_encode($chartConfig);
        // Truco para inyectar la función JS en el JSON string
        $chartJson = str_replace('"(val) => val > 0 ? val : \'\'"', "(val) => val > 0 ? val : ''", $chartJson);

        $chartUrl = 'https://quickchart.io/chart?v=3&c=' . urlencode($chartJson);
        $chartBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($chartUrl));

        // ==========================================
        // 4. DATOS ADICIONALES
        // ==========================================
        // Porcentajes
        $porcEval = $totalGeneral > 0 ? round(($totalEvalYear / $totalGeneral) * 100, 1) : 0;
        $porcSess = $totalGeneral > 0 ? round(($totalSessYear / $totalGeneral) * 100, 1) : 0;

        // Mensaje de Análisis
        if ($mes) {
            $mensaje = "Este reporte detalla el flujo de caja diario correspondiente a <strong>$periodoTexto</strong>. Se visualiza el comportamiento de ingresos día por día.";
        } else {
            $promedio = $totalGeneral / 12;
            $mensaje = "Reporte consolidado del ejercicio fiscal <strong>$year</strong>. El ingreso promedio mensual es de <strong>" . number_format($promedio, 2) . " Bs</strong>.";
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('statistics.costos-pdf', compact(
            'year',
            'mes',
            'periodoTexto',
            'mensaje',
            'totalEvalYear',
            'totalSessYear',
            'totalGeneral',
            'porcEval',
            'porcSess',
            'breakdown',
            'labelEje',
            'chartBase64'
        ))->setPaper('letter');

        $fileName = $mes ? "reporte_ingresos_{$mes}_{$year}.pdf" : "reporte_ingresos_{$year}.pdf";
        return $pdf->stream($fileName);
    }

    public function diagnosticoMedicoForm()
    {
        $years = Evaluation::selectRaw('YEAR(fecha) as year')
            ->distinct()
            ->pluck('year')
            ->sortDesc();

        return view('statistics.diagnostico-medico-form', compact('years'));
    }

    public function diagnosticoMedicoPDF(Request $request)
    {
        $request->validate([
            'year' => 'required|integer|min:1900',
            'mes' => 'nullable|integer|min:1|max:12',
        ]);

        $year = (int) $request->year;
        $mes = $request->mes ? (int) $request->mes : null;

        // ================== 1. OBTENCIÓN DE DATOS ======================
        $query = Evaluation::with('patient')
            ->whereYear('fecha', $year)
            ->whereNotNull('diagnostico_medico')
            ->where('diagnostico_medico', '!=', '');
        if ($mes) $query->whereMonth('fecha', $mes);
        $evaluaciones = $query->get();

        if ($evaluaciones->count() == 0) {
            return back()->with('warning', 'No hay datos para generar el reporte.');
        }

        $sesionesQuery = TherapySession::with(['evaluation.patient'])->whereYear('fecha', $year);
        if ($mes) $sesionesQuery->whereMonth('fecha', $mes);
        $sesiones = $sesionesQuery->get();

        // Normalización (Edad y Sexo)
        $normalizar = function ($p) {
            if ($p) {
                $p->sexo_simple = $p->sexo ? strtoupper(substr(trim($p->sexo), 0, 1)) : null;
                if (!isset($p->edad) || $p->edad === null) {
                    try {
                        $p->edad = !empty($p->fecha_nacimiento) ? Carbon::parse($p->fecha_nacimiento)->age : null;
                    } catch (\Exception $e) {
                        $p->edad = null;
                    }
                }
            }
        };
        $evaluaciones->each(fn($ev) => $normalizar($ev->patient));
        $sesiones->each(fn($ses) => $normalizar($ses->evaluation?->patient));

        // ================== 2. PROCESAMIENTO DE GRUPOS ======================
        $grupos = [
            '15-19' => [15, 19],
            '20-39' => [20, 39],
            '40-49' => [40, 49],
            '50-59' => [50, 59],
            '60+'   => [60, 200],
        ];

        $labels = array_keys($grupos);
        $dataEvalM = [];
        $dataEvalF = [];
        $dataSesM = [];
        $dataSesF = [];

        $totalGeneral = 0;
        $totalHombresEval = 0;
        $totalMujeresEval = 0;
        $totalSesiones = 0;
        $totalHombresSes = 0;
        $totalMujeresSes = 0;

        $tabla = [];
        $tablaSesiones = [];

        $maxEvalVal = 0;
        $maxEvalGrupo = '';
        $maxSesVal = 0;
        $maxSesGrupo = '';

        foreach ($grupos as $label => [$min, $max]) {
            // -- Evaluaciones --
            $M = $evaluaciones->filter(fn($ev) => $ev->patient && $ev->patient->edad >= $min && $ev->patient->edad <= $max && $ev->patient->sexo_simple === 'M')->count();
            $F = $evaluaciones->filter(fn($ev) => $ev->patient && $ev->patient->edad >= $min && $ev->patient->edad <= $max && $ev->patient->sexo_simple === 'F')->count();

            $subTotalEval = $M + $F;
            $dataEvalM[] = $M;
            $dataEvalF[] = $F;
            $tabla[$label] = ['M' => $M, 'F' => $F, 'TOTAL' => $subTotalEval];
            $totalGeneral += $subTotalEval;
            $totalHombresEval += $M;
            $totalMujeresEval += $F;

            if ($subTotalEval > $maxEvalVal) {
                $maxEvalVal = $subTotalEval;
                $maxEvalGrupo = $label;
            }

            // -- Sesiones --
            $Ms = $sesiones->filter(fn($ses) => $ses->evaluation?->patient && $ses->evaluation->patient->edad >= $min && $ses->evaluation->patient->edad <= $max && $ses->evaluation->patient->sexo_simple === 'M')->count();
            $Fs = $sesiones->filter(fn($ses) => $ses->evaluation?->patient && $ses->evaluation->patient->edad >= $min && $ses->evaluation->patient->edad <= $max && $ses->evaluation->patient->sexo_simple === 'F')->count();

            $subTotalSes = $Ms + $Fs;
            $dataSesM[] = $Ms;
            $dataSesF[] = $Fs;
            $tablaSesiones[$label] = ['M' => $Ms, 'F' => $Fs, 'TOTAL' => $subTotalSes];
            $totalSesiones += $subTotalSes;
            $totalHombresSes += $Ms;
            $totalMujeresSes += $Fs;

            if ($subTotalSes > $maxSesVal) {
                $maxSesVal = $subTotalSes;
                $maxSesGrupo = $label;
            }
        }

        // ================== 3. GRÁFICOS (QuickChart v3) ======================
        $generateChart = function ($title, $labels, $dataM, $dataF) {
            $config = [
                'type' => 'bar',
                'data' => [
                    'labels' => $labels,
                    'datasets' => [
                        ['label' => 'Hombres', 'backgroundColor' => 'rgba(54, 162, 235, 0.7)', 'borderColor' => '#36A2EB', 'borderWidth' => 1, 'data' => $dataM, 'barPercentage' => 0.6],
                        ['label' => 'Mujeres', 'backgroundColor' => 'rgba(255, 99, 132, 0.7)', 'borderColor' => '#FF6384', 'borderWidth' => 1, 'data' => $dataF, 'barPercentage' => 0.6]
                    ]
                ],
                'options' => [
                    'layout' => ['padding' => ['top' => 20, 'bottom' => 10, 'left' => 10, 'right' => 10]],
                    'plugins' => [
                        'title' => ['display' => false],
                        'legend' => ['position' => 'bottom', 'labels' => ['padding' => 20, 'font' => ['size' => 10]]],
                        'datalabels' => ['display' => true, 'anchor' => 'end', 'align' => 'top', 'font' => ['weight' => 'bold', 'size' => 9], 'offset' => 2]
                    ],
                    'scales' => [
                        'y' => ['beginAtZero' => true, 'ticks' => ['precision' => 0], 'grid' => ['color' => 'rgba(200,200,200,0.3)']],
                        'x' => ['grid' => ['display' => false]]
                    ]
                ]
            ];
            return 'https://quickchart.io/chart?v=3&c=' . urlencode(json_encode($config));
        };

        $chartEvalBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($generateChart('Evaluaciones', $labels, $dataEvalM, $dataEvalF)));
        $chartSesBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($generateChart('Sesiones', $labels, $dataSesM, $dataSesF)));

        // ================== 4. OTROS DATOS ======================
        $cantidadAlta = $evaluaciones->where('estado', 'ALTA')->count();
        $cantidadAbandono = $evaluaciones->where('estado', 'ABANDONO')->count();

        // Agrupar diagnósticos
        $diagnosticos = $evaluaciones->groupBy('diagnostico_medico')
            ->map(fn($items) => ['diagnostico' => $items[0]->diagnostico_medico, 'total' => $items->count()])
            ->sortByDesc('total')
            ->values();

        // --------------------------------------------------------
        // CORRECCIÓN: LÓGICA DE EMPATES (MÚLTIPLES DIAGNÓSTICOS)
        // --------------------------------------------------------
        $maxTotal = 0;
        $topDiagnosticos = collect([]);

        if ($diagnosticos->isNotEmpty()) {
            $maxTotal = $diagnosticos->first()['total'];
            // Filtramos todos los que tengan el mismo total máximo
            $topDiagnosticos = $diagnosticos->filter(fn($item) => $item['total'] === $maxTotal);
        }

        $mitad = (int) ceil($diagnosticos->count() / 2);
        $col1 = $diagnosticos->slice(0, $mitad)->values();
        $col2 = $diagnosticos->slice($mitad)->values();

        $meses = [1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril', 5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto', 9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'];

        // Textos de Análisis
        $analisisEval = "Durante este periodo, el grupo etario con mayor afluencia fue el de <strong>{$maxEvalGrupo} años</strong>, representando un punto focal en las evaluaciones. El total acumulado asciende a <strong>{$totalGeneral} pacientes</strong>.";
        $analisisSesion = "Se realizaron un total de <strong>{$totalSesiones} sesiones</strong> terapéuticas. El segmento demográfico más activo corresponde al rango de <strong>{$maxSesGrupo} años</strong>, lo que sugiere una mayor adherencia al tratamiento en este grupo.";

        // Generar PDF
        $pdf = Pdf::loadView('statistics.diagnostico-medico-pdf', compact(
            'tabla',
            'totalGeneral',
            'totalHombresEval',
            'totalMujeresEval',
            'analisisEval',
            'tablaSesiones',
            'totalSesiones',
            'totalHombresSes',
            'totalMujeresSes',
            'analisisSesion',
            'chartEvalBase64',
            'chartSesBase64',
            'cantidadAlta',
            'cantidadAbandono',
            'col1',
            'col2',
            'diagnosticos',
            'topDiagnosticos',
            'maxTotal', // <--- Nuevas variables pasadas
            'meses',
            'mes',
            'year'
        ))->setPaper('letter');

        return $pdf->stream("Reporte_Clinico_{$year}.pdf");
    }
}
