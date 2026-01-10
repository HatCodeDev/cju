<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Impresión de Credenciales</title>
    <style>
        /* RESET BÁSICO */
        * { box-sizing: border-box; margin: 0; padding: 0; -webkit-print-color-adjust: exact; print-color-adjust: exact; }

        body {
            background: #e5e7eb; /* Gris visual en pantalla */
            font-family: 'Arial', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        /* HOJA CARTA FÍSICA */
        .page {
            width: 215.9mm;  /* Ancho Carta */
            height: 279.4mm; /* Alto Carta */
            background: white;
            padding: 15mm;   /* Márgenes de seguridad de impresora */
            display: grid;
            /* Grid dinámico: 3 columnas de tarjetas */
            grid-template-columns: repeat(3, 1fr);
            grid-template-rows: repeat(3, 1fr);
            gap: 5mm;        /* Espacio entre tarjetas */
            align-content: start;
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
            margin-bottom: 20px;
            page-break-after: always; /* Clave para múltiples páginas */
        }

        /* TARJETA / GAFETE (Vertical: 5.4cm x 8.6cm) */
        .card {
            width: 54mm;
            height: 86mm;
            border: 1px dashed #9ca3af; /* Guía de corte punteada sutil */
            position: relative;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: space-between;
            padding: 4mm;
            background: #fff;
            overflow: hidden;
        }

        /* ELEMENTOS DEL DISEÑO */
        .card-header {
            width: 100%;
            height: 10mm;
            background-color: #111827; /* Color corporativo */
            position: absolute;
            top: 0;
            left: 0;
        }

        .avatar-container {
            margin-top: 12mm;
            width: 35mm;
            height: 35mm;
            border-radius: 50%;
            border: 2px solid #111827;
            overflow: hidden;
            display: flex;
            justify-content: center;
            align-items: center;
            background: #f3f4f6;
        }

        .avatar-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        .info-container {
            text-align: center;
            width: 100%;
            margin-top: 2mm;
        }

        .name {
            font-size: 11pt;
            font-weight: bold;
            color: #111827;
            line-height: 1.2;
            margin-bottom: 2mm;
            text-transform: uppercase;
        }

        .role-badge {
            background: #e5e7eb;
            color: #374151;
            padding: 1mm 3mm;
            border-radius: 4mm;
            font-size: 8pt;
            font-weight: 600;
            display: inline-block;
        }

        .qr-placeholder {
            width: 20mm;
            height: 20mm;
            margin-bottom: 2mm;
        }

        /* MARCAS DE CORTE EN ESQUINAS (Opcional, estilo L) */
        .cut-mark {
            position: absolute;
            width: 2mm;
            height: 2mm;
            border-color: #000;
            border-style: solid;
        }
        .tl { top: -1px; left: -1px; border-width: 1px 0 0 1px; }
        .tr { top: -1px; right: -1px; border-width: 1px 1px 0 0; }
        .bl { bottom: -1px; left: -1px; border-width: 0 0 1px 1px; }
        .br { bottom: -1px; right: -1px; border-width: 0 1px 1px 0; }

        /* ESTILOS DE IMPRESIÓN EXCLUSIVOS */
        @media print {
            body {
                background: white;
                padding: 0;
            }
            .page {
                box-shadow: none;
                margin: 0;
                width: 215.9mm; /* Forzar medidas en impresión */
                height: 279.4mm;
            }
            /* Ocultar elementos no imprimibles si los hubiera */
            .no-print { display: none; }
        }
    </style>
</head>
<body>

@php
    // Agrupar usuarios en bloques de 9 (3x3 grid) para paginación
    $chunks = $users->chunk(9);
@endphp

@foreach($chunks as $pageUsers)
    <div class="page">
        @foreach($pageUsers as $user)
            <div class="card">
                <div class="card-header"></div>

                <div class="cut-mark tl"></div>
                <div class="cut-mark tr"></div>
                <div class="cut-mark bl"></div>
                <div class="cut-mark br"></div>

                <div class="avatar-container">
                    @if($user->avatar_url)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_url) }}" class="avatar-img" alt="Foto">
                    @else
                        <span style="font-size: 24pt; color: #ccc;">?</span>
                    @endif
                </div>

                <div class="info-container">
                    <div class="name">{{ $user->name }}</div>
                    <div class="role-badge">
                        {{ $user->roles->first()?->name ?? 'Usuario' }}
                    </div>
                </div>

                <div class="qr-placeholder">
                    {!! \Illuminate\Support\Facades\Blade::render('<x-filament::icon icon="heroicon-o-qr-code" class="w-full h-full text-gray-800"/>') !!}
                </div>
            </div>
        @endforeach
    </div>
@endforeach

<script>
    // Auto-imprimir al cargar
    window.onload = function() {
        window.print();
    }
</script>
</body>
</html>
