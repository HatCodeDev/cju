<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
    <style>
        @page {
            margin: 0.5cm;
            size: letter;
        }

        body {
            font-family: sans-serif;
            font-size: 12px;
        }

        .container {
            width: 100%;
        }

        /* CORRECCIÓN 1: Matemáticas Seguras */
        .card-wrapper {
            float: left;
            width: 44%; /* 44% + 4% margen (2+2) = 48%. Sobra 2% de seguridad para bordes */
            margin: 2%;
            height: 240px;
        }

        .card {
            border: 2px dashed #ccc;
            padding: 10px;
            text-align: center;
            height: 215px;
            border-radius: 8px;
            background-color: #fff;
        }

        .name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            height: 35px;
            line-height: 1.2;
            overflow: hidden;
            display: block;
            padding-top: 5px;
        }

        .uuid {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
            font-family: monospace;
            word-wrap: break-word;
        }

        .qr-container {
            margin: 5px auto;
            width: 120px;
            height: 120px;
            text-align: center;
        }

        .qr-container img {
            width: 120px;
            height: 120px;
        }

        .footer {
            margin-top: 10px;
            font-size: 8px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        /* Salto de Página */
        .page-break {
            page-break-after: always;
            clear: both;
        }

        /* CORRECCIÓN 2: Salto de Fila Estricto */
        .row-break {
            clear: both; /* Esto resetea los floats */
            display: block;
            width: 100%;
            height: 0;
            margin: 0;
            padding: 0;
            border: none;
        }
    </style>
</head>
<body>
<div class="container">
    @foreach($retirees as $retiree)
        <div class="card-wrapper">
            <div class="card">
                <div class="name">
                    {{ Str::limit($retiree->full_name, 40) }}
                </div>

                <div class="uuid">{{ $retiree->uuid }}</div>

                <div class="qr-container">
                    <img
                        src="data:image/svg+xml;base64,{{ base64_encode(QrCode::format('svg')->size(120)->errorCorrection('H')->generate($retiree->uuid)) }}"
                        width="120"
                        height="120"
                    >
                </div>

                <div class="footer">
                    ESTACIÓN DE ESCANEO - CONTROL
                </div>
            </div>
        </div>

        {{-- LÓGICA DE CONTROL DE FLUJO --}}

        {{-- 1. Salto de Fila: Cada 2 tarjetas, forzamos nueva línea --}}
        @if($loop->iteration % 2 == 0)
            <div class="row-break"></div>
        @endif

        {{-- 2. Salto de Página: Cada 8 tarjetas, forzamos nueva hoja --}}
        {{-- Nota: Usamos modulus 8 para que ocurra en la tarjeta 8, 16, 24... --}}
        @if($loop->iteration % 8 == 0 && !$loop->last)
            <div class="page-break"></div>
        @endif

    @endforeach
</div>
</body>
</html>
