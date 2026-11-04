<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClubMiembro;
use App\Models\ClubPago;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubPagoController extends Controller
{
    /**
     * Matriz anual de mensualidades.
     * GET /api/club/pagos?anio=2026&grupo=ID
     *
     * Estado de cada mes por participante:
     *  - pagado:      existe el pago
     *  - pendiente:   debía pagarlo (mes vencido o el mes actual) y no lo pagó
     *  - futuro:      mes posterior al actual (todavía no corresponde)
     *  - no_inscrito: anterior a su inscripción
     *  - retirado:    posterior a su baja
     */
    public function matriz(Request $request)
    {
        $anio = (int) $request->query('anio', now()->year);
        $grupoId = $request->query('grupo');

        $inicioAnio = Carbon::create($anio, 1, 1)->startOfDay();
        $finAnio = Carbon::create($anio, 12, 31)->endOfDay();
        $mesActual = now()->startOfMonth();

        $miembros = ClubMiembro::with([
            'patient:id,ci,complemento,nombres,apellido_paterno,apellido_materno',
            'grupo:id,nombre,horario,orden',
            'pagos' => fn($q) => $q->whereBetween('periodo', [$inicioAnio->toDateString(), $finAnio->toDateString()])
                ->with('creador:id,nombres,apellido_paterno'),
        ])
            ->where('fecha_inscripcion', '<=', $finAnio->toDateString())
            ->where(fn($q) => $q->whereNull('fecha_baja')->orWhere('fecha_baja', '>=', $inicioAnio->toDateString()))
            ->when($grupoId, fn($q) => $q->where('club_grupo_id', $grupoId))
            ->get();

        $filas = $miembros->map(function (ClubMiembro $m) use ($anio, $mesActual) {
            $pagosPorMes = $m->pagos->keyBy(fn($p) => (int) $p->periodo->format('n'));
            $inicio = $m->fecha_inscripcion->copy()->startOfMonth();
            $baja = $m->estado === ClubMiembro::ESTADO_RETIRADO && $m->fecha_baja
                ? $m->fecha_baja->copy()->startOfMonth()
                : null;

            $meses = [];
            $totalPagado = 0;
            $pendientes = 0;

            for ($mes = 1; $mes <= 12; $mes++) {
                $periodo = Carbon::create($anio, $mes, 1);
                $pago = $pagosPorMes->get($mes);

                if ($pago) {
                    if ((int) $pago->monto === 0) {
                        $estado = 'exento';
                    } else {
                        $estado = 'pagado';
                        $totalPagado += $pago->monto;
                    }
                } elseif ($periodo->lessThan($inicio)) {
                    $estado = 'no_inscrito';
                } elseif ($baja && $periodo->greaterThan($baja)) {
                    $estado = 'retirado';
                } elseif ($periodo->greaterThan($mesActual)) {
                    $estado = 'futuro';
                } else {
                    $estado = 'pendiente';
                    $pendientes++;
                }

                $meses[] = [
                    'mes'     => $mes,
                    'periodo' => $periodo->format('Y-m'),
                    'estado'  => $estado,
                    'pago'    => $pago ? [
                        'id'          => $pago->id,
                        'monto'       => $pago->monto,
                        'fecha_pago'  => $pago->fecha_pago?->format('Y-m-d'),
                        'nro_recibo'  => $pago->nro_recibo,
                        'observaciones' => $pago->observaciones,
                        'registrado_por' => $pago->creador
                            ? trim($pago->creador->nombres . ' ' . $pago->creador->apellido_paterno)
                            : null,
                    ] : null,
                ];
            }

            return [
                'id'                => $m->id,
                'estado'            => $m->estado,
                'fecha_inscripcion' => $m->fecha_inscripcion->format('Y-m-d'),
                'fecha_baja'        => $m->fecha_baja?->format('Y-m-d'),
                'pago_matricula'    => (int) $m->pago_matricula,
                'patient'           => $m->patient,
                'grupo'             => $m->grupo,
                'meses'             => $meses,
                'total_pagado'      => $totalPagado,
                'meses_pendientes'  => $pendientes,
            ];
        })
            ->sortBy([
                fn($a, $b) => ($a['grupo']['orden'] ?? 0) <=> ($b['grupo']['orden'] ?? 0),
                fn($a, $b) => strcmp(
                    ($a['patient']['apellido_paterno'] ?? '') . ($a['patient']['nombres'] ?? ''),
                    ($b['patient']['apellido_paterno'] ?? '') . ($b['patient']['nombres'] ?? '')
                ),
            ])
            ->values();

        $totalesPorMes = [];
        for ($mes = 1; $mes <= 12; $mes++) {
            $totalesPorMes[$mes] = $filas->sum(fn($f) => $f['meses'][$mes - 1]['pago']['monto'] ?? 0);
        }

        $anioMin = ClubMiembro::min('fecha_inscripcion');

        return response()->json([
            'anio'            => $anio,
            'anios_disponibles' => range($anioMin ? Carbon::parse($anioMin)->year : now()->year, now()->year + 1),
            'filas'           => $filas,
            'totales_por_mes' => $totalesPorMes,
            'total_anual'     => array_sum($totalesPorMes),
        ]);
    }

    /**
     * Registrar el pago de uno o varios meses.
     * POST /api/club/miembros/{miembro}/pagos
     */
    public function store(Request $request, ClubMiembro $miembro)
    {
        $data = $request->validate([
            'periodos'          => 'nullable|array|max:24',
            'periodos.*'        => ['required', 'string', 'regex:/^\d{4}-(0[1-9]|1[0-2])$/', 'distinct'],
            'monto'             => 'required|integer|min:0|max:10000',
            'fecha_pago'        => 'required|date|before_or_equal:today',
            'nro_recibo'        => 'nullable|string|max:50',
            'observaciones'     => 'nullable|string|max:255',
            'eximir_anteriores' => 'nullable|boolean',
            'cobrar_matricula'  => 'nullable|boolean',
            'monto_matricula'   => 'nullable|integer|min:1|max:10000',
        ], [
            'periodos.*.regex'      => 'El mes seleccionado no tiene un formato válido.',
            'periodos.*.distinct'   => 'Hay meses repetidos en la selección.',
            'monto.required'        => 'El monto es obligatorio.',
            'monto.integer'         => 'El monto debe ser un número entero en Bs.',
            'fecha_pago.required'   => 'La fecha de pago es obligatoria.',
            'fecha_pago.before_or_equal' => 'La fecha de pago no puede ser futura.',
        ]);

        $cobrarMatricula = !empty($data['cobrar_matricula']);
        $montoMatricula = $cobrarMatricula ? (int) ($data['monto_matricula'] ?? 50) : 0;
        $periodosRaw = $data['periodos'] ?? [];

        if (empty($periodosRaw) && !$cobrarMatricula) {
            return $this->errorPeriodo('Seleccione al menos un mes a pagar o marque la regularización de matrícula.');
        }

        $inicio = $miembro->fecha_inscripcion->copy()->startOfMonth();
        $limiteAdelanto = now()->startOfMonth()->addMonths(12);
        $baja = $miembro->estado === ClubMiembro::ESTADO_RETIRADO && $miembro->fecha_baja
            ? $miembro->fecha_baja->copy()->startOfMonth()
            : null;

        $periodos = collect($periodosRaw)->map(fn($p) => Carbon::createFromFormat('Y-m-d', $p . '-01')->startOfDay());

        foreach ($periodos as $p) {
            $mes = $p->format('m/Y');
            if ($p->lessThan($inicio)) {
                return $this->errorPeriodo("{$mes} es anterior a la inscripción del participante.");
            }
            if ($baja && $p->greaterThan($baja)) {
                return $this->errorPeriodo("{$mes} es posterior a la baja del participante.");
            }
            if ($p->greaterThan($limiteAdelanto)) {
                return $this->errorPeriodo("Solo se permite adelantar hasta 12 meses ({$mes} excede el límite).");
            }
        }

        // Solo bloquear si ya tienen un pago real (> 0 Bs), permitiendo actualizar un mes exento (0 Bs)
        if ($periodos->isNotEmpty()) {
            $yaPagados = ClubPago::where('club_miembro_id', $miembro->id)
                ->whereIn('periodo', $periodos->map->toDateString())
                ->where('monto', '>', 0)
                ->get()
                ->map(fn($p) => $p->periodo->format('m/Y'));

            if ($yaPagados->isNotEmpty()) {
                return $this->errorPeriodo('Estos meses ya están pagados: ' . $yaPagados->implode(', ') . '.');
            }
        }

        $creados = DB::transaction(function () use ($periodos, $data, $miembro, $request, $inicio, $cobrarMatricula, $montoMatricula) {
            if ($cobrarMatricula) {
                $miembro->update([
                    'pago_matricula' => $miembro->pago_matricula + $montoMatricula,
                ]);
            }

            if ($periodos->isEmpty()) {
                return collect();
            }

            // Si se solicita eximir los meses anteriores no asistidos (ej. no vino el mes 2 y vuelve a pagar en el mes 3)
            if (!empty($data['eximir_anteriores'])) {
                $minPeriodoPagado = $periodos->min();
                $finExencion = $minPeriodoPagado->copy()->subMonth();
                if ($finExencion->greaterThanOrEqualTo($inicio)) {
                    $existentes = ClubPago::where('club_miembro_id', $miembro->id)
                        ->pluck('periodo')
                        ->map(fn($p) => Carbon::parse($p)->format('Y-m'))
                        ->flip();

                    for ($cursor = $inicio->copy(); $cursor->lessThanOrEqualTo($finExencion); $cursor->addMonth()) {
                        $ym = $cursor->format('Y-m');
                        if (!$existentes->has($ym)) {
                            ClubPago::create([
                                'club_miembro_id' => $miembro->id,
                                'periodo'         => $cursor->toDateString(),
                                'monto'           => 0,
                                'fecha_pago'      => $data['fecha_pago'],
                                'observaciones'   => 'No asistió / Permiso (Ausencia sin cargo)',
                                'created_by'      => $request->user()->id,
                            ]);
                        }
                    }
                }
            }

            $obsDefault = (int) $data['monto'] === 0
                ? ($data['observaciones'] ?? 'No asistió / Permiso (Ausencia sin cargo)')
                : ($data['observaciones'] ?? null);

            return $periodos->sort()->map(fn($p) => ClubPago::updateOrCreate(
                [
                    'club_miembro_id' => $miembro->id,
                    'periodo'         => $p->toDateString(),
                ],
                [
                    'monto'         => $data['monto'],
                    'fecha_pago'    => $data['fecha_pago'],
                    'nro_recibo'    => $data['nro_recibo'] ?? null,
                    'observaciones' => $obsDefault,
                    'created_by'    => $request->user()->id,
                ]
            ))->values();
        });

        $cantidad = $creados->count();
        $totalMensualidades = $cantidad * (int) $data['monto'];
        $totalGeneral = $totalMensualidades + $montoMatricula;

        if ($cobrarMatricula && $cantidad === 0) {
            $msg = "Matrícula regularizada correctamente ({$montoMatricula} Bs).";
        } elseif ($cobrarMatricula && $cantidad > 0) {
            $msg = "Matrícula ({$montoMatricula} Bs) y {$cantidad} mensualidad(es) registradas ({$totalGeneral} Bs en total).";
        } elseif ((int) $data['monto'] === 0) {
            $msg = $cantidad === 1
                ? 'Mes registrado como Permiso / No asistió (0 Bs).'
                : "Se registraron {$cantidad} meses como Permiso / No asistió (0 Bs).";
        } else {
            $msg = $cantidad === 1
                ? "Pago registrado correctamente ({$totalMensualidades} Bs)."
                : "Se registraron {$cantidad} mensualidades ({$totalMensualidades} Bs).";
        }

        return response()->json([
            'message' => $msg,
            'pagos'   => $creados,
        ], 201);
    }

    /**
     * Anular un pago registrado por error.
     */
    public function destroy(ClubPago $pago)
    {
        $mes = $pago->periodo->format('m/Y');
        $pago->delete();

        return response()->json(['message' => "Se anuló el pago de {$mes}."]);
    }

    private function errorPeriodo(string $mensaje)
    {
        return response()->json([
            'message' => $mensaje,
            'errors'  => ['periodos' => [$mensaje]],
        ], 422);
    }
}

