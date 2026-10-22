@extends('reports.plantilla', ['patient' => $session->evaluation->patient])

@section('content')
    {{-- 🔹 DATOS DEL PACIENTE (Compactado) --}}
    <section class="section">
        <h2>Datos del Paciente</h2>
        {{-- Estructura compactada: 2 pares Etiqueta:Valor por fila (4 columnas lógicas) --}}
        <table class="info-table compact-table" style="width: 100%;">
            <tr>
                {{-- Fila 1 --}}
                <td style="width:20%;">Nombre completo:</td>
                <td style="width:30%;">{{ $session->evaluation->patient->nombres }}
                    {{ $session->evaluation->patient->apellido_paterno }}
                    {{ $session->evaluation->patient->apellido_materno }}</td>
                <td style="width:20%;">CI:</td>
                <td style="width:30%;">{{ $session->evaluation->patient->ci }}</td>
            </tr>
            <tr>
                {{-- Fila 2 --}}
                <td>Sexo:</td>
                <td>{{ ucfirst($session->evaluation->patient->sexo) }}</td>
                <td>Fecha de nacimiento:</td>
                <td>{{ \Carbon\Carbon::parse($session->evaluation->patient->fecha_nacimiento)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                {{-- Fila 3 --}}
                <td>Ocupación:</td>
                <td>{{ $session->evaluation->patient->ocupacion ?? '—' }}</td>
                <td>Celular:</td>
                <td>{{ $session->evaluation->patient->celular ?? '—' }}</td>
            </tr>
            <tr>
                {{-- Fila 4 --}}
                <td>Domicilio:</td>
                <td>{{ $session->evaluation->patient->domicilio ?? '—' }}</td>
                <td>Edad:</td>
                <td>{{ $session->evaluation->patient->edad ?? '—' }}</td>
            </tr>
        </table>
    </section>

    <hr style="margin: 15px 0;">

    {{-- 🔹 EVALUACIÓN RELACIONADA (Compactado) --}}
    <section class="section">
        <h2>Evaluación Relacionada</h2>

        {{-- Encabezado con estado de la Evaluación --}}
        <h3 style="margin-bottom: 5px;">
            Ficha del {{ $session->evaluation->fecha?->format('d/m/Y') ?? '—' }}
            {{-- Mostrar estado de forma llamativa --}}
            @php
                $estado = strtoupper(old('estado', $session->evaluation->estado ?? 'SIN ESTADO'));

                $background = match ($estado) {
                    'ACTIVO' => '#4CAF50', // verde
                    'ALTA' => '#2196F3', // azul
                    'ABANDONO' => '#ffeb3b', // amarillo
                    default => '#9E9E9E', // gris
                };

                $color = $estado === 'ABANDONO' ? '#333' : '#fff'; // texto oscuro para amarillo
            @endphp


            <span
                style="background-color: {{ $background }};
             color: {{ $color }};
             padding: 3px 8px;
             border-radius: 4px;
             font-size: 0.8em;
             vertical-align: middle;
             font-weight: bold;
             margin-left: 10px;">
                {{ $estado }}
            </span>
        </h3>

        {{-- Detalles de la evaluación en formato compactado --}}
        <table class="info-table compact-table evaluation-table" style="width: 100%; font-size: 0.9em;">
            <tr>
                <td style="width:20%;">Responsable Eval.:</td>
                <td style="width:30%;">
                    {{-- Usa la variable pasada si existe, si no usa la relación si está definida --}}
                    {{ $session->evaluation->responsableUser->nombres ?? ($responsableNombre ?? '—') }} -
                    {{ $session->evaluation->responsableUser->cargo ?? ($responsableCargo ?? '—') }}
                </td>
                <td style="width:20%;">Diagnóstico Kinesico:</td>
                <td style="width:30%;">{{ $session->evaluation->diagnostico_kinesico ?? '—' }}</td>
            </tr>
            <tr>
                <td>Motivo de consulta:</td>
                <td colspan="3">{{ $session->evaluation->motivo_consulta ?? '—' }}</td>
            </tr>
            <tr>
                <td>Plan de Tratamiento:</td>
                <td colspan="3">{{ $session->evaluation->objetivos_tratamiento ?? '—' }}</td>
            </tr>
        </table>
    </section>

    {{-- 🔸 FICHA DE ALTA (Si existe, manteniendo el formato del código anterior) --}}
    @if ($session->evaluation->dischargeRecord)
        <div
            style="background-color: #e8f5e9; border: 1px solid #4CAF50; padding: 10px; margin-top: 20px; border-radius: 5px;">
            <h3 style="margin-top: 0; margin-bottom: 5px; color: #1b5e20;">
                FICHA DE ALTA MÉDICA
            </h3>
            <table class="info-table compact-table evaluation-table" style="width: 100%; font-size: 0.9em;">
                <tr>
                    <td style="width:20%;">Fecha de Alta:</td>
                    <td style="width:30%;">
                        {{ \Carbon\Carbon::parse($session->evaluation->dischargeRecord->fecha_alta)->format('d/m/Y') }}
                    </td>
                    <td style="width:20%;">Responsable:</td>
                    <td style="width:30%;">
                        {{ $session->evaluation->dischargeRecord->responsableUser
                            ? "{$session->evaluation->dischargeRecord->responsableUser->nombres} {$session->evaluation->dischargeRecord->responsableUser->apellido_paterno} - {$session->evaluation->dischargeRecord->responsableUser->cargo}"
                            : '—' }}
                    </td>
                </tr>
                <tr>
                    <td>Evolución Final:</td>
                    <td colspan="3">{{ $session->evaluation->dischargeRecord->evolucion_alta ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Observaciones:</td>
                    <td colspan="3">{{ $session->evaluation->dischargeRecord->observaciones_alta ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Recomendaciones:</td>
                    <td colspan="3">{{ $session->evaluation->dischargeRecord->recomendaciones_alta ?? '—' }}</td>
                </tr>
            </table>
        </div>
        <hr style="margin: 15px 0;">
    @else
        {{-- Si no hay Alta, solo ponemos el separador normal si existía --}}
        <hr style="margin: 15px 0;">
    @endif

    {{-- 🔹 DETALLES DE LA SESIÓN (Compactado) --}}
    <section class="section" style="border: 1px solid #007bff; padding: 15px; border-radius: 6px; background: #e6f7ff;">
        <h2>Detalles de la Sesión de Fisioterapia</h2>

        <h3 style="margin-top: 0; margin-bottom: 10px; color: #0056b3;">
            SESIÓN DEL {{ \Carbon\Carbon::parse($session->fecha)->format('d/m/Y') }}
        </h3>

        <table class="info-table compact-table session-table" style="width: 100%; font-size: 0.9em;">
            {{-- Responsable de la Sesión --}}
            <tr>
                <td style="width:20%;">Responsable Sesión:</td>
                <td colspan="3">
                    {{-- Usamos la relación $session->responsable si está definida, si no usamos las variables pasadas --}}
                    {{ $session->responsable
                        ? "{$session->responsable->nombres} {$session->responsable->apellido_paterno} {$session->responsable->apellido_materno} - {$session->responsable->cargo}"
                        : ($responsableSesionNombre ?? '—') .
                            (isset($responsableSesionCargo) && $responsableSesionCargo !== '—' ? " - {$responsableSesionCargo}" : '') }}
                </td>
            </tr>

            {{-- Campos principales de la Sesión --}}
            <tr>
                <td>Costo Sesión:</td>
                <td colspan="3">{{ $session->costo_sesion ?? '—' }}</td>
            </tr>
            <tr>
                <td>Trabajo Realizado:</td>
                <td colspan="3">{{ $session->que_se_trabajo ?? '—' }}</td>
            </tr>
            <tr>
                <td>Evolución:</td>
                <td colspan="3">{{ $session->evolucion ?? '—' }}</td>
            </tr>
            <tr>
                <td>Observaciones:</td>
                <td colspan="3">{{ $session->observaciones ?? '—' }}</td>
            </tr>
        </table>
    </section>
@endsection
