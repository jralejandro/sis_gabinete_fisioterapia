<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Reporte Clínico - {{ $patient->nombres }} {{ $patient->apellido_paterno }}</title>

    <!-- ================= ESTILOS GENERALES ================= -->
<style>
    /* ================= ESTILOS GENERALES ================= */
    body {
        font-family: 'DejaVu Sans', sans-serif;
        font-size: 11.5px; /* Ligeramente más pequeño para compactar */
        color: #343a40; /* Gris oscuro para mejor contraste */
        margin: 20px; /* Margen un poco más pequeño */
        line-height: 1.5;
    }

    @page {
        size: letter;
        margin: 20px;
    }

    /* ================= UTILIDADES ================= */
    .no-border-table td,
    .no-border-table th,
    .no-border-table tr {
        border: none !important;
        padding: 0;
    }

    /* ================= HEADER ================= */
    header {
        margin-bottom: 20px;
    }

    /* Línea divisoria moderna */
    .header-line {
        width: 100%;
        height: 3px; /* Más delgado y sutil */
        background: linear-gradient(to right, #0056b3, #007bff); /* Gradiente */
        margin-top: 8px;
        margin-bottom: 12px;
    }

    /* ================= FOOTER ================= */
    footer {
        margin-top: 30px;
        font-size: 10px;
        color: #6c757d;
        border-top: 1px solid #dee2e6; /* Separador sutil */
        padding-top: 5px;
    }

    /* ================= TITULOS ================= */

    h2 {
        background: #e9ecef; /* Fondo muy claro */
        color: #004085; /* Azul oscuro corporativo */
        padding: 8px 15px;
        border-radius: 3px;
        font-size: 17px;
        border-left: 5px solid #007bff; /* Línea de acento azul */
        margin-bottom: 12px;
        margin-top: 20px;
    }

    h3 {
        margin-top: 18px;
        margin-bottom: 6px; /* Reducido */
        font-size: 14px;
        color: #212529; /* Casi negro */
        border-bottom: 1px solid #adb5bd; /* Gris medio */
        padding-bottom: 3px;
        font-weight: 600;
    }

    /* ================= TABLAS GENERALES ================= */
    table {
        width: 100%;
        border-collapse: collapse;
        margin-bottom: 15px;
        font-size: 11.5px;
    }

    table th {
        background: #f8f9fa; /* Gris muy claro */
        color: #495057;
        font-weight: 700;
        padding: 7px;
        border: 1px solid #e9ecef; /* Borde muy claro */
        text-align: left;
        word-wrap: break-word;
    }

    table td {
        padding: 5px 7px; /* Padding ajustado */
        border: 1px solid #e9ecef;
        vertical-align: top;
        word-wrap: break-word;
        max-width: 150px;
    }

    /* Tabla de Información (compacta y mejor definida) */
    .info-table td:first-child {
        width: 25%; /* Ligeramente más estrecho para el label */
        background: #f2f4f7; /* Gris de acento */
        font-weight: bold;
        color: #495057;
    }

    .info-table td:last-child {
        width: 75%;
        background: white;
    }

    /* Clases para tablas compactas de 4 columnas (de la respuesta anterior) */
    .info-table.compact-table tr td:nth-child(odd) {
        width: 20%;
        background: #f2f4f7;
        font-weight: bold;
        color: #495057;
    }
    .info-table.compact-table tr td:nth-child(even) {
        width: 30%;
        background: white;
        font-weight: normal;
    }
    .info-table.compact-table td[colspan="3"] {
        width: 80% !important;
        background: white !important;
        font-weight: normal !important;
    }
    
    /* Estilo para los antecedentes para que se vea más limpio */
    .antecedentes-table td {
        background: #ffffff !important;
        border: none !important;
        padding: 5px 0 5px 10px;
        vertical-align: top;
        line-height: 1.4;
    }


    /* Separador suave */
    hr {
        border: none;
        border-top: 1px dashed #ced4da; /* Separador punteado */
        margin: 20px 0;
    }
</style>
</head>

<body>

    <!-- ================= HEADER ================= -->
    <header>

        <table width="100%" class="no-border-table" style="border-collapse: collapse;">
            <tr>

                <!-- LOGO -->
                <td style="width: 20%; text-align:left;">
                    <img src="{{ public_path('img/fisio.png') }}" style="width:100px;">
                </td>

                <!-- TITULO -->
                <td style="width: 55%; text-align:center;">
                    <h1 style="margin:0; font-size:32px; font-weight:900; letter-spacing:1px;">
                        <span style="color:#0587d3;">FISIO</span><span style="color:#003c7b;">TecMed</span>
                    </h1>

                    <p style="margin:0; font-size:13px; font-weight:600; color:#111;">
                        GABINETE DE FISIOTERAPIA Y KINESIOLOGÍA
                    </p>

                    <!-- Fecha de expedición -->
                    <p style="margin:2px 0 0 0; font-size:12px; color:#444; font-weight:500;">
                        Fecha de expedición: {{ \Carbon\Carbon::now()->format('d/m/Y') }}
                    </p>
                    <p style="margin:2px 0 0 0; font-size:12px; color:#444; font-weight:500;">
                        La Paz - Bolivia
                    </p>
                </td>


                <!-- CONTACTOS -->
                <td style="width: 25%; text-align:right; font-size:12px; font-weight:600;">
                    <p style="margin:2px 0;">
                        <img src="{{ public_path('img/whatsapp.png') }}" width="13"
                            style="margin-right:4px; vertical-align:middle;">
                        64034041 - 2229593
                    </p>

                    <p style="margin:2px 0;">
                        <img src="{{ public_path('img/gps.png') }}" width="13"
                            style="margin-right:4px; vertical-align:middle;">
                        Av. Saavedra No. 2246 – Facultad de Medicina<br>
                        Edificio de Tecnología Médica – Planta Baja
                    </p>
                </td>

            </tr>
        </table>

        <div class="header-line"></div>

    </header>


    <!-- ================= TITULO PRINCIPAL ================= -->
    <h2 style="text-align:center; font-size:26px; margin-top:10px; margin-bottom:20px; font-weight:800;">
        HISTORIA CLÍNICA FISIOTERAPÉUTICA
    </h2>


    <!-- ================= CONTENIDO DINÁMICO ================= -->
    @yield('content')


    <!-- ================= FOOTER ================= -->
    <footer>

        <table width="100%" class="no-border-table" style="border-collapse: collapse; margin-bottom:5px;">
            <tr>

                <!-- REDES SOCIALES -->
                <td style="width: 75%; text-align:left; font-size:12px;">
                    <br>
                    <span style="margin-right:12px;">
                        <img src="{{ public_path('img/facebook.png') }}" width="15"
                            style="vertical-align:middle; margin-right:4px;">
                        Gabinete de Fisioterapia y Kinesiología UMSA
                    </span>
                    <br>
                    <span style="margin-right:12px;">
                        <img src="{{ public_path('img/tiktok.png') }}" width="15"
                            style="vertical-align:middle; margin-right:4px;">
                        @gabinete.de.fisio6
                    </span>
                    <br>
                    <span style="margin-right:12px;">
                        <img src="{{ public_path('img/gmail.png') }}" width="15"
                            style="vertical-align:middle; margin-right:4px;">
                        gabinetedefisioterapiaumsa@gmail.com
                    </span>

                </td>

                <!-- LOGO DERECHA -->
                <td style="width: 25%; text-align:right;">
                    <img src="{{ public_path('img/fisio.png') }}" width="60">
                </td>

            </tr>
        </table>

        <p style="text-align:center; margin:0; font-size:11px;">
            © {{ date('Y') }} - Centro de Fisioterapia y Rehabilitación Física
        </p>

    </footer>

</body>

</html>
