<x-filament-panels::page>
    @assets
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    @endassets

    <style>
        /* Forzar que el contenedor del scanner ocupe todo el espacio y no tenga bordes */
        #qr-reader {
            border: none !important;
            width: 100%;
        }

        /* CORRECCIÓN CRÍTICA:
           1. Aplicamos border-radius a TODAS las esquinas del video, no solo abajo.
           2. object-fit cover asegura que no se deforme.
        */
        #qr-reader video {
            object-fit: cover !important;
            border-radius: 1.5rem !important; /* 24px igual que rounded-3xl */
        }

        /* Ocultar elementos innecesarios de la librería */
        #qr-reader__scan_region {
            display: none !important;
        }
    </style>

    <div
        class="flex flex-col items-center justify-start pt-6 w-full min-h-[calc(100vh-12rem)]"
        x-data="qrScannerComponent({
            processingCallback: (code) => $wire.processScan(code)
        })"
        x-init="initScanner()"
        x-cloak
    >
        <div class="w-full max-w-sm md:max-w-2xl bg-white dark:bg-gray-900 rounded-3xl shadow-xl ring-1 ring-gray-100 dark:ring-gray-800 flex flex-col transition-all duration-500">

            <div class="px-6 py-5 bg-white dark:bg-gray-900 rounded-t-3xl border-b border-gray-100 dark:border-gray-800 z-20 relative">
                <div class="flex justify-between items-start">
                    <div>
                        <h2 class="text-xl font-bold text-gray-800 dark:text-white tracking-tight">
                            Punto de Acceso
                        </h2>

                        <div class="mt-2 flex items-center gap-2">
                            <span class="relative flex h-2.5 w-2.5">
                                <span x-show="!hasError && isCameraActive" class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                <span :class="hasError ? 'bg-red-500' : (isProcessing ? 'bg-blue-500' : 'bg-green-500')" class="relative inline-flex rounded-full h-2.5 w-2.5 transition-colors duration-300"></span>
                            </span>
                            <p class="text-xs font-semibold uppercase tracking-wide text-gray-500 dark:text-gray-400">
                                <span x-text="hasError ? 'ERROR DE CÁMARA' : (isProcessing ? 'VALIDANDO CÓDIGO...' : 'LISTO PARA ESCANEAR')"></span>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="relative w-full h-[400px] md:h-[500px] bg-black rounded-b-3xl overflow-hidden group isolate transform-gpu">

                <div id="qr-reader" class="w-full h-full" wire:ignore></div>

                <div x-show="hasError" class="absolute inset-0 z-30 flex flex-col items-center justify-center bg-gray-900/95 p-8 text-center" x-transition.opacity>
                    <x-heroicon-o-video-camera-slash class="w-10 h-10 text-red-500 mb-4" />
                    <p class="text-gray-300 text-sm mb-4" x-text="errorMessage"></p>
                    <button wire:click="enableManualInput" class="text-primary-400 font-bold text-sm hover:underline">
                        Ingresar manualmente
                    </button>
                </div>

                <div x-show="!hasError && isCameraActive" class="absolute inset-0 z-10 pointer-events-none flex items-center justify-center">
                    <div
                        class="relative transition-all duration-300 ease-out border-[3px]"
                        :class="isProcessing ? 'border-green-400 bg-green-500/10 scale-95 shadow-[0_0_20px_rgba(74,222,128,0.3)]' : 'border-white/40'"
                        :style="`width: ${config.boxWidth}px; height: ${config.boxHeight}px; border-radius: 24px;`"
                    >
                        <div x-show="!isProcessing" class="absolute -top-[3px] -left-[3px] w-8 h-8 border-t-[3px] border-l-[3px] border-white rounded-tl-[24px]"></div>
                        <div x-show="!isProcessing" class="absolute -top-[3px] -right-[3px] w-8 h-8 border-t-[3px] border-r-[3px] border-white rounded-tr-[24px]"></div>
                        <div x-show="!isProcessing" class="absolute -bottom-[3px] -left-[3px] w-8 h-8 border-b-[3px] border-l-[3px] border-white rounded-bl-[24px]"></div>
                        <div x-show="!isProcessing" class="absolute -bottom-[3px] -right-[3px] w-8 h-8 border-b-[3px] border-r-[3px] border-white rounded-br-[24px]"></div>
                    </div>
                </div>

                <div x-show="!isCameraActive && !hasError" class="absolute inset-0 z-20 flex flex-col items-center justify-center bg-gray-900">
                    <x-filament::loading-indicator class="h-8 w-8 text-primary-500" />
                </div>
            </div>

        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('qrScannerComponent', ({ processingCallback }) => ({
                html5QrCode: null,
                isCameraActive: false,
                isProcessing: false,
                hasError: false,
                errorMessage: '',
                lastDecoded: null,

                config: {
                    fps: 10,
                    boxWidth: 250,
                    boxHeight: 250,
                    aspectRatio: 1.0
                },

                async initScanner() {
                    const isMobile = window.innerWidth < 768;

                    // Ajuste de configuración para móvil
                    if (isMobile) {
                        this.config.boxWidth = 240;
                        this.config.boxHeight = 240;
                        this.config.aspectRatio = 1.0;
                    } else {
                        this.config.boxWidth = 400;
                        this.config.boxHeight = 250;
                        this.config.aspectRatio = 1.77;
                    }

                    this.$nextTick(() => { this.startCamera(); });
                },

                async startCamera() {
                    if (typeof Html5Qrcode === 'undefined') {
                        this.handleError('Librería no cargada.');
                        return;
                    }
                    try {
                        const cameras = await Html5Qrcode.getCameras();
                        if (!cameras || cameras.length === 0) throw new Error('Sin cámara.');

                        this.html5QrCode = new Html5Qrcode("qr-reader");

                        await this.html5QrCode.start(
                            { facingMode: "environment" },
                            {
                                fps: this.config.fps,
                                qrbox: { width: this.config.boxWidth, height: this.config.boxHeight },
                                aspectRatio: this.config.aspectRatio,
                                disableFlip: false
                            },
                            (decodedText) => this.handleScan(decodedText)
                        );

                        this.isCameraActive = true;
                        this.hasError = false;
                    } catch (err) {
                        this.handleError('No se pudo acceder a la cámara o permisos denegados.');
                    }
                },

                handleScan(decodedText) {
                    if (this.isProcessing || this.lastDecoded === decodedText) return;

                    this.isProcessing = true;
                    this.lastDecoded = decodedText;

                    // Feedback háptico y sonoro
                    if (navigator.vibrate) navigator.vibrate(50);
                    new Audio('https://codeskulptor-demos.commondatastorage.googleapis.com/pang/pop.mp3').play().catch(()=>{});

                    // Procesamiento asíncrono seguro
                    Promise.resolve(processingCallback(decodedText))
                        .then(() => {
                            setTimeout(() => {
                                this.isProcessing = false;
                                this.lastDecoded = null;
                            }, 2000);
                        })
                        .catch(() => { this.isProcessing = false; });
                },

                handleError(msg) {
                    this.hasError = true;
                    this.isCameraActive = false;
                    this.errorMessage = msg;
                },

                destroy() {
                    if (this.html5QrCode && this.html5QrCode.isScanning) {
                        this.html5QrCode.stop().then(() => this.html5QrCode.clear()).catch(e => {});
                    }
                }
            }));
        });
    </script>
</x-filament-panels::page>
