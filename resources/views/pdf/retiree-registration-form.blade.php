<!DOCTYPE html>
<html lang="es">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <title>Formato de Registro - {{ $retiree->full_name }}</title>
    <style>
        /* Configuración General */
        @page {
            margin: 1cm 1.5cm;
            font-family: 'Arial', sans-serif;
        }
        body {
            font-size: 10pt;
            color: #222;
            line-height: 1.3;
        }

        /* Utilidades */
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .bold { font-weight: bold; }
        .uppercase { text-transform: uppercase; }
        .buap-blue { color: #003b5c; }

        /* Cabecera con Logos */
        table.header-table {
            width: 100%;
            border-bottom: 2px solid #003b5c;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header-title {
            font-size: 12pt;
            font-weight: bold;
            color: #003b5c;
            margin: 0;
        }
        .header-subtitle {
            font-size: 10pt;
            font-weight: normal;
            margin: 2px 0;
        }

        /* Secciones */
        .section-header {
            background-color: #e6eef2; /* Azul muy tenue */
            color: #003b5c;
            font-size: 9pt;
            font-weight: bold;
            padding: 5px 8px;
            margin-top: 10px;
            margin-bottom: 8px;
            border-left: 4px solid #003b5c;
            text-transform: uppercase;
        }

        /* Tablas de Datos */
        table.data-table {
            width: 100%;
            border-collapse: collapse;
        }
        table.data-table td {
            padding: 4px;
            vertical-align: bottom;
        }

        /* Etiquetas y Campos */
        .label {
            font-size: 8pt;
            font-weight: bold;
            color: #444;
            white-space: nowrap;
            width: 1%; /* Se ajusta al contenido */
            padding-right: 5px;
        }
        .field {
            border-bottom: 1px solid #777;
            font-size: 10pt;
            padding-left: 5px;
            color: #000;
        }
        .field-blank {
            border-bottom: 1px solid #777;
            height: 14px;
        }

        /* Checkboxes Personalizados */
        .checkbox-wrapper {
            display: inline-block;
            margin-right: 15px;
        }
        .box {
            display: inline-block;
            width: 10px;
            height: 10px;
            border: 1px solid #003b5c;
            margin-left: 4px;
            text-align: center;
            font-size: 8px;
            line-height: 9px;
            vertical-align: middle;
        }
        .check-label {
            font-size: 8pt;
            vertical-align: middle;
        }

        /* Pie de página y Firmas */
        .footer {
            margin-top: 60px;
            width: 100%;
        }
        .signature-block {
            width: 250px;
            margin: 0 auto;
            text-align: center;
        }
        .signature-line {
            border-top: 1px solid #000;
            margin-bottom: 5px;
        }
        .signature-text {
            font-size: 8pt;
            font-weight: bold;
        }
        .timestamp {
            position: absolute;
            bottom: -20px;
            right: 0;
            font-size: 7pt;
            color: #888;
        }
    </style>
</head>
<body>

<table class="header-table">
    <tr>
        <td width="15%" valign="middle">
            <img src="{{ public_path('images/LogoBUAP.png') }}" style="max-height: 70px; max-width: 80px;">
        </td>

        <td width="70%" align="center" valign="middle">
            <div class="header-title">BENEMÉRITA UNIVERSIDAD AUTÓNOMA DE PUEBLA</div>
            <div class="header-title" style="font-size: 11pt; margin-top: 4px;">CASA DEL JUBILADO UNIVERSITARIO</div>
            <div class="header-subtitle uppercase" style="margin-top: 8px;">FORMATO DE REGISTRO DE USUARIO (A)</div>
        </td>

        <td width="15%" align="right" valign="middle">
            <img src="{{ public_path('images/LogoCJU.png') }}" style="max-height: 70px; max-width: 80px;">
        </td>
    </tr>
</table>

<div class="section-header">1. Datos Generales</div>
<table class="data-table">
    <tr>
        <td class="label">NOMBRE COMPLETO:</td>
        <td class="field" colspan="3">{{ $retiree->full_name }}</td>
    </tr>
    <tr>
        <td class="label">CURP:</td>
        <td class="field">{{ $retiree->curp }}</td>
        <td class="label">FECHA NAC.:</td>
        <td class="field">{{ $retiree->birth_date?->format('d/m/Y') }} ({{ $retiree->age }} años)</td>
    </tr>
    <tr>
        <td class="label">TELÉFONO:</td>
        <td class="field">{{ $retiree->phone }}</td>
        <td class="label">GÉNERO:</td>
        <td class="field">{{ $retiree->gender?->getLabel() ?? '-' }}</td>
    </tr>
</table>

<table class="data-table" style="margin-top: 8px;">
    <tr>
        <td class="label" width="1%">TIPO DE USUARIO:</td>
        <td>
                <span class="checkbox-wrapper">
                    <span class="check-label">JUBILADO BUAP</span>
                    <div class="box">{{ $retiree->retiree_type === \App\Enums\RetireeType::Internal ? 'X' : '' }}</div>
                </span>
            <span class="checkbox-wrapper">
                    <span class="check-label">EXTERNO</span>
                    <div class="box">{{ $retiree->retiree_type === \App\Enums\RetireeType::External ? 'X' : '' }}</div>
                </span>
        </td>
        <td class="label" width="1%">ID / MATRÍCULA:</td>
        <td class="field" width="20%">{{ $retiree->worker_id ?? 'N/A' }}</td>
    </tr>
</table>

<div class="section-header">2. Información de Salud</div>
<table class="data-table">
    <tr>
        <td class="label">INSTITUCIÓN MÉDICA:</td>
        <td colspan="3">
            <span class="checkbox-wrapper"><span class="check-label">HUP</span><div class="box">{{ $retiree->insurance_type === \App\Enums\InsuranceType::HUP ? 'X' : '' }}</div></span>
            <span class="checkbox-wrapper"><span class="check-label">IMSS</span><div class="box">{{ $retiree->insurance_type === \App\Enums\InsuranceType::IMSS ? 'X' : '' }}</div></span>
            <span class="checkbox-wrapper"><span class="check-label">ISSSTE</span><div class="box">{{ $retiree->insurance_type === \App\Enums\InsuranceType::ISSSTE ? 'X' : '' }}</div></span>
            <span class="checkbox-wrapper"><span class="check-label">ISSSTEP</span><div class="box">{{ $retiree->insurance_type === \App\Enums\InsuranceType::ISSSTEP ? 'X' : '' }}</div></span>
            <span class="checkbox-wrapper"><span class="check-label">OTRO:</span></span>
            <span style="border-bottom: 1px solid #777; padding: 0 5px; font-size: 10pt;">{{ $retiree->insurance_name }}</span>
        </td>
    </tr>
    <tr>
        <td class="label">ALERGIAS / PADECIMIENTOS:</td>
        <td class="field" colspan="3">{{ $retiree->allergies ?? 'Sin alergias reportadas.' }}</td>
    </tr>
</table>

<div class="section-header">3. Actividades de Interés</div>
<table class="data-table">
    <tr>
        <td class="label" style="vertical-align: top; width: 100px;">SERVICIOS SOLICITADOS:</td>
        <td class="field" style="height: 30px; vertical-align: top;">
            @forelse($retiree->medicalServices as $service)
                {{ $service->name }}@if(!$loop->last), @endif
            @empty
                Ninguno seleccionado
            @endforelse
        </td>
    </tr>
    <tr>
        <td class="label" style="vertical-align: top;">TALLERES INSCRITOS:</td>
        <td class="field" style="height: 30px; vertical-align: top;">
            @forelse($retiree->workshops as $workshop)
                {{ $workshop->name }}@if(!$loop->last), @endif
            @empty
                Ninguno seleccionado
            @endforelse
        </td>
    </tr>
    <tr>
        <td class="label">NOTAS ADICIONALES:</td>
        <td class="field">{{ $retiree->other_services_notes }}</td>
    </tr>
</table>

<div class="section-header">4. Contacto de Emergencia</div>
<table class="data-table">
    <tr>
        <td class="label">NOMBRE CONTACTO:</td>
        <td class="field" colspan="3">{{ $retiree->emergencyContact?->name }}</td>
    </tr>
    <tr>
        <td class="label">PARENTESCO:</td>
        <td class="field">{{ $retiree->emergencyContact?->relationship }}</td>
        <td class="label">TELÉFONO:</td>
        <td class="field">{{ $retiree->emergencyContact?->phone }}</td>
    </tr>
    <tr>
        <td class="label">DATOS LABORALES:</td>
        <td colspan="3">
                <span class="checkbox-wrapper">
                    <span class="check-label">TRABAJA EN BUAP:</span>
                    <div class="box">{{ $retiree->emergencyContact?->is_university_worker ? 'X' : '' }}</div>
                </span>
            &nbsp;&nbsp;
            <span class="label">DEPENDENCIA:</span>
            <span style="border-bottom: 1px solid #777; padding: 0 10px;">
                    {{ $retiree->emergencyContact?->dependency ?? 'N/A' }}
                </span>
        </td>
    </tr>
</table>

<div class="footer">
    <div class="signature-block">
        <div class="signature-line"></div>
        <div class="signature-text">NOMBRE Y FIRMA DEL USUARIO(A)</div>
    </div>

    <div class="timestamp">
        Registro ID: {{ $retiree->id }} | Generado el: {{ now()->format('d/m/Y H:i:s') }}
    </div>
</div>

</body>
</html>
