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
            margin: 0;
            padding: 0;
        }

        /* ESTRATEGIA: Tabla Maestra
           Usamos border-spacing para crear el hueco entre tarjetas (gap)
           sin afectar el ancho porcentual de las celdas.
        */
        table.layout-grid {
            width: 100%;
            border-collapse: separate;
            border-spacing: 15px; /* Espacio horizontal y vertical entre tarjetas */
            table-layout: fixed; /* Crítico para respetar anchos exactos */
            margin-top: -15px; /* Compensar el primer espaciado superior */
        }

        td.card-cell {
            width: 50%;
            vertical-align: top;
            padding: 0;
        }

        /* Contenedor visual de la tarjeta */
        .card {
            border: 2px dashed #ccc;
            padding: 10px;
            text-align: center;
            height: 220px; /* Altura fija para consistencia */
            border-radius: 8px;
            background-color: #fff;
            /* overflow: hidden;  Evitamos overflow hidden en dompdf si es posible, mejor controlar contenido */
        }

        .name {
            font-size: 14px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 5px;
            height: 35px; /* Altura fija para nombre */
            line-height: 1.1;
            display: block;
        }

        .uuid {
            font-size: 9px;
            color: #666;
            margin-bottom: 5px;
            font-family: monospace;
        }

        .qr-wrapper {
            margin: 5px auto;
            width: 120px;
            height: 120px;
        }

        /* Asegura que la imagen no genere espacio extra inline */
        .qr-wrapper img {
            display: block;
            width: 120px;
            height: 120px;
        }

        .footer {
            margin-top: 8px;
            font-size: 8px;
            color: #888;
            border-top: 1px solid #eee;
            padding-top: 5px;
        }

        .page-break {
            page-break-after: always;
        }

        /* Utilidad para celdas vacías si el total es impar */
        .empty-cell {
            border: none;
            background: transparent;
        }
    </style>
</head>
<body>

{{-- Procesamos en grupos de 2 (Filas) --}}
@foreach(collect($retirees)->chunk(2) as $row)

    <table class="layout-grid">
        <tr>
            @foreach($row as $retiree)
                <td class="card-cell">
                    <div class="card">
                        <div class="name">
                            {{ Str::limit($retiree->full_name, 45) }}
                        </div>

                        <div class="uuid">{{ $retiree->uuid }}</div>

                        <div class="qr-wrapper">
                            {{--
                               OPTIMIZACIÓN:
                               Generamos el QR directamente.
                               Nota: Asegúrate de que $retiree->uuid no sea null.
                            --}}
                            <img
                                src="data:image/svg+xml;base64,{{ base64_encode(
                                        QrCode::format('svg')
                                            ->size(120)
                                            ->margin(0) // Margen 0 en el QR para aprovechar espacio
                                            ->errorCorrection('M') // 'M' es suficiente y genera QRs menos densos (más fáciles de leer)
                                            ->generate((string) $retiree->uuid)
                                    ) }}"
                                width="120"
                                height="120"
                            >
                        </div>

                        <div class="footer">
                            ESTACIÓN DE ESCANEO - CONTROL
                        </div>
                    </div>
                </td>
            @endforeach

            {{-- Si la fila tiene solo 1 elemento (impar), rellenamos para mantener estructura --}}
            @if($row->count() < 2)
                <td class="card-cell empty-cell"></td>
            @endif
        </tr>
    </table>

    {{--
       LÓGICA DE PAGINACIÓN:
       Si hemos impreso 4 filas (8 tarjetas), forzamos salto de página.
       Usamos $loop->iteration sobre el loop de CHUNKS (filas), no de items individuales.
    --}}
    @if($loop->iteration % 4 == 0 && !$loop->last)
        <div class="page-break"></div>
    @endif

@endforeach

</body>
</html>
