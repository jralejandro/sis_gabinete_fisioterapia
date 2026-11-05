<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\ClubGrupo;
use App\Models\ClubMiembro;
use App\Models\ClubPago;
use App\Models\ClubTest;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ClubMiembroController extends Controller
{
    /** Campos del paciente que se exponen en el módulo del club. */
    private const PATIENT_FIELDS = 'patient:id,ci,complemento,nombres,apellido_paterno,apellido_materno,sexo,fecha_nacimiento,celular';

    private function mensajes(): array
    {
        return [
            'patient_id.required'        => 'Debe seleccionar un paciente.',
            'patient_id.exists'          => 'El paciente seleccionado no existe.',
            'club_grupo_id.required'     => 'Debe seleccionar un grupo.',
            'club_grupo_id.exists'       => 'El grupo seleccionado no existe.',
            'fecha_inscripcion.required' => 'La fecha de inscripción es obligatoria.',
            'fecha_inscripcion.date'     => 'La fecha de inscripción no es válida.',
            'fecha_inscripcion.before_or_equal' => 'La fecha de inscripción no puede ser futura.',
            'pago_matricula.required'    => 'El monto de la matrícula es obligatorio (0 si está becado).',
            'pago_matricula.integer'     => 'La matrícula debe ser un monto entero en Bs.',
            'pago_matricula.min'         => 'La matrícula no puede ser negativa.',
            'monto_primer_mes.integer'   => 'La mensualidad debe ser un monto entero en Bs.',
            'fecha_baja.required'        => 'La fecha de baja es obligatoria.',
            'fecha_baja.after_or_equal'  => 'La fecha de baja no puede ser anterior a la inscripción.',
            'fecha_baja.before_or_equal' => 'La fecha de baja no puede ser futura.',
            'motivo_baja.required'       => 'Indique el motivo de la baja.',
        ];
    }

    /**
     * Agrega los indicadores de pago calculados a un participante.
     */
    private function conIndicadores(ClubMiembro $miembro): array
    {
        $mesActual = now()->format('Y-m');
        $pendientes = $miembro->periodosPendientes();
        $mora = array_values(array_filter($pendientes, fn($p) => $p !== $mesActual));
        $ultimoPago = $miembro->pagos->filter(fn($p) => $p->monto > 0)->sortByDesc('periodo')->first();

        return array_merge($miembro->toArray(), [
            'pagado_mes_actual'   => $miembro->estado === ClubMiembro::ESTADO_ACTIVO
                ? !in_array($mesActual, $pendientes, true)
                : null,
            'meses_pendientes'    => count($pendientes),
            'meses_mora'          => count($mora),
            'periodos_pendientes' => $pendientes,
            'ultimo_periodo_pagado' => $ultimoPago?->periodo?->format('Y-m'),
        ]);
    }

    /**
     * Marca como "No asistió / Permiso (0 Bs)" todos los meses no pagados anteriores al mes de reincorporación,
     * para que el participante no arrastre deuda por los meses que estuvo ausente.
     */
    private function eximirMesesAusentesHasta(ClubMiembro $miembro, Carbon $mesReincorporacion, int $userId): void
    {
        $inicio = $miembro->fecha_inscripcion->copy()->startOfMonth();
        $finExencion = $mesReincorporacion->copy()->startOfMonth()->subMonth();

        if ($finExencion->lessThan($inicio)) {
            return;
        }

        $yaRegistrados = ClubPago::where('club_miembro_id', $miembro->id)
            ->pluck('periodo')
            ->map(fn($p) => Carbon::parse($p)->format('Y-m'))
            ->flip();

        for ($cursor = $inicio->copy(); $cursor->lessThanOrEqualTo($finExencion); $cursor->addMonth()) {
            $ym = $cursor->format('Y-m');
            if (!$yaRegistrados->has($ym)) {
                ClubPago::create([
                    'club_miembro_id' => $miembro->id,
                    'periodo'         => $cursor->toDateString(),
                    'monto'           => 0,
                    'fecha_pago'      => now()->toDateString(),
                    'observaciones'   => 'No asistió / Permiso (Ausencia sin cargo)',
                    'created_by'      => $userId,
                ]);
            }
        }
    }

    /**
     * Validación de cupo disponible en un grupo.
     */
    private function validarCupo(ClubGrupo $grupo, ?int $ignorarMiembroId = null): ?string
    {
        if (!$grupo->estado) {
            return "El {$grupo->nombre} está inactivo.";
        }

        if ($grupo->cupo_maximo) {
            $ocupados = ClubMiembro::where('club_grupo_id', $grupo->id)
                ->where('estado', ClubMiembro::ESTADO_ACTIVO)
                ->when($ignorarMiembroId, fn($q) => $q->where('id', '!=', $ignorarMiembroId))
                ->count();

            if ($ocupados >= $grupo->cupo_maximo) {
                return "El {$grupo->nombre} ya alcanzó su cupo máximo ({$grupo->cupo_maximo} participantes).";
            }
        }

        return null;
    }

    /**
     * Listado de participantes + resumen (KPIs).
     * ?estado=ACTIVO|RETIRADO|TODOS (por defecto ACTIVO)
     */
    public function index(Request $request)
    {
        $estado = strtoupper($request->query('estado', ClubMiembro::ESTADO_ACTIVO));

        $miembros = ClubMiembro::with([self::PATIENT_FIELDS, 'grupo:id,nombre,horario,dias,orden', 'pagos:id,club_miembro_id,periodo'])
            ->when(in_array($estado, [ClubMiembro::ESTADO_ACTIVO, ClubMiembro::ESTADO_RETIRADO], true),
                fn($q) => $q->where('estado', $estado))
            ->orderByDesc('id')
            ->get();

        $data = $miembros->map(fn($m) => $this->conIndicadores($m))->values();

        // KPIs del mes actual
        $inicioMes = now()->startOfMonth()->toDateString();
        $finMes = now()->endOfMonth()->toDateString();

        $mensualidadesMes = (int) ClubPago::whereBetween('fecha_pago', [$inicioMes, $finMes])->sum('monto');
        $matriculasMes = (int) ClubMiembro::whereBetween('fecha_inscripcion', [$inicioMes, $finMes])->sum('pago_matricula');

        $activos = ClubMiembro::with('pagos:id,club_miembro_id,periodo')
            ->where('estado', ClubMiembro::ESTADO_ACTIVO)
            ->get();

        $mesActual = now()->format('Y-m');
        $enMora = $activos->filter(function ($m) use ($mesActual) {
            return count(array_filter($m->periodosPendientes(), fn($p) => $p !== $mesActual)) > 0;
        })->count();
        $alDiaMes = $activos->filter(fn($m) => !in_array($mesActual, $m->periodosPendientes(), true))->count();

        return response()->json([
            'resumen' => [
                'activos'            => $activos->count(),
                'retirados'          => ClubMiembro::where('estado', ClubMiembro::ESTADO_RETIRADO)->count(),
                'pagaron_mes_actual' => $alDiaMes,
                'en_mora'            => $enMora,
                'mensualidades_mes'  => $mensualidadesMes,
                'matriculas_mes'     => $matriculasMes,
                'recaudado_mes'      => $mensualidadesMes + $matriculasMes,
            ],
            'miembros' => $data,
        ]);
    }

    /**
     * Ficha completa de una inscripción.
     */
    public function show(ClubMiembro $miembro)
    {
        $miembro->load([
            self::PATIENT_FIELDS,
            'grupo',
            'pagos.creador:id,nombres,apellido_paterno',
            'tests.creador:id,nombres,apellido_paterno',
            'creador:id,nombres,apellido_paterno',
        ]);

        $historial = ClubMiembro::with('grupo:id,nombre,horario')
            ->where('patient_id', $miembro->patient_id)
            ->where('id', '!=', $miembro->id)
            ->orderByDesc('fecha_inscripcion')
            ->get(['id', 'club_grupo_id', 'fecha_inscripcion', 'fecha_baja', 'estado', 'motivo_baja', 'pago_matricula']);

        return response()->json(array_merge($this->conIndicadores($miembro), [
            'historial_inscripciones' => $historial,
        ]));
    }

    /**
     * Inscribir a un paciente en el club.
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'patient_id'        => 'required|integer|exists:patients,id',
            'club_grupo_id'     => 'required|integer|exists:club_grupos,id',
            'fecha_inscripcion' => 'required|date|before_or_equal:today',
            'pago_matricula'    => 'required|integer|min:0|max:10000',
            'observaciones'     => 'nullable|string|max:2000',
            'pagar_primer_mes'  => 'nullable|boolean',
            'monto_primer_mes'  => 'nullable|integer|min:0|max:10000',
        ], $this->mensajes());

        $yaActivo = ClubMiembro::where('patient_id', $data['patient_id'])
            ->where('estado', ClubMiembro::ESTADO_ACTIVO)
            ->with('grupo:id,nombre')
            ->first();

        if ($yaActivo) {
            return response()->json([
                'message' => "Este paciente ya está inscrito y activo en el {$yaActivo->grupo->nombre}.",
                'errors'  => ['patient_id' => ['El paciente ya tiene una inscripción activa en el club.']],
            ], 422);
        }

        $grupo = ClubGrupo::findOrFail($data['club_grupo_id']);
        if ($error = $this->validarCupo($grupo)) {
            return response()->json([
                'message' => $error,
                'errors'  => ['club_grupo_id' => [$error]],
            ], 422);
        }

        // Si el paciente ya tenía un expediente RETIRADO en el club, reutilizamos ese mismo expediente
        // para conservar todos sus estudios/tests e historial de pagos en una sola ficha.
        $expedientePrevio = ClubMiembro::where('patient_id', $data['patient_id'])
            ->where('estado', ClubMiembro::ESTADO_RETIRADO)
            ->orderByDesc('id')
            ->first();

        if ($expedientePrevio) {
            $miembro = DB::transaction(function () use ($expedientePrevio, $data, $request) {
                $fechaReinc = Carbon::parse($data['fecha_inscripcion']);
                $this->eximirMesesAusentesHasta($expedientePrevio, $fechaReinc, $request->user()->id);

                $notaReinc = 'Reincorporado el ' . $fechaReinc->format('d/m/Y');
                if (!empty($data['observaciones'])) {
                    $notaReinc .= ' — ' . $data['observaciones'];
                }
                $obsFinal = $expedientePrevio->observaciones
                    ? $expedientePrevio->observaciones . "\n" . $notaReinc
                    : $notaReinc;

                $expedientePrevio->update([
                    'club_grupo_id'  => $data['club_grupo_id'],
                    'estado'         => ClubMiembro::ESTADO_ACTIVO,
                    'fecha_baja'     => null,
                    'motivo_baja'    => null,
                    'pago_matricula' => $expedientePrevio->pago_matricula + (int) $data['pago_matricula'],
                    'observaciones'  => $obsFinal,
                ]);

                if (!empty($data['pagar_primer_mes'])) {
                    $periodoMes = $fechaReinc->copy()->startOfMonth()->toDateString();
                    ClubPago::updateOrCreate(
                        [
                            'club_miembro_id' => $expedientePrevio->id,
                            'periodo'         => $periodoMes,
                        ],
                        [
                            'monto'         => $data['monto_primer_mes'] ?? 60,
                            'fecha_pago'    => $data['fecha_inscripcion'],
                            'observaciones' => 'Pagado al momento de su reincorporación.',
                            'created_by'    => $request->user()->id,
                        ]
                    );
                }

                return $expedientePrevio;
            });

            return response()->json([
                'message' => 'Participante reincorporado exitosamente en su mismo expediente.',
                'miembro' => $miembro->load([self::PATIENT_FIELDS, 'grupo']),
            ], 200);
        }

        $miembro = DB::transaction(function () use ($data, $request) {
            $miembro = ClubMiembro::create([
                'patient_id'        => $data['patient_id'],
                'club_grupo_id'     => $data['club_grupo_id'],
                'fecha_inscripcion' => $data['fecha_inscripcion'],
                'pago_matricula'    => $data['pago_matricula'],
                'observaciones'     => $data['observaciones'] ?? null,
                'estado'            => ClubMiembro::ESTADO_ACTIVO,
                'created_by'        => $request->user()->id,
            ]);

            if (!empty($data['pagar_primer_mes'])) {
                ClubPago::create([
                    'club_miembro_id' => $miembro->id,
                    'periodo'         => Carbon::parse($data['fecha_inscripcion'])->startOfMonth()->toDateString(),
                    'monto'           => $data['monto_primer_mes'] ?? 60,
                    'fecha_pago'      => $data['fecha_inscripcion'],
                    'observaciones'   => 'Pagado al momento de la inscripción.',
                    'created_by'      => $request->user()->id,
                ]);
            }

            return $miembro;
        });

        return response()->json([
            'message' => 'Participante inscrito correctamente en el club.',
            'miembro' => $miembro->load([self::PATIENT_FIELDS, 'grupo']),
        ], 201);
    }

    /**
     * Editar inscripción (cambio de grupo, fecha, matrícula u observaciones).
     */
    public function update(Request $request, ClubMiembro $miembro)
    {
        $data = $request->validate([
            'club_grupo_id'     => 'required|integer|exists:club_grupos,id',
            'fecha_inscripcion' => 'required|date|before_or_equal:today',
            'pago_matricula'    => 'required|integer|min:0|max:10000',
            'observaciones'     => 'nullable|string|max:2000',
        ], $this->mensajes());

        if ((int) $data['club_grupo_id'] !== (int) $miembro->club_grupo_id && $miembro->estado === ClubMiembro::ESTADO_ACTIVO) {
            $grupo = ClubGrupo::findOrFail($data['club_grupo_id']);
            if ($error = $this->validarCupo($grupo, $miembro->id)) {
                return response()->json([
                    'message' => $error,
                    'errors'  => ['club_grupo_id' => [$error]],
                ], 422);
            }
        }

        // No permitir mover la inscripción después de un mes ya pagado
        $primerPago = $miembro->pagos()->orderBy('periodo')->first();
        if ($primerPago && Carbon::parse($data['fecha_inscripcion'])->startOfMonth()->greaterThan($primerPago->periodo)) {
            $mes = $primerPago->periodo->format('m/Y');
            return response()->json([
                'message' => "Existe un pago registrado para {$mes}; la fecha de inscripción no puede ser posterior a ese mes.",
                'errors'  => ['fecha_inscripcion' => ["Debe ser igual o anterior a {$mes}."]],
            ], 422);
        }

        $miembro->update($data);

        return response()->json([
            'message' => 'Inscripción actualizada correctamente.',
            'miembro' => $miembro->load([self::PATIENT_FIELDS, 'grupo']),
        ]);
    }

    /**
     * Dar de baja (retiro) a un participante.
     */
    public function baja(Request $request, ClubMiembro $miembro)
    {
        if ($miembro->estado !== ClubMiembro::ESTADO_ACTIVO) {
            return response()->json(['message' => 'Esta inscripción ya se encuentra dada de baja.'], 422);
        }

        $data = $request->validate([
            'fecha_baja'  => 'required|date|after_or_equal:' . $miembro->fecha_inscripcion->toDateString() . '|before_or_equal:today',
            'motivo_baja' => 'required|string|max:255',
        ], $this->mensajes());

        $miembro->update([
            'estado'      => ClubMiembro::ESTADO_RETIRADO,
            'fecha_baja'  => $data['fecha_baja'],
            'motivo_baja' => $data['motivo_baja'],
        ]);

        return response()->json([
            'message' => 'El participante fue dado de baja del club.',
            'miembro' => $miembro,
        ]);
    }

    /**
     * Reincorporar / Habilitar a un participante retirado en su mismo expediente.
     * PATCH /api/club/miembros/{miembro}/reactivar
     */
    public function reactivar(Request $request, ClubMiembro $miembro)
    {
        if ($miembro->estado === ClubMiembro::ESTADO_ACTIVO) {
            return response()->json(['message' => 'El participante ya se encuentra activo en el club.'], 422);
        }

        $data = $request->validate([
            'club_grupo_id'         => 'required|integer|exists:club_grupos,id',
            'fecha_reincorporacion' => 'required|date|after_or_equal:' . $miembro->fecha_inscripcion->toDateString() . '|before_or_equal:today',
            'pago_matricula'        => 'required|integer|min:0|max:10000',
            'pagar_mes_actual'      => 'nullable|boolean',
            'monto_mes_actual'      => 'nullable|integer|min:0|max:10000',
            'observaciones'         => 'nullable|string|max:1000',
        ], $this->mensajes());

        $grupo = ClubGrupo::findOrFail($data['club_grupo_id']);
        if ($error = $this->validarCupo($grupo, $miembro->id)) {
            return response()->json([
                'message' => $error,
                'errors'  => ['club_grupo_id' => [$error]],
            ], 422);
        }

        DB::transaction(function () use ($miembro, $data, $request) {
            $fechaReinc = Carbon::parse($data['fecha_reincorporacion']);

            // Marcar como "No asistió / Permiso (0 Bs)" todos los meses en los que estuvo ausente
            $this->eximirMesesAusentesHasta($miembro, $fechaReinc, $request->user()->id);

            $notaReinc = 'Reincorporado el ' . $fechaReinc->format('d/m/Y');
            if (!empty($data['observaciones'])) {
                $notaReinc .= ' — ' . $data['observaciones'];
            }
            $obsFinal = $miembro->observaciones
                ? $miembro->observaciones . "\n" . $notaReinc
                : $notaReinc;

            $miembro->update([
                'club_grupo_id'  => $data['club_grupo_id'],
                'estado'         => ClubMiembro::ESTADO_ACTIVO,
                'fecha_baja'     => null,
                'motivo_baja'    => null,
                'pago_matricula' => $miembro->pago_matricula + (int) $data['pago_matricula'],
                'observaciones'  => $obsFinal,
            ]);

            if (!empty($data['pagar_mes_actual'])) {
                $periodoMes = $fechaReinc->copy()->startOfMonth()->toDateString();
                ClubPago::updateOrCreate(
                    [
                        'club_miembro_id' => $miembro->id,
                        'periodo'         => $periodoMes,
                    ],
                    [
                        'monto'         => $data['monto_mes_actual'] ?? 60,
                        'fecha_pago'    => $data['fecha_reincorporacion'],
                        'observaciones' => 'Pagado al momento de su reincorporación.',
                        'created_by'    => $request->user()->id,
                    ]
                );
            }
        });

        return response()->json([
            'message' => 'Participante habilitado y reincorporado exitosamente al club.',
            'miembro' => $miembro->fresh([self::PATIENT_FIELDS, 'grupo']),
        ]);
    }

    /**
     * Eliminar una inscripción registrada por error (con sus pagos y tests).
     */
    public function destroy(ClubMiembro $miembro)
    {
        DB::transaction(function () use ($miembro) {
            foreach ($miembro->tests as $test) {
                \Illuminate\Support\Facades\Storage::disk(ClubTest::DISK)->delete($test->archivo);
            }
            $miembro->delete();
        });

        return response()->json(['message' => 'Inscripción eliminada correctamente.']);
    }
}

