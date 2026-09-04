    <!-- Tesseract.js (Di-host secara LOKAL untuk mengantisipasi blokir CORS ISP Indonesia / Browser Security) -->
    <script src="{{ asset('vendor/tesseract/tesseract.min.js') }}"></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('inventoryForm', () => ({
                type: @js(old('type', $sparepart->type)),
                partNumber: @js(old('part_number', $sparepart->part_number)),
                isLocked: false,
                itemName: @js(old('name', $sparepart->name)),
                itemBrand: @js(old('brand_id', $sparepart->brand_id)),
                itemCategory: @js(old('category_id', $sparepart->category_id)),
                itemColor: @js(old('color', $sparepart->color)), 
                itemUnit: @js(old('unit', $sparepart->unit)),
                itemPrice: @js(old('price', $sparepart->price)),
                imagePreview: null,
                existingImage: @js(old('existing_image', $sparepart->image ? asset('storage/' . $sparepart->image) : '')),
                isLoading: false,
                isSubmitting: false,

                saveDraft() {
                    const draft = {
                        type: this.type,
                        partNumber: this.partNumber,
                        itemName: this.itemName,
                        itemBrand: this.itemBrand,
                        itemCategory: this.itemCategory,
                        itemColor: this.itemColor,
                        itemUnit: this.itemUnit,
                        itemPrice: this.itemPrice
                    };
                    localStorage.setItem('inventory_draft_' + window.location.pathname, JSON.stringify(draft));
                },

                init() {
                    // 1. Setup Global Trigger for Scan Modal
                    window.triggerScanModal = () => {
                        console.log('Trigger Scan Modal via Global Function');
                        this.openScanModal();
                    }

                    // 2. Check for Pre-filled PN
                    if (this.partNumber) {
                         this.checkPN(true);
                    }

                    // Pre-fill image preview if existing
                    if (this.existingImage) {
                        this.imagePreview = this.existingImage;
                    }

                    // Restore Image from LocalStorage if Validation Failed (same logic as create)
                    const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
                    if (hasErrors) {
                        const storedImage = localStorage.getItem('temp_inventory_image');
                        if (storedImage) {
                            this.imagePreview = storedImage;
                            fetch(storedImage)
                                .then(res => res.blob())
                                .then(blob => {
                                    const file = new File([blob], "restored-image.png", { type: blob.type });
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(file);
                                    this.$nextTick(() => {
                                        if (this.$refs.fileInput) {
                                            this.$refs.fileInput.files = dataTransfer.files;
                                            this.fileName = file.name;
                                        }
                                    });
                                });
                        }
                    } else {
                        localStorage.removeItem('temp_inventory_image');
                        
                        // Restore form draft if no validation errors
                        const draftStr = localStorage.getItem('inventory_draft_' + window.location.pathname);
                        if (draftStr) {
                            try {
                                const draft = JSON.parse(draftStr);
                                this.type = draft.type || this.type;
                                this.partNumber = draft.partNumber || this.partNumber;
                                this.itemName = draft.itemName || this.itemName;
                                this.itemBrand = draft.itemBrand || this.itemBrand;
                                this.itemCategory = draft.itemCategory || this.itemCategory;
                                this.itemColor = draft.itemColor || this.itemColor;
                                this.itemUnit = draft.itemUnit || this.itemUnit;
                                this.itemPrice = draft.itemPrice || this.itemPrice;
                            } catch(e) {}
                        }
                    }

                    // Setup auto-save watchers
                    this.$watch('type', () => this.saveDraft());
                    this.$watch('partNumber', () => this.saveDraft());
                    this.$watch('itemName', () => this.saveDraft());
                    this.$watch('itemBrand', () => this.saveDraft());
                    this.$watch('itemCategory', () => this.saveDraft());
                    this.$watch('itemColor', () => this.saveDraft());
                    this.$watch('itemUnit', () => this.saveDraft());
                    this.$watch('itemPrice', () => this.saveDraft());

                    // Clear draft on submit
                    document.querySelector('form').addEventListener('submit', () => {
                        localStorage.removeItem('inventory_draft_' + window.location.pathname);
                    });
                },

                // Auto-fill Logic
                async checkPN(isInitialLoad = false) {
                    if (!this.partNumber) return;
                    
                    this.isLoading = true;
                    try {
                        const response = await axios.get('{{ route("inventory.check-part-number") }}', {
                            params: { part_number: this.partNumber }
                        });

                        if (response.data.exists) {
                            const data = response.data.data;
                            
                            if (!isInitialLoad) {
                                this.itemName = data.name;
                                this.itemBrand = data.brand_id;
                                this.itemCategory = data.category_id;
                                this.type = data.type;
                                this.itemUnit = data.unit;
                                this.itemPrice = data.price; // Auto-fill price
                                
                                // Handle Image
                                if (data.image_url) {
                                    this.imagePreview = data.image_url;
                                    this.existingImage = data.image_path;
                                }
                            }

                            this.isLocked = true;
                            console.log('Produk ditemukan, data diisi otomatis (atau dilock).');
                        } else {
                            // PN baru (belum ada di database): unlock semua field
                            this.isLocked = false;
                        }
                    } catch (error) {
                        console.error('Error checking PN:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                // OCR Functionality
                scanModalOpen: false,
                ocrLoading: false,
                
                scanErrorMsg: null,
                scanSuccessMsg: null,
                scanRawText: null,
                stream: null,
                debugMode: false,
                debugImage: null,
                debugLog: '',
                
                log(msg) {
                    if (this.debugMode) {
                        this.debugLog += msg + "\n";
                    }
                    console.log("[OCR] " + msg);
                },
                videoDevices: [],
                currentDeviceIndex: 0,
                currentDeviceLabel: '',

                openScanModal() {
                    this.scanModalOpen = true;
                    this.getVideoDevices().then(() => {
                        this.startCamera();
                    });
                },

                closeScanModal() {
                    this.stopCamera();
                    this.scanModalOpen = false;
                    this.ocrLoading = false;
                    this.scanErrorMsg = null;
                    this.scanSuccessMsg = null;
                    this.scanRawText = null;
                    this.debugLog = '';
                },

                async getVideoDevices() {
                    try {
                        const devices = await navigator.mediaDevices.enumerateDevices();
                        this.videoDevices = devices.filter(device => device.kind === 'videoinput');
                    } catch (err) {
                        console.error("Error enumerating devices:", err);
                    }
                },

                async switchCamera() {
                    if (this.videoDevices.length < 2) return;
                    this.currentDeviceIndex = (this.currentDeviceIndex + 1) % this.videoDevices.length;
                    this.stopCamera();
                    await this.startCamera();
                },

                async startCamera() {
                    try {
                        const constraints = { video: {} };
                        if (this.videoDevices.length > 0) {
                            const deviceId = this.videoDevices[this.currentDeviceIndex].deviceId;
                            constraints.video.deviceId = { exact: deviceId };
                            this.currentDeviceLabel = this.videoDevices[this.currentDeviceIndex].label;
                        } else {
                            constraints.video.facingMode = 'environment';
                        }
                        this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                        this.$refs.video.srcObject = this.stream;
                        if (!this.currentDeviceLabel && this.videoDevices.length > 0) {
                            this.getVideoDevices().then(() => {
                                if (this.videoDevices[this.currentDeviceIndex]) {
                                    this.currentDeviceLabel = this.videoDevices[this.currentDeviceIndex].label;
                                }
                            });
                        }
                    } catch (err) {
                        console.error("Error detecting camera:", err);
                        this.scanErrorMsg = "{{ __('ui.camera_access_denied') }}";
                    }
                },

                stopCamera() {
                    if (this.stream) {
                        this.stream.getTracks().forEach(track => track.stop());
                        this.stream = null;
                    }
                },

                async captureAndScan() {
                    if (!this.stream) return;

                    this.ocrLoading = true;
                    this.scanErrorMsg = null;
                    this.scanSuccessMsg = null;
                    this.scanRawText = null;
                    this.debugLog = '';

                    const video = this.$refs.video;
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);
                    const image = canvas.toDataURL('image/png');
                    const processedImage = await this.preprocessImage(image);
                    this.debugImage = processedImage; 
                    await this.processFullAnalysis(processedImage);
                },

                async preprocessImage(imageSource) {
                    return new Promise((resolve) => {
                        this.log("Memulai Pra-Pemrosesan Gambar...");
                        const img = new Image();
                        img.onload = () => {
                            this.log(`Resolusi Asli: ${img.width}x${img.height}`);
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            
                            let width = img.width;
                            let height = img.height;
                            const MAX_WIDTH = 1200;
                            const MAX_HEIGHT = 1200;

                            if (width > MAX_WIDTH || height > MAX_HEIGHT) {
                                if (width > height) {
                                    height = Math.round((MAX_WIDTH / width) * height);
                                    width = MAX_WIDTH;
                                } else {
                                    width = Math.round((MAX_HEIGHT / height) * width);
                                    height = MAX_HEIGHT;
                                }
                            }
                            this.log(`Resolusi Setelah Skala: ${width}x${height}`);

                            canvas.width = width;
                            canvas.height = height;
                            
                            ctx.imageSmoothingEnabled = true; 
                            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                            
                            this.log("Mengubah format gambar tanpa filter agresif...");
                            resolve(canvas.toDataURL('image/png'));
                        };
                        img.onerror = () => {
                            this.log("Gagal memuat gambar untuk canvas.");
                            resolve(imageSource); // fallback
                        };
                        img.src = imageSource;
                    });
                },

                handleFileUpload(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    this.ocrLoading = true;
                    this.scanErrorMsg = null;
                    this.scanSuccessMsg = null;
                    this.scanRawText = null;
                    this.debugLog = '';

                    const reader = new FileReader();
                    reader.onload = async (event) => {
                        const processedImage = await this.preprocessImage(event.target.result);
                        this.debugImage = processedImage; 
                        await this.processFullAnalysis(processedImage);
                    };
                    reader.readAsDataURL(file);
                },

                async processFullAnalysis(imageSource) {
                    let worker = null;
                    try {
                        this.ocrLoading = true;
                        this.log("Menginisialisasi Mesin OCR Tesseract...");
                        worker = Tesseract.createWorker({
                            workerPath: '{{ asset("vendor/tesseract/worker.min.js") }}',
                            corePath: '{{ asset("vendor/tesseract/tesseract-core.wasm.js") }}',
                            langPath: '{{ asset("vendor/tesseract/lang-data") }}',
                            logger: m => {
                                if(m.status === 'recognizing text') {
                                    this.log(`Proses OCR: ${Math.round(m.progress * 100)}%`);
                                } else {
                                    this.log(`Tesseract: ${m.status}`);
                                }
                            },
                        });
                        this.log("Pekerja Tesseract disiapkan. Memuat bahasa...");
                        await worker.load();
                        await worker.loadLanguage('eng');
                        await worker.initialize('eng');
                        await worker.setParameters({ 
                            tessedit_char_whitelist: '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ-/. ',
                            tessedit_pageseg_mode: '11' 
                        });
                        const { data: { text } } = await worker.recognize(imageSource);
                        this.log("Teks berhasil diekstrak.");
                        const rawText = text.toUpperCase();
                        this.scanRawText = rawText;
                        
                        const knownBrands = ['LENOVO', 'DELL', 'HP', 'ASUS', 'ACER', 'APPLE', 'SAMSUNG', 'TOSHIBA', 'SONY', 'MSI', 'LOGITECH', 'CANON', 'EPSON', 'PROLINK', 'UGREEN'];
                        let foundBrand = '';
                        for (const brand of knownBrands) {
                            if (rawText.toUpperCase().includes(brand)) {
                                foundBrand = brand.charAt(0) + brand.slice(1).toLowerCase(); 
                                break; 
                            }
                        }

                        // 1.5. Name / Description Detection
                        let foundName = '';
                        const descRegex = /(?:DESC|DESCRIPTION|NAME)[\s.:]*([^\n\r]+)/i;
                        const matchDesc = rawText.match(descRegex);
                        if (matchDesc && matchDesc[1]) {
                            foundName = matchDesc[1].trim();
                            console.log("Found Description:", foundName);
                        }

                        // 2. Part Number Detection
                        let foundPN = '';
                        
                        // Heuristic A: Explicit Label "PN", "P/N", "Part No", "Orig.PN", etc.
                        const pnRegex = /(?:ORIG\.?|SHIP|MACHINE)?[\s\.]*(?:P\/N|PN|PART NO|PART NUMBER)[\s.:]*([A-Z0-9\-\/]{3,})/i;
                        const matchA = rawText.match(pnRegex);
                        if (matchA && matchA[1]) {
                            foundPN = matchA[1].trim();
                            console.log("Found PN using Heuristic A:", foundPN);
                        }
                        
                        // Heuristic B: Line by line inspection
                        if (!foundPN) {
                            const lines = rawText.split('\n');
                            for (let i = 0; i < lines.length; i++) {
                                let line = lines[i].trim();
                                if(!line) continue;
                                
                                // Look for standalone standard PN formats
                                if (/^[A-Z0-9]{2,}-[A-Z0-9]{3,}$/i.test(line) && line.length > 5 && line.length < 20) {
                                     foundPN = line;
                                     console.log("Found PN using Heuristic B (Regex pattern):", foundPN);
                                     break;
                                }
                                
                                // If the line contains "S/N" (Serial Number) or "MAC", we skip it
                                if (/S\/N|SN:|MAC/i.test(line)) continue;
                            }
                        }

                        if (foundPN) {
                            // Typos correction: Lenovo specific PNs often start with '5' but OCR reads 'S' or vice-versa
                            if (foundPN.startsWith('555') && foundPN.length >= 8) {
                                foundPN = '5SS' + foundPN.substring(3);
                            } else if (foundPN.startsWith('582') && foundPN.length >= 8) {
                                // '8' is often misread from 'B' (e.g. 5B2...)
                                foundPN = '5B2' + foundPN.substring(3);
                            } else if (foundPN.startsWith('S82') && foundPN.length >= 8) {
                                // 'S' for '5' and '8' for 'B'
                                foundPN = '5B2' + foundPN.substring(3);
                            } else if (foundPN.startsWith('SB2') && foundPN.length >= 8) {
                                foundPN = '5B2' + foundPN.substring(3);
                            }
                            // Clean stray characters from extremities
                            this.partNumber = foundPN.replace(/^[^A-Z0-9]+|[^A-Z0-9]+$/g, '');
                            this.scanSuccessMsg = `Part Number terdeteksi: ${this.partNumber}`;
                            
                            // Di halaman Edit, kita isi jika kosong agar tidak menimpa data eksisting sembarangan
                            if (foundBrand && !this.itemBrand) this.itemBrand = foundBrand;
                            if (foundName && (!this.itemName || this.itemName.trim() === '')) this.itemName = foundName;
                            
                            // Beri jeda 1 detik agar pengguna sempat membaca 'Sukses', lalu tutuplah pop-up modal
                            setTimeout(() => {
                                this.closeScanModal();
                            }, 1000);
                        } else {
                            this.scanErrorMsg = "Part Number tidak dapat ditemukan dalam gambar. Coba pastikan gambar lebih jelas dan terang.";
                            this.log("Gagal mengekstrak Part Number yang valid.");
                        }

                    } catch (error) {
                        console.error('OCR Error:', error);
                        this.log(`Error Terjadi: ${error.message || error}`);
                        this.scanErrorMsg = `Gagal menganalisis: ${error.message || error}`;
                    } finally {
                        this.ocrLoading = false;
                        if (worker) {
                            await worker.terminate();
                            this.log("Pekerja Tesseract dihentikan.");
                        }
                    }
                }
            }))
        })
    </script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const urlParams = new URLSearchParams(window.location.search);
            if (urlParams.get('focus') === 'price') {
                const priceInput = document.getElementById('price');
                if (priceInput) {
                    setTimeout(() => {
                        priceInput.focus();
                        priceInput.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    }, 500);
                }
            }
            
            // Auto-scroll to first validation error if exists
            setTimeout(() => {
                const firstError = document.querySelector('.text-red-600, .text-danger-500, .text-danger-600, [class*="text-red-"]');
                if (firstError) {
                    firstError.scrollIntoView({ behavior: 'smooth', block: 'center' });
                    const input = firstError.closest('div, .relative, .card')?.querySelector('input:not([type="hidden"]), select, textarea');
                    if (input) {
                        input.focus({preventScroll: true});
                    }
                }
            }, 300);
        });
    </script>
