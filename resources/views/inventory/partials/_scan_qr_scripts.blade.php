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

        const playBeep = () => {
            try {
                const AudioContext = window.AudioContext || window.webkitAudioContext;
                if(!AudioContext) return;
                const ctx = new AudioContext();
                const oscillator = ctx.createOscillator();
                const gainNode = ctx.createGain();
                
                oscillator.connect(gainNode);
                gainNode.connect(ctx.destination);
                
                oscillator.type = 'sine';
                oscillator.frequency.value = 800;
                gainNode.gain.setValueAtTime(0, ctx.currentTime);
                gainNode.gain.linearRampToValueAtTime(1, ctx.currentTime + 0.05);
                gainNode.gain.linearRampToValueAtTime(0, ctx.currentTime + 0.2);
                
                oscillator.start(ctx.currentTime);
                oscillator.stop(ctx.currentTime + 0.2);
            } catch (e) {
                console.warn('Audio API not supported');
            }
        };

        const onScanSuccess = (decodedText, decodedResult) => {
            playBeep();
            stopCamera().then(() => {
                handleResult(decodedText);
            });
        };

        const handleResult = (decodedText) => {
            const resultDiv = document.getElementById('result');
            const errorDiv = document.getElementById('error-message');

            let targetUrl = decodedText;

            try {
                const url = new URL(decodedText);
                
                // Fallback pintar: Jika QR lama masih pakai localhost, otomatis ubah ke domain live saat ini
                if (url.origin.includes('localhost') || url.origin.includes('127.0.0.1')) {
                    targetUrl = window.location.origin + url.pathname + url.search;
                } 
                // Keamanan: Cegah open redirect ke web lain
                else if (url.origin !== window.location.origin) {
                    document.getElementById('error-text').innerText = 'QR Code ini tidak berasal dari sistem Azventory. Scan dibatalkan.';
                    errorDiv.classList.remove('hidden');
                    return;
                }
            } catch (e) {
                // Bukan URL (mungkin sekedar Part Number / Nama Barang)
                targetUrl = `{{ route('inventory.index') }}?search=${encodeURIComponent(decodedText)}`;
            }

            errorDiv.classList.add('hidden');
            resultDiv.classList.remove('hidden');

            // Redirect setelah terkonfirmasi aman
            setTimeout(() => {
                window.location.href = targetUrl;
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
                
                const capabilities = html5QrCode.getRunningTrackCameraCapabilities();
                if (capabilities && typeof capabilities.torch !== 'undefined') {
                    document.getElementById('btn-torch').classList.remove('hidden');
                } else {
                    document.getElementById('btn-torch').classList.add('hidden');
                }
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

        let isTorchOn = false;
        const toggleTorch = () => {
            isTorchOn = !isTorchOn;
            html5QrCode.applyVideoConstraints({
                advanced: [{ torch: isTorchOn }]
            }).then(() => {
                const btnTorchText = document.getElementById('btn-torch-text');
                const torchIcon = document.getElementById('torch-icon');
                if (isTorchOn) {
                    btnTorchText.innerText = "Matikan Senter";
                    torchIcon.classList.replace('text-secondary-500', 'text-yellow-500');
                } else {
                    btnTorchText.innerText = "Nyalakan Senter";
                    torchIcon.classList.replace('text-yellow-500', 'text-secondary-500');
                }
            }).catch(err => {
                console.warn('Senter tidak didukung', err);
                isTorchOn = false;
            });
        };

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

        // Paste Image Support (Ctrl+V)
        window.addEventListener('paste', e => {
            if (e.clipboardData && e.clipboardData.files && e.clipboardData.files.length > 0) {
                const file = e.clipboardData.files[0];
                if (file.type.startsWith('image/')) {
                    scanFromFile({ files: [file] });
                }
            }
        });

        // Drag and Drop Image Support
        window.addEventListener('dragover', e => {
            e.preventDefault();
        });
        window.addEventListener('drop', e => {
            e.preventDefault();
            if (e.dataTransfer && e.dataTransfer.files && e.dataTransfer.files.length > 0) {
                const file = e.dataTransfer.files[0];
                if (file.type.startsWith('image/')) {
                    scanFromFile({ files: [file] });
                }
            }
        });

        // Handle BFCache (Back/Forward Cache) revival
        window.addEventListener('pageshow', function (event) {
            if (event.persisted) {
                // If user pressed back button, restart the camera
                startCamera();
            }
        });
    </script>
