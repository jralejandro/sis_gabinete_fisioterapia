<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte Financiero</title>
    <style>
        /* =========================================
           CONFIGURACIÓN DE PÁGINA (Margenes Fijos)
           ========================================= */
        @page {
            margin: 110px 40px 60px 40px;
        }

        body {
            font-family: 'DejaVu Sans', sans-serif;
            font-size: 11px;
            color: #444;
        }

        /* Header y Footer Fijos */
        header {
            position: fixed;
            top: -90px;
            left: 0;
            right: 0;
            height: 80px;
            border-bottom: 3px solid #0056b3;
            background: #fff;
        }

        /* Estilos Generales */
        .section-title {
            font-size: 16px;
            font-weight: bold;
            color: #2c3e50;
            margin-bottom: 15px;
            border-bottom: 1px solid #ddd;
            padding-bottom: 5px;
        }

        .kpi-table {
            width: 100%;
            border-spacing: 10px;
            margin-bottom: 20px;
        }

        .kpi-box {
            border: 1px solid #e0e0e0;
            border-radius: 6px;
            padding: 15px;
            text-align: center;
            background: #fff;
            box-shadow: 2px 2px 5px rgba(0, 0, 0, 0.03);
        }

        .kpi-label {
            font-size: 9px;
            color: #888;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .kpi-value {
            font-size: 22px;
            font-weight: bold;
            color: #0056b3;
            margin-top: 5px;
        }

        .alert-box {
            background-color: #e3f2fd;
            border-left: 4px solid #1976d2;
            color: #0d47a1;
            padding: 12px;
            border-radius: 4px;
            font-size: 11px;
            margin-bottom: 25px;
        }

        /* Tabla de Datos Financieros */
        .fin-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 10px;
            margin-bottom: 20px;
        }

        .fin-table th {
            background-color: #343a40;
            color: #fff;
            padding: 8px;
            text-transform: uppercase;
            text-align: left;
        }

        .fin-table td {
            padding: 8px;
            border-bottom: 1px solid #eee;
            color: #555;
        }

        .fin-table tr:nth-child(even) {
            background-color: #f9f9f9;
        }

        /* Columnas específicas */
        .col-money {
            text-align: right;
            font-family: monospace;
            font-size: 11px;
        }

        .col-total {
            text-align: right;
            font-family: monospace;
            font-size: 11px;
            font-weight: bold;
            color: #0056b3;
        }

        .total-row td {
            background-color: #e9ecef;
            color: #000;
            font-weight: bold;
            border-top: 2px solid #0056b3;
            padding-top: 10px;
            padding-bottom: 10px;
        }

        .chart-container {
            border: 1px solid #eee;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin-bottom: 20px;
            page-break-inside: avoid;
        }

        .page-break {
            page-break-after: always;
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
                    <div style="font-size: 11px; color: #666;">REPORTE DE INGRESOS Y COSTOS</div>
                </td>
                <td width="20%" align="right">
                    <div style="font-size: 10px; font-weight: bold;">{{ $periodoTexto }}</div>
                    <div style="font-size: 9px; color: #888;">{{ date('d/m/Y') }}</div>
                </td>
            </tr>
        </table>
    </header>



    <div class="section-title">1. Resumen Financiero: {{ $periodoTexto }}</div>

    <table class="kpi-table">
        <tr>
            <td class="kpi-box" width="33%">
                <div class="kpi-label">Ingreso Total</div>
                <div class="kpi-value">{{ number_format($totalGeneral, 2) }} <span style="font-size:12px">Bs</span>
                </div>
            </td>
            <td class="kpi-box" width="33%">
                <div class="kpi-label">Por Evaluaciones</div>
                <div class="kpi-value" style="color:#28a745;">{{ number_format($totalEvalYear, 2) }} <span
                        style="font-size:12px">Bs</span></div>
                <div style="font-size:9px; color:#666; margin-top:2px;">{{ $porcEval }}% del total</div>
            </td>
            <td class="kpi-box" width="33%">
                <div class="kpi-label">Por Sesiones</div>
                <div class="kpi-value" style="color:#17a2b8;">{{ number_format($totalSessYear, 2) }} <span
                        style="font-size:12px">Bs</span></div>
                <div style="font-size:9px; color:#666; margin-top:2px;">{{ $porcSess }}% del total</div>
            </td>
        </tr>
    </table>

    <div class="alert-box">
        <strong>Detalle del Periodo:</strong> {!! $mensaje !!}
    </div>

    <div class="section-title">2. Tendencia de Ingresos</div>
    <div class="chart-container">
        <img src="{{ $chartBase64 }}" style="width: 100%; max-height: 300px;">
    </div>

    <div class="page-break"></div>

    <div class="section-title">3. Detalle {{ $mes ? 'Diario' : 'Mensual' }} de Ingresos</div>

    <table class="fin-table">
        <thead>
            <tr>
                <th width="30%">{{ $labelEje }}</th>
                <th width="20%" style="text-align:right;">Evaluaciones (Bs)</th>
                <th width="20%" style="text-align:right;">Sesiones (Bs)</th>
                <th width="30%" style="text-align:right;">TOTAL (Bs)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($breakdown as $row)
                @if ($row['total'] > 0)
                    <tr>
                        <td style="font-weight:bold;">{{ $row['label'] }}</td>
                        <td class="col-money">{{ number_format($row['eval'], 2) }}</td>
                        <td class="col-money">{{ number_format($row['sess'], 2) }}</td>
                        <td class="col-total">{{ number_format($row['total'], 2) }}</td>
                    </tr>
                @endif
            @endforeach

            <tr class="total-row">
                <td style="text-align:left; padding-left:15px; text-transform:uppercase;">TOTAL DEL PERIODO</td>
                <td class="col-money">{{ number_format($totalEvalYear, 2) }}</td>
                <td class="col-money">{{ number_format($totalSessYear, 2) }}</td>
                <td class="col-total" style="font-size:13px;">{{ number_format($totalGeneral, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>

</html>
