<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte Clínico</title>
    <style>
        /* =========================================
           1. CONFIGURACIÓN DE PÁGINA
           ========================================= */
        @page {
            /* Margen: Arriba(110) Der(40) Abajo(60) Izq(40) */
            margin: 110px 40px 60px 40px; 
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #444;
        }

        /* =========================================
           2. HEADER Y FOOTER FIJOS
           ========================================= */
        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            height: 80px;
            border-bottom: 3px solid #0056b3;
            background: #fff;
        }


        /* =========================================
           3. ESTILOS DE CONTENIDO
           ========================================= */
        .page-break { page-break-after: always; }
        
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        /* Estilos Tarjetas */
        .indicator-card {
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            padding: 15px;
            margin-bottom: 20px;
            background: #fff;
        }

        /* =========================================
           4. TABLA PATOLOGÍAS (ESTILO IMAGEN)
           ========================================= */
        .pat-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
        }
        
        /* Encabezado Oscuro */
        .pat-table th {
            background-color: #2d2d2d; /* Gris oscuro */
            color: #ffffff;
            font-weight: bold;
            text-transform: uppercase;
            padding: 8px 10px;
            text-align: left;
        }
        
        .pat-table td {
            padding: 6px 10px;
            border-bottom: 1px solid #f0f0f0;
        }

        .text-diag { color: #555; }
        
        .text-cant { 
            color: #0056b3; 
            font-weight: bold; 
            text-align: right; 
        }

        /* Barras y KPIs */
        .progress-bg { background: #eee; height: 8px; border-radius: 4px; width: 100%; margin-top: 4px; }
        .progress-bar { height: 100%; border-radius: 4px; }

        .data-table { width: 100%; border-collapse: collapse; margin-bottom: 20px; }
        .data-table th { background: #f8f9fa; border: 1px solid #ddd; padding: 6px; }
        .data-table td { border: 1px solid #eee; padding: 6px; text-align: center; }
        .text-left { text-align: left !important; }

        .kpi-box {
            border: 1px solid #ddd; padding: 10px; text-align: center; border-radius: 5px;
        }
    </style>
</head>
<body>

    <header>
        <table width="100%" style="margin-top: 10px;">
            <tr>
                <td width="20%"><img src="{{ public_path('img/fisio.png') }}" height="50"></td>
                <td width="60%" align="center">
                    <div style="font-size: 18px; font-weight: bold; color: #333;">FISIOTecMed</div>
                    <div style="font-size: 11px; color: #666;">REPORTE CLÍNICO ESTADÍSTICO</div>
                </td>
                <td width="20%" align="right">
                    <div style="font-size: 10px; font-weight: bold;">{{ $mes ? strtoupper($meses[$mes]) : 'ANUAL' }} {{ $year }}</div>
                    <div style="font-size: 9px; color: #888;">{{ date('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </header>


    <div class="section-title">1. Análisis de Evaluaciones</div>
    
    <table width="100%" cellspacing="10" style="margin-bottom:20px;">
        <tr>
            <td class="kpi-box">
                <div style="font-size:9px; color:#777;">TOTAL PACIENTES</div>
                <div style="font-size:20px; font-weight:bold; color:#0056b3;">{{ $totalGeneral }}</div>
            </td>
            <td class="kpi-box">
                <div style="font-size:9px; color:#777;">HOMBRES</div>
                <div style="font-size:20px; font-weight:bold; color:#36A2EB;">{{ $totalHombresEval }}</div>
            </td>
            <td class="kpi-box">
                <div style="font-size:9px; color:#777;">MUJERES</div>
                <div style="font-size:20px; font-weight:bold; color:#FF6384;">{{ $totalMujeresEval }}</div>
            </td>
        </tr>
    </table>

    <div style="background:#e3f2fd; padding:10px; border-left:4px solid #0056b3; margin-bottom:20px; font-size:11px;">
        {!! $analisisEval !!}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-left">Rango Etario</th>
                @foreach($tabla as $k => $v) <th>{{ $k }}</th> @endforeach
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-left">Hombres</td>
                @foreach($tabla as $v) <td>{{ $v['M'] }}</td> @endforeach
                <td style="font-weight:bold;">{{ $totalHombresEval }}</td>
            </tr>
            <tr>
                <td class="text-left">Mujeres</td>
                @foreach($tabla as $v) <td>{{ $v['F'] }}</td> @endforeach
                <td style="font-weight:bold;">{{ $totalMujeresEval }}</td>
            </tr>
        </tbody>
    </table>

    <div style="text-align:center; border:1px solid #eee; padding:10px;">
        <img src="{{ $chartEvalBase64 }}" style="width:100%; max-height:300px;">
    </div>

    <div class="page-break"></div>

    <div class="section-title">2. Rendimiento de Sesiones</div>

    <table width="100%" cellspacing="10" style="margin-bottom:20px;">
        <tr>
            <td class="kpi-box">
                <div style="font-size:9px; color:#777;">TOTAL SESIONES</div>
                <div style="font-size:20px; font-weight:bold; color:#0056b3;">{{ $totalSesiones }}</div>
            </td>
            <td class="kpi-box">
                <div style="font-size:9px; color:#777;">HOMBRES</div>
                <div style="font-size:20px; font-weight:bold; color:#36A2EB;">{{ $totalHombresSes }}</div>
            </td>
            <td class="kpi-box">
                <div style="font-size:9px; color:#777;">MUJERES</div>
                <div style="font-size:20px; font-weight:bold; color:#FF6384;">{{ $totalMujeresSes }}</div>
            </td>
        </tr>
    </table>

    <div style="background:#e8f5e9; padding:10px; border-left:4px solid #2e7d32; margin-bottom:20px; font-size:11px;">
        {!! $analisisSesion !!}
    </div>

    <table class="data-table">
        <thead>
            <tr>
                <th class="text-left">Rango Etario</th>
                @foreach($tablaSesiones as $k => $v) <th>{{ $k }}</th> @endforeach
                <th>TOTAL</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td class="text-left">Hombres</td>
                @foreach($tablaSesiones as $v) <td>{{ $v['M'] }}</td> @endforeach
                <td style="font-weight:bold;">{{ $totalHombresSes }}</td>
            </tr>
            <tr>
                <td class="text-left">Mujeres</td>
                @foreach($tablaSesiones as $v) <td>{{ $v['F'] }}</td> @endforeach
                <td style="font-weight:bold;">{{ $totalMujeresSes }}</td>
            </tr>
        </tbody>
    </table>

    <div style="text-align:center; border:1px solid #eee; padding:10px;">
        <img src="{{ $chartSesBase64 }}" style="width:100%; max-height:300px;">
    </div>

    <div class="page-break"></div>

    <div class="section-title">3. Indicadores Clínicos y Patologías</div>

    <div class="indicator-card">
        <h4 style="margin:0 0 10px 0; font-size:12px; color:#555;">CONDICIÓN DE EGRESO</h4>
        <table width="100%">
            <tr>
                <td width="50%" style="padding-right:20px;">
                    <div style="margin-bottom:10px;">
                        <div style="display:flex; justify-content:space-between; font-size:10px; font-weight:bold;">
                            <span style="color:#2ecc71;">ALTA MÉDICA</span>
                            <span>{{ $cantidadAlta }}</span>
                        </div>
                        <div class="progress-bg"><div class="progress-bar" style="width:{{ ($cantidadAlta + $cantidadAbandono) > 0 ? ($cantidadAlta/($cantidadAlta+$cantidadAbandono))*100 : 0 }}%; background:#2ecc71;"></div></div>
                    </div>
                    <div>
                        <div style="display:flex; justify-content:space-between; font-size:10px; font-weight:bold;">
                            <span style="color:#e74c3c;">ABANDONO</span>
                            <span>{{ $cantidadAbandono }}</span>
                        </div>
                        <div class="progress-bg"><div class="progress-bar" style="width:{{ ($cantidadAlta + $cantidadAbandono) > 0 ? ($cantidadAbandono/($cantidadAlta+$cantidadAbandono))*100 : 0 }}%; background:#e74c3c;"></div></div>
                    </div>
                </td>
                <td width="50%" style="vertical-align:top; font-size:10px; color:#666; padding-left:20px; border-left:1px solid #eee;">
                    Total de casos cerrados en el periodo: <strong>{{ $cantidadAlta + $cantidadAbandono }}</strong>.
                </td>
            </tr>
        </table>
    </div>

    <div class="indicator-card" style="padding:0; border:none;">
        <h4 style="margin:0 0 10px 0; font-size:14px; color:#333;">Desglose de Patologías Atendidas</h4>
        
        @if($diagnosticos->count() > 0)
            <div style="background-color:#e3f2fd; color:#0d47a1; padding:10px; border-left:4px solid #1976d2; font-size:11px; margin-bottom:15px;">
                @if($topDiagnosticos->count() > 1)
                    {{-- CASO EMPATE --}}
                    <strong>★ Diagnósticos más frecuentes:</strong> 
                    {{ $topDiagnosticos->pluck('diagnostico')->join(', ', ' y ') }} 
                    con <strong>{{ $maxTotal }}</strong> casos cada uno.
                @else
                    {{-- CASO ÚNICO --}}
                    <strong>★ Diagnóstico más frecuente:</strong> 
                    {{ $topDiagnosticos->first()['diagnostico'] }} 
                    con <strong>{{ $maxTotal }}</strong> casos.
                @endif
            </div>

            <table class="pat-table">
                <thead>
                    <tr>
                        <th width="38%">DIAGNÓSTICO</th>
                        <th width="10%" style="text-align:right;">CANT.</th>
                        <th width="4%" style="background:#fff;"></th>
                        <th width="38%">DIAGNÓSTICO</th>
                        <th width="10%" style="text-align:right;">CANT.</th>
                    </tr>
                </thead>
                <tbody>
                    @php $maxRows = max($col1->count(), $col2->count()); @endphp
                    @for ($i = 0; $i < $maxRows; $i++)
                        @php 
                            $bg = ($i % 2 == 0) ? '#ffffff' : '#f9f9f9';
                            $d1 = $col1->get($i);
                            $d2 = $col2->get($i);
                        @endphp
                        <tr style="background-color: {{ $bg }};">
                            <td class="text-diag">{{ $d1['diagnostico'] ?? '' }}</td>
                            <td class="text-cant">{{ $d1['total'] ?? '' }}</td>
                            <td style="background:#fff;"></td>
                            <td class="text-diag">{{ $d2['diagnostico'] ?? '' }}</td>
                            <td class="text-cant">{{ $d2['total'] ?? '' }}</td>
                        </tr>
                    @endfor
                    
                    <tr>
                        <td colspan="5" style="padding-top:15px;">
                            <div style="background:#f1f3f5; border-left:5px solid #0056b3; padding:10px; display:flex; justify-content:space-between;">
                                <span style="font-weight:bold; color:#333; text-transform:uppercase;">Total General de Casos Atendidos</span>
                                <span style="font-weight:bold; color:#0056b3; font-size:14px;">{{ $diagnosticos->sum('total') }}</span>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        @else
            <p style="text-align:center; padding:20px; color:#999;">No hay registros.</p>
        @endif
    </div>

</body>
</html>