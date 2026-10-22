@extends('reports.plantilla', ['patient' => $evaluation->patient]) {{-- Asegura que se pasa el paciente a la plantilla --}}

@section('content')

    {{-- 🔹 DATOS DEL PACIENTE (Compactado) --}}
    <section class="section">
        <h2>Datos del Paciente</h2>
        {{-- Estructura compactada: 2 pares Etiqueta:Valor por fila (4 columnas lógicas) --}}
        <table class="info-table compact-table" style="width: 100%;">
            <tr>
                {{-- Fila 1 --}}
                <td style="width:20%;">Nombre completo:</td>
                <td style="width:30%;">{{ $evaluation->patient->nombres }} {{ $evaluation->patient->apellido_paterno }}
                    {{ $evaluation->patient->apellido_materno }}</td>
                <td style="width:20%;">CI:</td>
                <td style="width:30%;">{{ $evaluation->patient->ci }}</td>
            </tr>
            <tr>
                {{-- Fila 2 --}}
                <td>Sexo:</td>
                <td>{{ ucfirst($evaluation->patient->sexo) }}</td>
                <td>Fecha de nacimiento:</td>
                <td>{{ \Carbon\Carbon::parse($evaluation->patient->fecha_nacimiento)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                {{-- Fila 3 --}}
                <td>Ocupación:</td>
                <td>{{ $evaluation->patient->ocupacion ?? '—' }}</td>
                <td>Celular:</td>
                <td>{{ $evaluation->patient->celular ?? '—' }}</td>
            </tr>
            <tr>
                {{-- Fila 4 --}}
                <td>Domicilio:</td>
                <td>{{ $evaluation->patient->domicilio ?? '—' }}</td>
                <td>Estado civil:</td>
                <td>{{ $evaluation->patient->estado_civil ?? '—' }}</td>
            </tr>
            {{-- Las filas 5 y 6 (referencia) se pueden omitir si se quiere compactar más, o añadir si son críticas: --}}
            <tr>
                <td>Persona de referencia:</td>
                <td>{{ $evaluation->patient->persona_referencia ?? '—' }}</td>
                <td>Celular referencia:</td>
                <td>{{ $evaluation->patient->celular_referencia ?? '—' }}</td>
            </tr>
        </table>
    </section>

    <hr style="margin: 15px 0;">

    {{-- 🔹 DATOS DE LA EVALUACIÓN (Detallado y Compactado) --}}
    <section class="section">
        <h2>Detalles de la Evaluación</h2>

        <h3 style="margin-bottom: 5px;">
            Evaluación del {{ \Carbon\Carbon::parse($evaluation->fecha)->format('d/m/Y') }}
            {{-- Mostrar estado de forma llamativa --}}
            @php
                $estado = strtoupper(old('estado', $evaluation->estado ?? 'SIN ESTADO'));

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

        <table class="info-table compact-table evaluation-table" style="width: 100%; font-size: 0.9em;">
            {{-- Bloque 1: Responsables y Referencias (4 columnas lógicas) --}}
            <tr>
                <td style="width:20%;">Costo:</td>
                <td style="width:30%;">Bs. {{ old('costo_evaluacion', $evaluation->costo_evaluacion ?? 25) }}</td>
                <td style="width:20%;">Médico de Ref.:</td>
                <td style="width:30%;">{{ old('medico_referencia', $evaluation->medico_referencia ?? '—') }}</td>
            </tr>
            <tr>
                <td style="width:20%;">Responsable:</td>
                <td style="width:30%;">
                    {{ $evaluation->responsableUser->nombres ?? old('responsable_evaluacion', $responsableNombre ?? '—') }}
                    -
                    {{ $evaluation->responsableUser->cargo ?? old('responsable_cargo', $responsableCargo ?? '—') }}
                </td>
                <td style="width:20%;">Centro de Ref.:</td>
                <td style="width:30%;">{{ old('centro_referencia', $evaluation->centro_referencia ?? '—') }}</td>
            </tr>
            <tr>
                <td>Diagnóstico Médico:</td>
                <td>{{ old('diagnostico_medico', $evaluation->diagnostico_medico ?? '—') }}</td>

                {{-- Si el diagnóstico es "Otro", mostrar detalle --}}
                @if (($evaluation->diagnostico_medico ?? '') === 'Otro')
                    <td>Otro (detalle):</td>
                    <td>{{ old('diagnostico_medico_otro', $evaluation->diagnostico_medico_otro ?? '—') }}</td>
                @else
                    <td colspan="2"></td>
                @endif
            </tr>

            {{-- Bloque 2: Constantes Vitales (4 columnas lógicas) --}}
            <tr>
                <td>FC:</td>
                <td>{{ $evaluation->frecuencia_cardiaca ?? '—' }} lpm</td>
                <td>FR:</td>
                <td>{{ $evaluation->frecuencia_respiratoria ?? '—' }} rpm</td>
            </tr>
            <tr>
                <td>PA:</td>
                <td>{{ $evaluation->presion_arterial ?? '—' }}</td>
                <td>Temp:</td>
                <td>{{ $evaluation->temperatura ?? '—' }} °C</td>
            </tr>

            {{-- Bloque 3: Textos Largos (1 columna, abarca todo el ancho) --}}
            <tr>
                <td>Motivo de consulta:</td>
                <td colspan="3">{{ $evaluation->motivo_consulta ?? '—' }}</td>
            </tr>
            <tr>
                <td>Historia enfermedad actual:</td>
                <td colspan="3">{{ $evaluation->historia_enfermedad_actual ?? '—' }}</td>
            </tr>
            <tr>
                <td>Características del dolor:</td>
                <td colspan="3">{{ $evaluation->caracteristicas_dolor ?? '—' }}</td>
            </tr>

            {{-- Bloque 4: Conclusión y Plan (1 columna, abarca todo el ancho) --}}
            <tr>
                <td>Diagnóstico kinésico:</td>
                <td colspan="3"><strong>{{ $evaluation->diagnostico_kinesico ?? '—' }}</strong></td>
            </tr>
            <tr>
                <td>Objetivos del tratamiento:</td>
                <td colspan="3">{{ $evaluation->objetivos_tratamiento ?? '—' }}</td>
            </tr>
            <tr>
                <td>Plan de tratamiento:</td>
                <td colspan="3">{{ $evaluation->plan_tratamiento ?? '—' }}</td>
            </tr>
        </table>
    </section>

    {{-- --- --}}

    {{-- 🔸 FICHA DE ALTA (Si existe, manteniendo el formato del código anterior) --}}
    @if (isset($evaluation->dischargeRecord))
        <div
            style="background-color: #e8f5e9; border: 1px solid #4CAF50; padding: 10px; margin-top: 20px; border-radius: 5px;">
            <h3 style="margin-top: 0; margin-bottom: 5px; color: #1b5e20;">
                FICHA DE ALTA MÉDICA
            </h3>
            <table class="info-table compact-table evaluation-table" style="width: 100%; font-size: 0.9em;">
                <tr>
                    <td style="width:20%;">Fecha de Alta:</td>
                    <td style="width:30%;">
                        {{ \Carbon\Carbon::parse($evaluation->dischargeRecord->fecha_alta)->format('d/m/Y') }}
                    </td>
                    <td style="width:20%;">Responsable:</td>
                    <td style="width:30%;">
                        {{ $evaluation->dischargeRecord->responsableUser
                            ? "{$evaluation->dischargeRecord->responsableUser->nombres} {$evaluation->dischargeRecord->responsableUser->apellido_paterno} - {$evaluation->dischargeRecord->responsableUser->cargo}"
                            : '—' }}
                    </td>
                </tr>
                <tr>
                    <td>Evolución Final:</td>
                    <td colspan="3">{{ $evaluation->dischargeRecord->evolucion_alta ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Observaciones:</td>
                    <td colspan="3">{{ $evaluation->dischargeRecord->observaciones_alta ?? '—' }}</td>
                </tr>
                <tr>
                    <td>Recomendaciones:</td>
                    <td colspan="3">{{ $evaluation->dischargeRecord->recomendaciones_alta ?? '—' }}</td>
                </tr>
            </table>
        </div>
    @endif

    {{-- --- --}}

    {{-- 🔸 SESIONES DE TERAPIA (Compactado) --}}
    <section class="section" style="margin-top: 20px;">
        <h2 style="margin-bottom: 5px;">Sesiones de Fisioterapia</h2>

        @if ($evaluation->therapySessions->count())
            @foreach ($evaluation->therapySessions as $i => $session)
                <div class="session-block"
                    style="border:1px solid #ccc; padding:8px; margin-bottom:5px; border-radius:4px; background:#f9f9f9; font-size: 0.85em;">
                    <strong>Sesión #{{ $i + 1 }}</strong>
                    ({{ \Carbon\Carbon::parse($session->fecha)->format('d/m/Y') }})
                    — Responsable:
                    {{ $session->responsable
                        ? "{$session->responsable->nombres} {$session->responsable->apellido_paterno} {$session->responsable->apellido_materno} - {$session->responsable->cargo}"
                        : '—' }}

                    <ul style="list-style-type:none; padding-left:0; margin-top:5px; margin-bottom:0;">
                        <li><strong>Costo:</strong> {{ $session->costo_sesion ?? '—' }} Bs.</li>
                        <li><strong>Trabajo:</strong> {{ $session->que_se_trabajo ?? '—' }}</li>
                        <li><strong>Evolución:</strong> {{ $session->evolucion ?? '—' }}</li>
                        <li><strong>Observaciones:</strong> {{ $session->observaciones ?? '—' }}</li>
                    </ul>
                </div>
            @endforeach
        @else
            <p style="font-size: 0.9em;"><em>No se registraron sesiones para esta evaluación.</em></p>
        @endif
    </section>

@endsection
