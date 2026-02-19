<x-app-layout>
    <div class="py-6" x-data="inventoryDetail()" @open-return-modal.window="initReturn($event.detail)">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header & Actions -->
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-4">
                <div>
                    <div class="flex items-center gap-3">
                        <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                            Detail Riwayat Peminjaman
                        </h2>
                        @if($borrowing->status === 'borrowed')
                            <span class="badge badge-warning">Dipinjam</span>
                        @elseif($borrowing->status === 'returned')
                            <span class="badge badge-success">Selesai</span>
                        @else
                            <span class="badge badge-secondary">{{ ucfirst($borrowing->status) }}</span>
                        @endif
                    </div>
                    <div class="flex items-center gap-2 mt-1.5 text-secondary-500 text-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Dipinjam: {{ $borrowing->borrowed_at ? $borrowing->borrowed_at->translatedFormat('d F Y H:i') : '-' }}
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('inventory.show', $borrowing->sparepart) }}" class="btn btn-secondary">
                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        Detail Barang
                    </a>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column: Borrowing Summary -->
                <div class="lg:col-span-1 flex flex-col gap-6">
                    <!-- Borrower Info Card -->
                    <div class="card p-6">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">Informasi Peminjam</h3>
                        
                        <div class="flex items-center gap-4 mb-4">
                            <div class="w-16 h-16 rounded-full bg-secondary-200 overflow-hidden flex-shrink-0">
                                @if($borrowing->user && $borrowing->user->avatar)
                                    <img src="{{ asset('storage/' . $borrowing->user->avatar) }}" class="w-full h-full object-cover" loading="lazy" alt="{{ $borrowing->user->name }}">
                                @else
                                    <div class="w-full h-full flex items-center justify-center text-secondary-500 text-xl font-bold">
                                        {{ substr($borrowing->user->name ?? 'U', 0, 1) }}
                                    </div>
                                @endif
                            </div>
                            <div class="flex flex-col">
                                <span class="text-lg font-semibold text-secondary-900">{{ $borrowing->user->name ?? 'User Terhapus' }}</span>
                                <span class="text-sm text-secondary-500">{{ $borrowing->user->role ?? '-' }}</span>
                                @if($borrowing->user && $borrowing->user->email)
                                    <span class="text-xs text-secondary-400 mt-1">{{ $borrowing->user->email }}</span>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Borrowing Details Card -->
                    <div class="card p-6">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">Detail Peminjaman</h3>
                        
                        <div class="space-y-4">
                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Barang</span>
                                <p class="text-base text-secondary-900 font-medium mt-1">{{ $borrowing->sparepart->name }}</p>
                                <p class="text-xs text-secondary-500 font-mono">{{ $borrowing->sparepart->part_number }}</p>
                            </div>

                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Total Dipinjam</span>
                                <p class="text-base text-secondary-900 font-bold mt-1">{{ $borrowing->quantity }} {{ $borrowing->sparepart->unit }}</p>
                            </div>

                            @if($borrowing->remaining_quantity !== null)
                                <div>
                                    <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Sisa Belum Dikembalikan</span>
                                    <p class="text-base {{ $borrowing->remaining_quantity > 0 ? 'text-warning-600' : 'text-success-600' }} font-bold mt-1">
                                        {{ $borrowing->remaining_quantity }} {{ $borrowing->sparepart->unit }}
                                    </p>
                                </div>
                            @endif

                            <div>
                                <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Tanggal Pinjam</span>
                                <p class="text-base text-secondary-900 font-medium mt-1">
                                    {{ $borrowing->borrowed_at ? $borrowing->borrowed_at->translatedFormat('d F Y H:i') : '-' }}
                                </p>
                            </div>

                            @if($borrowing->expected_return_at)
                                <div>
                                    <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Estimasi Kembali</span>
                                    <p class="text-base text-secondary-900 font-medium mt-1">
                                        {{ \Carbon\Carbon::parse($borrowing->expected_return_at)->translatedFormat('d F Y') }}
                                    </p>
                                </div>
                            @endif

                            @if($borrowing->notes)
                                <div>
                                    <span class="text-xs text-secondary-400 uppercase tracking-wider font-semibold">Catatan</span>
                                    <p class="text-sm text-secondary-600 mt-1">{{ $borrowing->notes }}</p>
                                </div>
                            @endif
                        </div>

                        @if($borrowing->remaining_quantity > 0)
                            <div class="mt-6 pt-4 border-t border-secondary-100">
                                <button
                                    @click="initReturn({ maxQty: {{ $borrowing->remaining_quantity }}, borrowingId: {{ $borrowing->id }} })"
                                    class="btn btn-primary w-full justify-center"
                                >
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"></path></svg>
                                    Kembalikan Barang
                                </button>
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Right Column: Returns Timeline -->
                <div class="lg:col-span-2">
                    <div class="card p-6">
                        <h3 class="text-lg font-bold text-secondary-900 mb-4 border-b border-secondary-100 pb-2">Riwayat Pengembalian</h3>
                        
                        @if($borrowing->returns->isEmpty())
                            <!-- Empty State -->
                            <div class="flex flex-col items-center justify-center py-12 text-center">
                                <div class="w-20 h-20 rounded-full bg-secondary-100 flex items-center justify-center mb-4">
                                    <svg class="w-10 h-10 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                    </svg>
                                </div>
                                <p class="text-lg font-semibold text-secondary-700 mb-2">Belum Ada Pengembalian</p>
                                <p class="text-sm text-secondary-500 max-w-md">
                                    Barang masih dalam status dipinjam. Riwayat pengembalian akan muncul setelah ada pengembalian pertama.
                                </p>
                            </div>
                        @else
                            <!-- Timeline -->
                            <div class="space-y-6">
                                @foreach($borrowing->returns as $return)
                                    <div class="relative pl-8 pb-6 border-l-2 border-secondary-200 last:pb-0 last:border-l-0">
                                        <!-- Timeline Dot -->
                                        <div class="absolute -left-2 top-0 w-4 h-4 rounded-full"
                                             :class="getItemColor('{{ $return->condition }}')">
                                        </div>

                                        <!-- Return Card -->
                                        <div class="bg-secondary-50 rounded-lg p-4 ml-2">
                                            <div class="flex items-start justify-between mb-3">
                                                <div class="flex-1">
                                                    <p class="text-sm font-semibold text-secondary-900 flex items-center gap-2">
                                                        <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                        </svg>
                                                        {{ $return->return_date ? $return->return_date->translatedFormat('d F Y H:i') : '-' }}
                                                    </p>
                                                </div>
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-bold"
                                                      :class="getBadgeColor('{{ $return->condition }}')">
                                                    @if($return->condition === 'good') Baik
                                                    @elseif($return->condition === 'bad') Rusak
                                                    @elseif($return->condition === 'lost') Hilang
                                                    @else {{ ucfirst($return->condition) }}
                                                    @endif
                                                </span>
                                            </div>

                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-3">
                                                <div>
                                                    <span class="text-xs text-secondary-500 font-semibold">Jumlah Dikembalikan</span>
                                                    <p class="text-lg font-bold text-secondary-900">{{ $return->quantity }} {{ $borrowing->sparepart->unit }}</p>
                                                </div>
                                            </div>

                                            @if($return->notes)
                                                <div class="mb-3">
                                                    <span class="text-xs text-secondary-500 font-semibold">Catatan</span>
                                                    <p class="text-sm text-secondary-700 mt-1">{{ $return->notes }}</p>
                                                </div>
                                            @endif

                                            <!-- Photos Gallery -->
                                            @if($return->photos && count($return->photos) > 0)
                                                <div>
                                                    <span class="text-xs text-secondary-500 font-semibold mb-2 block">Foto Bukti</span>
                                                    <div class="grid grid-cols-2 sm:grid-cols-3 gap-2">
                                                        @foreach($return->photos as $photo)
                                                            <a href="{{ asset('storage/' . $photo) }}" target="_blank" class="group relative aspect-square rounded-lg overflow-hidden bg-secondary-200 hover:ring-2 hover:ring-primary-500 transition-all">
                                                                <img src="{{ asset('storage/' . $photo) }}" alt="Return Evidence" class="w-full h-full object-cover group-hover:scale-105 transition-transform">
                                                                <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 transition-all flex items-center justify-center">
                                                                    <svg class="w-6 h-6 text-white opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                                    </svg>
                                                                </div>
                                                            </a>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    <!-- Return Modal -->
    <template x-teleport="body">
        <div x-show="returnModalOpen" 
             style="display: none;"
             class="fixed inset-0 z-[99]" 
             aria-labelledby="modal-title" 
             role="dialog" 
             aria-modal="true">
            
            <div class="flex min-h-screen items-center justify-center py-12 px-4 sm:px-6">
                <!-- Backdrop -->
                <div x-show="returnModalOpen"
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0" 
                     x-transition:enter-end="opacity-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100" 
                     x-transition:leave-end="opacity-0" 
                     class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                     @click="returnModalOpen = false"
                     aria-hidden="true"></div>
        
                <!-- Modal Panel -->
                <div x-show="returnModalOpen" 
                     x-transition:enter="ease-out duration-300" 
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave="ease-in duration-200" 
                     x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" 
                     x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" 
                     class="relative bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:max-w-lg w-full flex flex-col max-h-[85vh]">
                    
                    <!-- Header (Fixed) -->
                    <div class="bg-white px-4 py-4 sm:px-6 border-b border-gray-200 flex-none z-10 shadow-sm">
                        <div class="flex items-center">
                            <div class="flex-shrink-0 flex items-center justify-center h-10 w-10 rounded-full bg-success-100 mx-0">
                                <svg class="h-6 w-6 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </div>
                            <div class="ml-4 text-left">
                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                    {{ __('ui.return_item_title') }}
                                </h3>
                            </div>
                        </div>
                    </div>

                    <!-- Form -->
                    <form :action="`/inventory/borrow/${selectedBorrowing}/return`" method="POST" enctype="multipart/form-data" class="flex flex-col flex-1 min-h-0" @submit.prevent="submitReturn">
                        @csrf
                        
                        <!-- Scrollable Content -->
                        <div class="flex-1 overflow-y-auto px-4 pt-2 pb-4 sm:px-6 space-y-4 custom-scrollbar">
                            
                            <!-- Error Display -->
                            <div x-ref="errorContainer" x-show="Object.keys(errors).length > 0" class="mb-4 bg-danger-50 border border-danger-200 text-danger-700 px-4 py-3 rounded relative">
                                <strong class="font-bold">{{ __('ui.save_failed') }}</strong>
                                <ul class="list-disc list-inside text-sm mt-1">
                                    <template x-for="(fieldErrors, field) in errors" :key="field">
                                        <template x-for="error in fieldErrors">
                                            <li x-text="error"></li>
                                        </template>
                                    </template>
                                </ul>
                            </div>
                            <div x-show="successMessage" class="mb-4 bg-success-50 border border-success-200 text-success-700 px-4 py-3 rounded relative">
                                <strong class="font-bold" x-text="successMessage"></strong>
                            </div>

                            <!-- Quantity -->
                            <div>
                                <label for="return_qty" class="block text-sm font-medium text-gray-700">{{ __('ui.return_quantity') }} <span class="text-danger-500">*</span></label>
                                <div class="mt-1 relative rounded-md shadow-sm">
                                    <input type="number" 
                                           name="return_quantity" 
                                           id="return_qty" 
                                           x-model="returnQty"
                                           :max="maxReturnQty"
                                           min="1"
                                           required
                                           class="focus:ring-success-500 focus:border-success-500 block w-full sm:text-sm border-gray-300 rounded-md"
                                           placeholder="Jumlah">
                                </div>
                                <p class="mt-1 text-xs" 
                                   :class="{'text-danger-600': !isValid, 'text-gray-500': isValid}">
                                    Maks: <span x-text="maxReturnQty"></span>
                                </p>
                            </div>

                            <!-- Condition (Custom Dropdown) -->
                            <div @click.outside="dropdownOpen = false" class="relative z-50">
                                <label class="block text-sm font-medium text-gray-700 mb-1">{{ __('ui.condition') }} <span class="text-danger-500">*</span></label>
                                <div class="relative">
                                    <button type="button" @click="dropdownOpen = !dropdownOpen"
                                            class="input-field w-full text-left flex justify-between items-center rounded-xl py-2.5 px-4 text-sm cursor-pointer border border-gray-300 hover:border-primary-400 focus:ring-2 ring-primary-500 bg-white transition-all shadow-sm">
                                        <span x-text="conditionLabel" :class="{'text-gray-900': returnCondition, 'text-gray-500': !returnCondition}" class="truncate mr-2"></span>
                                        <svg class="w-4 h-4 text-gray-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': dropdownOpen}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                    </button>

                                    <div x-show="dropdownOpen" 
                                         style="display: none;"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         class="absolute z-50 mt-1 w-full min-w-[100%] bg-white rounded-xl shadow-xl border border-secondary-100 overflow-hidden">
                                        <div class="max-h-60 overflow-y-auto p-1 space-y-0.5">
                                            <template x-for="option in conditionOptions" :key="option.value">
                                                <div @click="selectCondition(option)" 
                                                     class="px-3 py-2 rounded-lg cursor-pointer text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors text-sm"
                                                     :class="{'bg-primary-50 text-primary-700 font-medium': returnCondition === option.value}">
                                                    <span x-text="option.label"></span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                                <input type="hidden" name="return_condition" x-model="returnCondition">
                            </div>

                            <!-- Multiple Evidence Images & Camera (Hidden if Lost) -->
                            <div class="mt-4" x-show="returnCondition !== 'lost'" x-data="{ 
                                files: [], 
                                previews: [],
                                cameraOpen: false,
                                stream: null,
                                
                                addFiles(e) {
                                    const newFiles = Array.from(e.target.files);
                                    this.processFiles(newFiles);
                                },

                                processFiles(newFiles) {
                                    if (this.files.length + newFiles.length > 5) {
                                        alert('Maksimal 5 foto');
                                        return;
                                    }
                                    this.files = this.files.concat(newFiles);
                                    this.updateInput();
                                    newFiles.forEach(file => {
                                        const reader = new FileReader();
                                        reader.onload = (e) => { this.previews.push(e.target.result); };
                                        reader.readAsDataURL(file);
                                    });
                                    $dispatch('file-change', this.files.length);
                                },

                                removeFile(index) {
                                    this.files.splice(index, 1);
                                    this.previews.splice(index, 1);
                                    this.updateInput();
                                    $dispatch('file-change', this.files.length);
                                },

                                updateInput() {
                                    const dt = new DataTransfer();
                                    this.files.forEach(file => dt.items.add(file));
                                    $refs.fileInput.files = dt.files;
                                },

                                // Camera Logic
                                async startCamera() {
                                    this.cameraOpen = true;
                                    this.$nextTick(async () => {
                                        try {
                                            this.stream = await navigator.mediaDevices.getUserMedia({ video: { facingMode: 'environment' } });
                                            this.$refs.video.srcObject = this.stream;
                                        } catch (err) {
                                            alert('Tidak dapat mengakses kamera: ' + err.message);
                                            this.cameraOpen = false;
                                        }
                                    });
                                },

                                stopCamera() {
                                    if (this.stream) {
                                        this.stream.getTracks().forEach(track => track.stop());
                                        this.stream = null;
                                    }
                                    this.cameraOpen = false;
                                },

                                capturePhoto() {
                                    const video = this.$refs.video;
                                    const canvas = this.$refs.canvas;
                                    canvas.width = video.videoWidth;
                                    canvas.height = video.videoHeight;
                                    const ctx = canvas.getContext('2d');
                                    ctx.drawImage(video, 0, 0, canvas.width, canvas.height);
                                    
                                    canvas.toBlob(blob => {
                                        const file = new File([blob], 'camera_' + Date.now() + '.jpg', { type: 'image/jpeg' });
                                        this.processFiles([file]);
                                        this.stopCamera();
                                    }, 'image/jpeg', 0.8);
                                },

                                triggerGallery() {
                                    $refs.galleryInput.click();
                                }
                            }">
                                <label class="block text-sm font-medium text-gray-700 mb-2">Bukti Foto <span class="text-danger-500">*</span></label>
                                
                                <!-- Split Buttons (Pill/Chip Style - Full Width) -->
                                <div class="grid grid-cols-2 gap-3 mb-4">
                                    <button type="button" @click="startCamera" 
                                            class="w-full inline-flex justify-center items-center px-4 py-2 rounded-full bg-blue-50 text-blue-700 hover:bg-blue-100 hover:text-blue-800 transition-colors duration-200 border border-transparent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                                        <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 9a2 2 0 012-2h.93a2 2 0 001.664-.89l.812-1.22A2 2 0 0110.07 4h3.86a2 2 0 011.664.89l.812 1.22A2 2 0 0018.07 7H19a2 2 0 012 2v9a2 2 0 01-2 2H5a2 2 0 01-2-2V9z" />
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 13a3 3 0 11-6 0 3 3 0 016 0z" />
                                        </svg>
                                        <span class="text-sm font-semibold">Buka Kamera</span>
                                    </button>

                                    <button type="button" @click="triggerGallery" 
                                            class="w-full inline-flex justify-center items-center px-4 py-2 rounded-full bg-purple-50 text-purple-700 hover:bg-purple-100 hover:text-purple-800 transition-colors duration-200 border border-transparent focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-purple-500">
                                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5 mr-2" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                        </svg>
                                        <span class="text-sm font-semibold">Pilih Galeri</span>
                                    </button>
                                </div>

                                <!-- Camera Overlay -->
                                <div x-show="cameraOpen" 
                                        style="display: none;"
                                        class="fixed inset-0 z-[60] bg-black bg-opacity-90 flex flex-col items-center justify-center p-4">
                                    <div class="relative w-full max-w-lg bg-black rounded-lg overflow-hidden shadow-2xl">
                                        <video x-ref="video" autoplay playsinline class="w-full h-auto object-cover"></video>
                                        <canvas x-ref="canvas" class="hidden"></canvas>
                                        
                                        <div class="absolute bottom-6 left-0 right-0 flex justify-center gap-6 items-center">
                                            <button type="button" @click="stopCamera" class="p-3 bg-white/20 hover:bg-white/30 rounded-full text-white backdrop-blur-sm transition-all">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                            <button type="button" @click="capturePhoto" class="p-4 bg-white rounded-full text-primary-600 shadow-lg hover:scale-105 transition-transform">
                                                <div class="w-12 h-12 rounded-full border-4 border-primary-600 flex items-center justify-center">
                                                    <div class="w-10 h-10 bg-primary-600 rounded-full"></div>
                                                </div>
                                            </button>
                                        </div>
                                    </div>
                                    <p class="text-white mt-4 text-sm font-medium">Pose dan ambil foto</p>
                                </div>

                                <!-- Hidden Inputs -->
                                <input type="file" x-ref="galleryInput" accept="image/*" multiple class="hidden" @change="addFiles($event)">
                                <input type="file" name="return_photos[]" multiple class="hidden" x-ref="fileInput">

                                <!-- Previews Grid -->
                                <div class="grid grid-cols-3 gap-3" x-show="previews.length > 0">
                                    <template x-for="(preview, index) in previews" :key="index">
                                        <div class="relative aspect-square rounded-lg overflow-hidden border border-gray-200 group bg-gray-50">
                                            <img :src="preview" class="w-full h-full object-cover">
                                            <button type="button" @click="removeFile(index)" class="absolute top-1 right-1 bg-red-500 text-white rounded-full p-1 shadow-md hover:bg-red-600 transition-colors z-10 w-6 h-6 flex items-center justify-center">
                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </template>
                                </div>
                                <p class="mt-2 text-xs text-gray-500" x-show="files.length > 0">Maksimal 5 foto (Wajib)</p>
                                <input type="hidden" id="file_count" :value="files.length">
                            </div>

                            <!-- Notes -->
                            <div>
                                <label for="notes" class="block text-sm font-medium text-gray-700">Catatan <span class="text-secondary-400 font-normal text-xs">(Opsional)</span></label>
                                <textarea name="return_notes" id="notes" rows="2" class="form-textarea mt-1 block w-full rounded-md border-gray-300 focus:border-primary-500 focus:ring-primary-500 sm:text-sm" placeholder="Keterangan tambahan..."></textarea>
                            </div>
                        </div>

                        <!-- Footer Actions (Fixed) -->
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 flex flex-row-reverse gap-3 border-t border-gray-200 flex-none z-10 shadow-[0_-2px_4px_rgba(0,0,0,0.05)]"
                                x-data="{ hasFiles: false }" 
                                @file-change.window="hasFiles = $event.detail > 0">
                            <button type="submit" 
                                    :disabled="!isValid || isSubmitting || (returnCondition !== 'lost' && !hasFiles)"
                                    :class="{ 'opacity-50 cursor-not-allowed': !isValid || isSubmitting || (returnCondition !== 'lost' && !hasFiles), 'hover:bg-primary-700': isValid && !isSubmitting && (returnCondition === 'lost' || hasFiles) }"
                                    class="flex-1 sm:flex-none sm:w-auto inline-flex justify-center items-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm transition-all">
                                <span x-show="!isSubmitting">Konfirmasi</span>
                                <span x-show="isSubmitting" class="flex items-center gap-2">
                                    <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Proses...
                                </span>
                            </button>
                            <button type="button" @click="returnModalOpen = false" class="flex-1 sm:flex-none sm:w-auto mt-0 inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </template>

    @include('inventory.partials.alpine_script')
    </div>
</x-app-layout>
