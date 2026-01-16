<x-app-layout>
    <div class="py-6 min-h-[80vh] flex flex-col justify-center">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 w-full">
            <div class="max-w-xl mx-auto">
            
            <div class="text-center mb-4">
                <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                    {{ __('Scan QR Code') }}
                </h2>
                <p class="mt-2 text-secondary-500">
                    Arahkan kamera ke QR Code sparepart untuk melihat detail.
                </p>
            </div>

            <div class="card p-6 shadow-xl relative overflow-hidden">
                <!-- Status Indicators -->
                <div id="status-indicator" class="absolute top-0 left-0 right-0 h-1 bg-secondary-200">
                    <div id="scan-line" class="h-full bg-primary-500 w-full animate-marquee hidden"></div>
                </div>

                <!-- Camera Viewport -->
                <div class="relative bg-black rounded-xl overflow-hidden aspect-square mb-6 group">
                     <div id="reader" class="w-full h-full object-cover"></div>
                     
                     <!-- Overlay for framing -->
                     <div class="absolute inset-0 pointer-events-none border-[30px] border-black/50 hidden md:block" id="camera-overlay">
                         <div class="absolute inset-0 border-2 border-white/20"></div>
                         <div class="absolute top-0 left-0 w-8 h-8 border-l-4 border-t-4 border-primary-500 rounded-tl-lg"></div>
                         <div class="absolute top-0 right-0 w-8 h-8 border-r-4 border-t-4 border-primary-500 rounded-tr-lg"></div>
                         <div class="absolute bottom-0 left-0 w-8 h-8 border-l-4 border-b-4 border-primary-500 rounded-bl-lg"></div>
                         <div class="absolute bottom-0 right-0 w-8 h-8 border-r-4 border-b-4 border-primary-500 rounded-br-lg"></div>
                     </div>
                     
                     <!-- Initial State / Placeholder -->
                     <div id="camera-placeholder" class="absolute inset-0 flex items-center justify-center bg-secondary-900 text-white z-10">
                         <div class="text-center">
                             <svg class="w-12 h-12 mx-auto mb-3 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                             <span class="text-sm font-medium text-secondary-400">Menunggu Izin Kamera...</span>
                         </div>
                     </div>
                </div>

                <!-- Controls -->
                <div class="grid grid-cols-2 gap-4">
                    <button onclick="startCamera()" id="btn-camera" class="btn btn-primary justify-center">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                        Aktifkan Kamera
                    </button>
                    <label for="qr-input-file" class="btn btn-secondary justify-center cursor-pointer">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                        Upload Gambar
                        <input type="file" id="qr-input-file" accept="image/*" class="hidden" onchange="scanFromFile(this)">
                    </label>
                </div>

                <!-- Messages -->
                <div id="result" class="mt-6 hidden p-4 bg-success-50 text-success-700 rounded-lg border border-success-100 flex items-center gap-3 animate-pulse">
                    <div class="p-2 bg-success-100 rounded-full">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <span class="font-bold block">QR Code Ditemukan!</span>
                        <span class="text-sm">Sedang mengalihkan ke halaman detail...</span>
                    </div>
                </div>
                
                <div id="error-message" class="mt-6 hidden p-4 bg-danger-50 text-danger-700 rounded-lg border border-danger-100 flex items-start gap-3">
                    <div class="p-2 bg-danger-100 rounded-full flex-shrink-0">
                         <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                    </div>
                    <div class="text-sm" id="error-text"></div>
                </div>
            </div>
            
            <div class="mt-8 text-center">
                 <a href="{{ route('superadmin.inventory.index') }}" class="text-secondary-500 hover:text-secondary-700 text-sm font-medium">
                    &larr; Kembali ke Inventaris
                </a>
            </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        const html5QrCode = new Html5Qrcode("reader");
        let isCameraRunning = false;
        const scanLine = document.getElementById('scan-line');
        const cameraPlaceholder = document.getElementById('camera-placeholder');

        const onScanSuccess = (decodedText, decodedResult) => {
            stopCamera().then(() => {
                handleResult(decodedText);
            });
        };

        const handleResult = (decodedText) => {
            const resultDiv = document.getElementById('result');
            resultDiv.classList.remove('hidden');
            
            // Redirect
            setTimeout(() => {
                window.location.href = decodedText;
            }, 1000);
        }

        const onScanFailure = (error) => {
            // console.warn(`Code scan error = ${error}`);
        };
        
        const startCamera = () => {
             const errorDiv = document.getElementById('error-message');
             errorDiv.classList.add('hidden');
             
             if (isCameraRunning) return;

             const config = { fps: 10, qrbox: { width: 250, height: 250 } };
             html5QrCode.start({ facingMode: "environment" }, config, onScanSuccess, onScanFailure)
            .then(() => {
                isCameraRunning = true;
                cameraPlaceholder.classList.add('hidden');
                scanLine.classList.remove('hidden');
                document.getElementById('btn-camera').innerText = "Matikan Kamera";
                document.getElementById('btn-camera').setAttribute('onclick', 'stopCamera()');
                document.getElementById('btn-camera').classList.replace('btn-primary', 'btn-danger');
            })
            .catch(err => {
                 document.getElementById('error-text').innerText = "Gagal mengakses kamera: " + err;
                 errorDiv.classList.remove('hidden');
            });
        };

        const stopCamera = () => {
            if (isCameraRunning) {
                return html5QrCode.stop().then(() => {
                    isCameraRunning = false;
                    html5QrCode.clear();
                    cameraPlaceholder.classList.remove('hidden');
                    scanLine.classList.add('hidden');
                    document.getElementById('btn-camera').innerText = "Aktifkan Kamera";
                    document.getElementById('btn-camera').setAttribute('onclick', 'startCamera()');
                    document.getElementById('btn-camera').classList.replace('btn-danger', 'btn-primary');
                });
            }
            return Promise.resolve();
        };

        const scanFromFile = (input) => {
            if (!input.files || input.files.length === 0) return;
            
            const file = input.files[0];
            
            stopCamera().then(() => {
                const errorDiv = document.getElementById('error-message');
                errorDiv.classList.add('hidden');

                if (file.type === 'image/svg+xml') {
                    convertSvgToPng(file).then(pngFile => {
                        scanFile(pngFile);
                    }).catch(err => {
                         document.getElementById('error-text').innerText = "Gagal memproses gambar SVG.";
                         errorDiv.classList.remove('hidden');
                    });
                } else {
                    scanFile(file);
                }
            });
        };

        const scanFile = (file) => {
             html5QrCode.scanFile(file, true)
                .then(decodedText => {
                    handleResult(decodedText);
                })
                .catch(err => {
                    const errorDiv = document.getElementById('error-message');
                    document.getElementById('error-text').innerText = "Gagal memindai gambar. Pastikan QR Code terlihat jelas.";
                    errorDiv.classList.remove('hidden');
                    console.error(err);
                });
        }

        const convertSvgToPng = (file) => {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = new Image();
                    img.onload = () => {
                        const canvas = document.createElement('canvas');
                        canvas.width = img.width || 500;
                        canvas.height = img.height || 500;
                        const ctx = canvas.getContext('2d');
                        ctx.fillStyle = "white";
                        ctx.fillRect(0, 0, canvas.width, canvas.height);
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        canvas.toBlob((blob) => {
                            if (blob) resolve(new File([blob], "qr.png", { type: "image/png" }));
                            else reject("Gagal konversi canvas.");
                        }, 'image/png');
                    };
                    img.onerror = () => reject("Gagal muat SVG.");
                    img.src = e.target.result;
                };
                reader.onerror = () => reject("Gagal baca file.");
                reader.readAsDataURL(file);
            });
        };

        // Auto start
        startCamera();
    </script>
    @endpush
</x-app-layout>
