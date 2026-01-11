<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Impresión de Credenciales</title>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;600;700;800&display=swap" rel="stylesheet">

    <style>
        /* --- CONFIGURACIÓN GLOBAL --- */
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
            -webkit-print-color-adjust: exact !important;
            print-color-adjust: exact !important;
        }

        body {
            background: #525659; /* Fondo oscuro para contraste en pantalla */
            font-family: 'Montserrat', sans-serif;
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 20px;
        }

        /* --- HOJA CARTA (LIENZO) --- */
        .page {
            width: 215.9mm;
            height: 279.4mm;
            background: white;
            padding: 15mm; /* Margen seguro de impresión */
            display: grid;
            /* 2 Columnas para asegurar márgenes laterales */
            grid-template-columns: repeat(2, 70mm);
            /* 2 Filas */
            grid-template-rows: repeat(2, 110mm);
            gap: 10mm; /* Espacio entre credenciales para cortar cómodo */
            justify-content: center;
            align-content: start;
            margin-bottom: 20px;
            box-shadow: 0 0 10px rgba(0,0,0,0.5);
            page-break-after: always;
            position: relative;
        }

        /* --- TARJETA BASE (Dimensiones Físicas) --- */
        .card {
            width: 70mm;   /* Requerimiento: 7cm */
            height: 110mm; /* Requerimiento: 11cm */
            position: relative;
            overflow: hidden;
            border: 1px dashed #d1d5db; /* Guía de corte sutil */
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }

        /* --- CARA FRONTAL --- */
        /* Asume que guardarás la imagen limpia en public/images/gafete-front.png */
        .card-front {
            background-image: url("{{ asset('images/gafete-front.png') }}");
        }

        /* --- CARA TRASERA --- */
        /* Asume que guardarás la imagen en public/images/gafete-back.png */
        .card-back {
            background-image: url("{{ asset('images/gafete-back.png') }}");
        }

        /* --- ELEMENTOS DINÁMICOS (Solo en Frontal) --- */

        /* Contenedor de la foto - Ajustado al círculo negro de tu diseño */
        .photo-area {
            position: absolute;
            top: 15.5mm; /* Ajuste manual basado en tu imagen */
            left: 50%;
            transform: translateX(-50%);
            width: 38mm;  /* Tamaño estimado del círculo negro */
            height: 38mm;
            border-radius: 50%;
            overflow: hidden;
            z-index: 10;
            /* Borde opcional si el recorte de la foto no es perfecto */
            /* border: 2px solid white; */
        }

        .photo-img {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }

        /* Contenedor de textos */
        .text-area {
            position: absolute;
            top: 60mm; /* Debajo de la foto */
            left: 0;
            width: 100%;
            text-align: center;
            padding: 0 4mm;
        }

        .user-name {
            font-family: 'Montserrat', sans-serif;
            font-weight: 800; /* Extra Bold */
            font-size: 14pt;
            color: #003b5c; /* Azul institucional BUAP aprox */
            text-transform: uppercase;
            line-height: 1.1;
            margin-bottom: 3mm;
        }

        .user-role {
            font-family: 'Montserrat', sans-serif;
            font-weight: 600;
            font-size: 10pt;
            color: #003b5c;
            text-transform: uppercase;
            border-top: 2px solid #003b5c; /* La línea separadora */
            display: inline-block;
            padding-top: 2mm;
            max-width: 80%;
        }

        /* --- GUIAS DE CORTE (Crop Marks) --- */
        .crop-mark {
            position: absolute;
            width: 4mm;
            height: 4mm;
            border-color: #999;
            border-style: solid;
        }
        .cm-tl { top: -1px; left: -1px; border-width: 1px 0 0 1px; }
        .cm-tr { top: -1px; right: -1px; border-width: 1px 1px 0 0; }
        .cm-bl { bottom: -1px; left: -1px; border-width: 0 0 1px 1px; }
        .cm-br { bottom: -1px; right: -1px; border-width: 0 1px 1px 0; }

        /* --- MODO IMPRESIÓN --- */
        @media print {
            body { background: white; padding: 0; margin: 0; }
            .page { box-shadow: none; margin: 0 auto; page-break-after: always; }
        }
    </style>
</head>
<body>

@php
    // Procesamos usuarios de 2 en 2, porque caben 2 usuarios completos (4 tarjetas) por hoja
    $chunks = $users->chunk(2);
@endphp

@foreach($chunks as $pageUsers)
    <div class="page">
        @foreach($pageUsers as $user)
            <div class="card card-front">
                <div class="crop-mark cm-tl"></div><div class="crop-mark cm-tr"></div>
                <div class="crop-mark cm-bl"></div><div class="crop-mark cm-br"></div>

                <div class="photo-area">
                    @if($user->avatar_url)
                        <img src="{{ \Illuminate\Support\Facades\Storage::url($user->avatar_url) }}" class="photo-img" alt="Foto">
                    @else
                        <div style="width:100%; height:100%; background:#ccc; display:flex; align-items:center; justify-content:center; color:white; font-size:20pt;">?</div>
                    @endif
                </div>

                <div class="text-area">
                    <div class="user-name">
                        {{ \Illuminate\Support\Str::limit($user->name, 30) }}
                    </div>
                    <div class="user-role">
                        {{ str_replace('_', ' ', $user->roles->first()?->name ?? 'ADMINISTRATIVO') }}
                    </div>
                </div>
            </div>

            <div class="card card-back">
                <div class="crop-mark cm-tl"></div><div class="crop-mark cm-tr"></div>
                <div class="crop-mark cm-bl"></div><div class="crop-mark cm-br"></div>

            </div>

        @endforeach
    </div>
@endforeach

<script>
    // Se recomienda usar Chrome/Edge y activar "Gráficos de fondo"
    window.onload = function() {
        // Pequeño delay para asegurar carga de imágenes
        setTimeout(() => {
            window.print();
        }, 500);
    }
</script>

</body>
</html>
