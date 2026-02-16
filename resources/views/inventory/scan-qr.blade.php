<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Standardized Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.scan_qr_title') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">{{ __('ui.scan_qr_desc') }}</p>
                </div>
                <div>
                     <a href="{{ route('inventory.index') }}" class="btn btn-secondary flex items-center gap-2">
                        <svg class="w-5 h-5 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        {{ __('ui.back_to_inventory') }}
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 items-start">
                
                <!-- Left Column: Scanner -->
                <div class="lg:col-span-8">
                    <div class="card bg-white shadow-xl rounded-2xl overflow-hidden border border-secondary-200 relative group">
                        
                        <!-- Scanner Header with Controls -->
                        <div class="p-3 border-b border-secondary-100 flex justify-between items-center bg-secondary-50">
                            <div class="flex items-center gap-2">
                                <span class="bg-red-500 w-2.5 h-2.5 rounded-full block"></span>
                                <span class="bg-yellow-500 w-2.5 h-2.5 rounded-full block"></span>
                                <span class="bg-green-500 w-2.5 h-2.5 rounded-full block"></span>
                            </div>
                            <div class="flex items-center gap-2">
                                <span class="text-[10px] font-mono text-secondary-500 uppercase tracking-wider">{{ __('ui.camera_feed_live') }}</span>
                                <button onclick="flipCamera()" class="p-1 rounded-md hover:bg-secondary-200 text-secondary-600 transition-colors" title="Ganti Kamera">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                </button>
                            </div>
                        </div>

                        <!-- Camera Viewport (aspect-video) -->
                        <div class="relative bg-black aspect-video w-full overflow-hidden">
                            <div id="reader" class="w-full h-full object-cover"></div>

                            <!-- Professional Scan Overlay -->
                            <div class="absolute inset-0 pointer-events-none" id="camera-overlay">
                                <div class="absolute inset-0 border-[30px] border-black/60 hidden md:block"></div>
                                <div class="absolute inset-0 md:inset-8 border-2 border-white/20 rounded-lg flex items-center justify-center">
                                    <div class="absolute top-0 left-0 w-10 h-10 border-l-4 border-t-4 border-primary-500 rounded-tl-xl shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                                    <div class="absolute top-0 right-0 w-10 h-10 border-r-4 border-t-4 border-primary-500 rounded-tr-xl shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                                    <div class="absolute bottom-0 left-0 w-10 h-10 border-l-4 border-b-4 border-primary-500 rounded-bl-xl shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                                    <div class="absolute bottom-0 right-0 w-10 h-10 border-r-4 border-b-4 border-primary-500 rounded-br-xl shadow-[0_0_10px_rgba(59,130,246,0.5)]"></div>
                                    <div id="scan-line" class="absolute left-4 right-4 h-0.5 bg-primary-400 shadow-[0_0_15px_rgba(59,130,246,0.8)] animate-scan hidden"></div>
                                </div>
                            </div>

                            <!-- Placeholder -->
                            <div id="camera-placeholder" class="absolute inset-0 flex flex-col items-center justify-center bg-secondary-900 z-10 p-6 text-center">
                                <div class="w-16 h-16 bg-secondary-800 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z"></path></svg>
                                </div>
                                <h3 class="text-white font-medium text-base">{{ __('ui.camera_inactive') }}</h3>
                                <p class="text-secondary-400 text-xs mt-1 max-w-xs">{{ __('ui.camera_permission_desc') }}</p>
                                <button onclick="startCamera()" id="btn-start-placeholder" class="mt-4 btn btn-primary px-5 py-2 rounded-full shadow-lg shadow-primary-500/20 hover:scale-105 transition-transform text-sm">
                                    {{ __('ui.start_scan') }}
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Messages -->
                    <div id="result" class="mt-4 hidden transform transition-all duration-500 ease-out">
                        <div class="bg-white p-4 rounded-xl border border-success-200 shadow-lg flex items-center gap-4">
                             <div class="w-10 h-10 bg-success-100 rounded-full flex items-center justify-center flex-shrink-0 animate-bounce">
                                <svg class="w-5 h-5 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <div>
                                <h4 class="text-base font-bold text-gray-900">{{ __('ui.qr_found') }}</h4>
                                <p class="text-xs text-gray-500">{{ __('ui.redirecting_to_inventory') }}</p>
                            </div>
                        </div>
                    </div>
                    
                    <div id="error-message" class="mt-4 hidden transform transition-all duration-300">
                        <div class="bg-white p-3 rounded-xl border-l-4 border-danger-500 shadow-md flex items-start gap-3">
                            <div class="text-danger-500 mt-0.5">
                                 <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-900 text-xs">{{ __('ui.scan_failed') }}</h4>
                                <p class="text-danger-600 text-xs mt-0.5" id="error-text"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Info & Actions -->
                <div class="lg:col-span-4 space-y-4">
                    
                    <!-- Control Card -->
                    <div class="bg-white rounded-xl shadow-sm border border-secondary-200 p-5">
                        <h3 class="font-semibold text-gray-900 mb-3 flex items-center gap-2 text-sm">
                            <svg class="w-4 h-4 text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                            {{ __('ui.camera_control') }}
                        </h3>
                        
                        <div class="space-y-3">
                            <button onclick="toggleCamera()" id="btn-camera" class="w-full btn btn-outline-primary justify-center py-2 text-sm">
                                <span id="btn-camera-text">{{ __('ui.stop_camera') }}</span>
                            </button>
                            
                            <button onclick="flipCamera()" class="w-full btn btn-outline-secondary justify-center py-2 text-sm group">
                                <svg class="w-4 h-4 mr-2 text-secondary-500 group-hover:text-secondary-700 transition-transform duration-300 group-hover:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                {{ __('ui.flip_camera') }}
                            </button>

                            <div class="relative py-1">
                                <div class="absolute inset-0 flex items-center" aria-hidden="true">
                                    <div class="w-full border-t border-gray-200"></div>
                                </div>
                                <div class="relative flex justify-center">
                                    <span class="px-2 bg-white text-[10px] text-gray-400 font-medium">{{ __('ui.or') }}</span>
                                </div>
                            </div>

                             <label for="qr-input-file" class="w-full btn btn-secondary justify-center cursor-pointer py-2 bg-gray-50 hover:bg-gray-100 border-dashed border-2 border-gray-300 text-sm">
                                <svg class="w-4 h-4 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" /></svg>
                                <span class="text-gray-600">{{ __('ui.upload_image') }}</span>
                                <input type="file" id="qr-input-file" accept="image/*" class="hidden" onchange="scanFromFile(this)">
                            </label>
                        </div>
                    </div>

                    <!-- Instructions -->
                    <div class="bg-blue-50 rounded-xl p-4 border border-blue-100">
                        <h4 class="text-blue-800 font-semibold mb-2 text-xs flex items-center gap-2">
                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            {{ __('ui.guide') }}
                        </h4>
                        <ul class="text-xs text-blue-700 space-y-1.5 list-disc pl-4">
                            <li>{{ __('ui.guide_1') }}</li>
                            <li>{{ __('ui.guide_2') }}</li>
                            <li>{{ __('ui.guide_3') }}</li>
                        </ul>
                    </div>
                </div>

            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://unpkg.com/html5-qrcode" type="text/javascript"></script>
    <script>
        const html5QrCode = new Html5Qrcode("reader");
        let isCameraRunning = false;
        let currentFacingMode = "environment"; // default setup
        const scanLine = document.getElementById('scan-line');
        const cameraPlaceholder = document.getElementById('camera-placeholder');
        const btnCameraText = document.getElementById('btn-camera-text');
        
        // CSS Animation for Scan Line
        const style = document.createElement('style');
        style.innerHTML = `
            @keyframes scan {
                0%, 100% { top: 5%; opacity: 0; }
                10% { opacity: 1; }
                90% { opacity: 1; }
                50% { top: 95%; }
            }
            .animate-scan {
                animation: scan 2s linear infinite;
            }
        `;
        document.head.appendChild(style);

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
             
             if (isCameraRunning) return Promise.resolve();

             const config = { fps: 10, qrbox: { width: 250, height: 250 } };
             return html5QrCode.start({ facingMode: currentFacingMode }, config, onScanSuccess, onScanFailure)
            .then(() => {
                isCameraRunning = true;
                cameraPlaceholder.classList.add('hidden');
                scanLine.classList.remove('hidden');
                updateButtonState(true);
            })
            .catch(err => {
                 let errorMessage = "{{ __('ui.camera_error_default') }}";
                 const errString = err.toString();
                 
                 if (errString.includes("NotAllowedError") || errString.includes("PermissionDeniedError")) {
                     errorMessage = "{{ __('ui.camera_error_not_allowed') }}";
                 } else if (errString.includes("NotFoundError") || errString.includes("DevicesNotFoundError")) {
                     errorMessage = "{{ __('ui.camera_error_not_found') }}";
                 } else if (errString.includes("NotReadableError") || errString.includes("TrackStartError")) {
                     errorMessage = "{{ __('ui.camera_error_not_readable') }}";
                 }

                 document.getElementById('error-text').innerText = errorMessage;
                 errorDiv.classList.remove('hidden');
                 console.error(err);
            });
        };

        const stopCamera = () => {
            if (isCameraRunning) {
                return html5QrCode.stop().then(() => {
                    isCameraRunning = false;
                    html5QrCode.clear();
                    cameraPlaceholder.classList.remove('hidden');
                    scanLine.classList.add('hidden');
                    updateButtonState(false);
                });
            }
            return Promise.resolve();
        };

        const toggleCamera = () => {
            if (isCameraRunning) {
                stopCamera();
            } else {
                startCamera();
            }
        }
        
        const flipCamera = () => {
            const wasRunning = isCameraRunning;
            stopCamera().then(() => {
                currentFacingMode = currentFacingMode === "environment" ? "user" : "environment";
                // If it was running, restart immediately. If not, user has to click start.
                // Or better UX: just start it to show the flip effect.
                startCamera(); 
            });
        }

        const updateButtonState = (isRunning) => {
            const btn = document.getElementById('btn-camera');
            if (isRunning) {
                btnCameraText.innerText = "{{ __('ui.stop_camera') }}";
                btn.classList.replace('btn-outline-primary', 'btn-outline-danger');
            } else {
                btnCameraText.innerText = "{{ __('ui.start_camera') }}";
                btn.classList.replace('btn-outline-danger', 'btn-outline-primary');
            }
        }

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
                         document.getElementById('error-text').innerText = "{{ __('ui.error_process_svg') }}";
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
                    document.getElementById('error-text').innerText = "{{ __('ui.error_scan_image') }}";
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
                        // Use natural dimensions if available, or fallback to parsed width/height
                        let width = img.naturalWidth || img.width;
                        let height = img.naturalHeight || img.height;

                        // Fallback defaults if 0
                        if (!width) width = 1000;
                        if (!height) height = 500;

                        // Maintain decent resolution
                        if (width < 800) {
                            const scale = 800 / width;
                            width *= scale;
                            height *= scale;
                        }

                        const canvas = document.createElement('canvas');
                        canvas.width = width;
                        canvas.height = height;
                        
                        const ctx = canvas.getContext('2d');
                        ctx.fillStyle = "white";
                        ctx.fillRect(0, 0, canvas.width, canvas.height); // White background
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        
                        canvas.toBlob((blob) => {
                            if (blob) resolve(new File([blob], "qr.png", { type: "image/png" }));
                            else reject("Gagal konversi canvas.");
                        }, 'image/png');
                    };
                    img.onerror = () => reject("Gagal muat SVG.");
                    img.src = e.target.result;
                    img.crossOrigin = "anonymous";
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
