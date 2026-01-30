<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                    {{ __('Tambah Sparepart Baru') }}
                </h2>
                <p class="mt-1 text-sm text-secondary-500">Isi detail sparepart di bawah ini untuk menambahkan ke inventaris.</p>
            </div>



            <form action="{{ route('superadmin.inventory.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-4" x-data="inventoryForm()"
                     @trigger-check-pn="checkPN()"
                     @update-pn="partNumber = $event.detail"
                     @update-name="itemName = $event.detail"
                     @update-brand="itemBrand = $event.detail"
                     @update-category="itemCategory = $event.detail">
                    <!-- Section 1: Informasi Dasar -->
                    <div class="card p-6 overflow-visible">
                        <div class="mb-4 border-b border-secondary-100 pb-2">
                            <h3 class="text-lg font-semibold text-secondary-900">Informasi Dasar</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Tipe Barang -->
                            <div class="col-span-1 md:col-span-2">
                                <label class="input-label mb-3 block">Tipe Barang <span class="text-danger-500">*</span></label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-4 hover:bg-secondary-50 transition-all duration-200" :class="{ 'border-primary-500 bg-primary-50 ring-1 ring-primary-500': type === 'sale', 'border-secondary-200': type !== 'sale', 'opacity-50 cursor-not-allowed': isLocked }">
                                        <div class="mt-1">
                                            <input type="radio" name="type" value="sale" x-model="type" class="text-primary-600 focus:ring-primary-500 w-5 h-5" :disabled="isLocked">
                                        </div>
                                        <div>
                                            <span class="block font-semibold text-secondary-900 text-base">Barang Dijual (Sale)</span>
                                            <span class="block text-sm text-secondary-500 mt-1">Barang dagangan dengan stok dan harga jual.</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-4 hover:bg-secondary-50 transition-all duration-200" :class="{ 'border-primary-500 bg-primary-50 ring-1 ring-primary-500': type === 'asset', 'border-secondary-200': type !== 'asset', 'opacity-50 cursor-not-allowed': isLocked }">
                                        <div class="mt-1">
                                            <input type="radio" name="type" value="asset" x-model="type" class="text-primary-600 focus:ring-primary-500 w-5 h-5" :disabled="isLocked">
                                        </div>
                                        <div>
                                            <span class="block font-semibold text-secondary-900 text-base">Inventaris (Asset)</span>
                                            <span class="block text-sm text-secondary-500 mt-1">Aset internal kantor, dapat dipinjamkan.</span>
                                        </div>
                                    </label>
                                </div>
                                <input type="hidden" name="type" x-model="type"> <!-- Hidden input ensures value is sent even if disabled -->
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <!-- Row 1: PN & Name -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 col-span-full">
                                <!-- Part Number -->
                                <div>
                                    <label for="part_number" class="input-label">Part Number (PN) <span class="text-danger-500">*</span></label>
                                    <div class="relative flex gap-2" x-data="{
                                        open: false,
                                        search: '',
                                        selected: '{{ old('part_number') }}',
                                        options: {{ json_encode($partNumbers) }} || [],
                                        get filteredOptions() {
                                            if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                            return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                        },
                                        select(value) {
                                            this.selected = value;
                                            this.search = value;
                                            this.$dispatch('update-pn', value);
                                            this.open = false;
                                            this.$dispatch('trigger-check-pn');
                                        },
                                        createNew() {
                                            let term = this.search.toUpperCase();
                                            this.select(term);
                                        },
                                        init() {
                                            if (this.selected) {
                                                this.$dispatch('update-pn', this.selected);
                                                this.$dispatch('trigger-check-pn');
                                                this.search = this.selected;
                                            }
                                            this.$watch('partNumber', value => {
                                                if (value !== this.selected) {
                                                    this.selected = value;
                                                    this.search = value;
                                                }
                                            });
                                        }
                                    }" @click.outside="open = false">
                                        <div class="relative w-full">
                                            <input type="hidden" name="part_number" x-model="selected">
                                            <input id="part_number" class="input-field pr-10 w-full" type="text" 
                                                   x-model="search" 
                                                   @focus="!isLocked && (open = true, $el.select())"
                                                   @input="!isLocked && (open = true, selected = search, partNumber = search.toUpperCase(), search = search.toUpperCase())"
                                                   @change="checkPN"
                                                   @keydown.enter.prevent="createNew()" 
                                                   placeholder="Contoh: KBD-LOGI-GPRO-X" 
                                                   autocomplete="off" />
                                            
                                            <!-- Loading Spinner -->
                                            <div x-show="isLoading" class="absolute right-3 top-3">
                                                <svg class="animate-spin h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                </svg>
                                            </div>

                                            <!-- Dropdown -->
                                            <div x-show="open" 
                                                 x-transition:leave="transition ease-in duration-100"
                                                 x-transition:leave-start="opacity-100"
                                                 x-transition:leave-end="opacity-0"
                                                 class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                                
                                                <template x-for="option in filteredOptions" :key="option">
                                                    <div @click="select(option)" 
                                                         class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-primary-50 text-secondary-900">
                                                        <span x-text="option" class="block truncate" :class="{ 'font-semibold': selected === option, 'font-normal': selected !== option }"></span>
                                                        <span x-show="selected === option" class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary-600">
                                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                            </svg>
                                                        </span>
                                                    </div>
                                                </template>

                                                <!-- Create New Option -->
                                                <div x-show="search.length > 0 && !options.some(o => o === search)" 
                                                     @click="createNew()"
                                                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-primary-600 hover:bg-primary-50 border-t border-secondary-100">
                                                    <span class="block truncate">
                                                        Gunakan "<span x-text="search" class="font-bold"></span>"
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" @click="openScanModal()" class="btn btn-secondary px-3" title="Scan Part Number">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75zM16.5 19.5h.75v.75h-.75v-.75z" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Scan Modal -->
                                    <!-- Scan Modal -->
                                    <div x-show="scanModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" style="display: none;" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                                        <!-- Backdrop -->
                                        <div x-show="scanModalOpen" 
                                             x-transition:enter="ease-out duration-300"
                                             x-transition:enter-start="opacity-0"
                                             x-transition:enter-end="opacity-100"
                                             x-transition:leave="ease-in duration-200"
                                             x-transition:leave-start="opacity-100"
                                             x-transition:leave-end="opacity-0"
                                             class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" 
                                             @click="closeScanModal()"></div>

                                        <div class="flex min-h-full items-center justify-center p-4 text-center sm:p-0">
                                            <!-- Modal Panel -->
                                            <div x-show="scanModalOpen" 
                                                 x-transition:enter="ease-out duration-300"
                                                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave="ease-in duration-200"
                                                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                                                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                                                 class="relative transform overflow-hidden rounded-lg bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg w-full max-w-sm mx-auto">
                                                
                                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                                    <div class="sm:flex sm:items-start w-full">
                                                        <div class="mt-3 text-center sm:mt-0 sm:text-left w-full">
                                                            <div class="flex justify-between items-center mb-2">
                                                                <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                                    Scan Part Number
                                                                </h3>
                                                                <div class="flex items-center gap-2">
                                                                    <!-- Debug hidden -->
                                                                    <label class="hidden inline-flex items-center cursor-pointer">
                                                                        <input type="checkbox" x-model="debugMode" class="sr-only peer">
                                                                        <div class="relative w-9 h-5 bg-gray-200 peer-focus:outline-none peer-focus:ring-2 peer-focus:ring-primary-300 rounded-full peer peer-checked:after:translate-x-full rtl:peer-checked:after:-translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:start-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:bg-primary-600"></div>
                                                                        <span class="ms-2 text-xs font-medium text-gray-900">Debug</span>
                                                                    </label>
                                                                    <button @click="closeScanModal()" class="text-gray-400 hover:text-gray-500">
                                                                        <span class="sr-only">Close</span>
                                                                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                                                        </svg>
                                                                    </button>
                                                                </div>
                                                            </div>
                                                            
                                                            <!-- Camera Container -->
                                                            <div class="mb-2 px-2 py-1 bg-amber-50 text-amber-700 text-xs rounded border border-amber-200">
                                                                <span class="font-bold">Info:</span> Pastikan foto label terlihat jelas dan terang.
                                                            </div>
                                                            <div class="grid gap-4" :class="debugMode ? 'grid-cols-2' : 'grid-cols-1'">
                                                                <!-- Live Camera -->
                                                                <div class="relative w-full bg-black rounded-lg overflow-hidden aspect-video">
                                                                    <video x-ref="video" autoplay playsinline class="w-full h-full object-cover"></video>
                                                                    
                                                                    <div x-show="ocrLoading" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 z-20">
                                                                        <div class="text-center">
                                                                            <svg class="animate-spin h-10 w-10 text-white mx-auto mb-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                                                            </svg>
                                                                            <span class="text-white font-semibold text-sm">Memproses...</span>
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                <!-- Debug Preview -->
                                                                <div x-show="debugMode" class="relative w-full bg-gray-100 rounded-lg overflow-hidden aspect-video border border-gray-300">
                                                                    <div class="absolute top-0 left-0 bg-black/50 text-white text-[10px] px-1">Processed</div>
                                                                    <img :src="debugImage" class="w-full h-full object-contain" x-show="debugImage">
                                                                    <div x-show="!debugImage" class="flex items-center justify-center h-full text-xs text-gray-400">Preview Processed</div>
                                                                </div>
                                                            </div>
                                                            <div class="mb-4"></div> <!-- Spacer -->

                                                            <!-- Camera Control Buttons -->
                                                            <div class="flex justify-between items-center mb-3 px-1" x-show="videoDevices.length > 1">
                                                                <span class="text-xs text-gray-500" x-text="'Kamera: ' + (currentDeviceLabel || 'Default')"></span>
                                                                <button type="button" @click="switchCamera()" class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs rounded-full transition-colors font-medium border border-gray-300">
                                                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                                                                        <path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0l3.181 3.183a8.25 8.25 0 0013.803-3.7M4.031 9.865a8.25 8.25 0 0113.803-3.7l3.181 3.182m0-4.991v4.99" />
                                                                    </svg>
                                                                    Putar Kamera
                                                                </button>
                                                            </div>

                                                            <div x-show="ocrError" class="mb-4 bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded relative text-sm">
                                                                <span class="block sm:inline" x-text="ocrError"></span>
                                                            </div>

                                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                                                <button type="button" @click="captureAndScan" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm order-1 sm:order-none">
                                                                    Ambil Gambar
                                                                </button>
                                                                
                                                                <label class="w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:text-sm cursor-pointer order-2 sm:order-none">
                                                                    <span>Upload Foto</span>
                                                                    <input type="file" class="hidden" accept="image/*" @change="handleFileUpload">
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                                </div>
                                            </div>
                                        </div>
                                    <x-input-error :messages="$errors->get('part_number')" class="mt-2" />
                                </div>

                                <!-- Nama Barang (Creatable Select) -->
                                <div class="relative" x-data="{
                                    open: false,
                                    search: '',
                                    selected: '{{ old('name') }}',
                                    options: {{ json_encode($names) }} || [],
                                    get filteredOptions() {
                                        if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                        return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(value) {
                                        this.selected = value;
                                        this.search = value;
                                        this.$dispatch('update-name', value);
                                        this.open = false;
                                    },
                                    createNew() {
                                        let newValue = this.search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                        this.select(newValue);
                                    },
                                    init() {
                                        if (this.selected) {
                                            this.search = this.selected;
                                            this.$dispatch('update-name', this.selected);
                                        }
                                        this.$watch('itemName', value => {
                                            if (value !== this.selected) {
                                                this.selected = value;
                                                this.search = value;
                                            }
                                        });
                                        if (this.itemName) {
                                            this.selected = this.itemName;
                                            this.search = this.itemName;
                                        }
                                    }
                                }" @click.outside="open = false">
                                    <label for="name" class="input-label">Nama Barang <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="name" x-model="selected">
                                        <input type="text" 
                                               id="name"
                                               class="input-field w-full pr-10 cursor-text" 
                                               :class="{'bg-secondary-100 text-secondary-500': isLocked}"
                                               x-model="search"
                                               :readonly="isLocked" 
                                               @focus="!isLocked && (open = true, $el.select())" 
                                               @input="!isLocked && (open = true, selected = search, itemName = search)" 
                                               @keydown.enter.prevent="createNew()"
                                               placeholder="Contoh: Laptop Dell XPS 15 / Keyboard Logitech" 
                                               autocomplete="off">
                                        
                                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2 text-secondary-400" @click="!isLocked && (open = !open)" :disabled="isLocked">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Dropdown -->
                                    <div x-show="open" 
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                        
                                        <template x-for="option in filteredOptions" :key="option">
                                            <div @click="select(option)" 
                                                 class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-primary-50 text-secondary-900">
                                                <span x-text="option" class="block truncate" :class="{ 'font-semibold': selected === option, 'font-normal': selected !== option }"></span>
                                                <span x-show="selected === option" class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary-600">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </template>

                                        <!-- Create New Option -->
                                        <div x-show="search.length > 0 && !options.some(o => o.toLowerCase() === search.toLowerCase())" 
                                             @click="createNew()"
                                             class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-primary-600 hover:bg-primary-50 border-t border-secondary-100">
                                            <span class="block truncate">
                                                Tambah "<span x-text="search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase())" class="font-bold"></span>"
                                            </span>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Row 2: Merk & Kategori -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 col-span-full">
                                <!-- Merk (Creatable Select) -->
                                <div class="relative" x-data="{
                                    open: false,
                                    search: '',
                                    selected: '{{ old('brand') }}',
                                    options: {{ json_encode($brands) }},
                                    get filteredOptions() {
                                        if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                        return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(value) {
                                        this.selected = value;
                                        this.search = value;
                                        this.$dispatch('update-brand', value);
                                        this.open = false;
                                    },
                                    createNew() {
                                        // Auto-Capitalize Title Case
                                        let newValue = this.search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                        this.select(newValue);
                                    },
                                    init() {
                                        if (this.selected) {
                                            this.search = this.selected;
                                            this.$dispatch('update-brand', this.selected);
                                        }
                                        // Sync initial value from parent (old input or empty)
                                        this.$watch('itemBrand', value => {
                                            if (value !== this.selected) {
                                                this.selected = value;
                                                this.search = value;
                                            }
                                        });
                                        if (this.itemBrand) {
                                            this.selected = this.itemBrand;
                                            this.search = this.itemBrand;
                                        }
                                    }
                                }" @click.outside="open = false">
                                    <label for="brand" class="input-label">Merk <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="brand" x-model="selected">
                                        <input type="text" 
                                               id="brand"
                                               class="input-field w-full pr-10 cursor-text" 
                                               :class="{'bg-secondary-100 text-secondary-500': isLocked}"
                                               x-model="search" 
                                               :readonly="isLocked"
                                               @focus="!isLocked && (open = true, $el.select())" 
                                               @input="open = true, selected = search, itemBrand = search" 
                                               @keydown.enter.prevent="createNew()"
                                               placeholder="Contoh: Dell, Logitech, Toyota" 
                                               autocomplete="off">
                                        
                                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2 text-secondary-400" @click="!isLocked && (open = !open)" :disabled="isLocked">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Dropdown -->
                                    <div x-show="open" 
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                        
                                        <template x-for="option in filteredOptions" :key="option">
                                            <div @click="select(option)" 
                                                 class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-primary-50 text-secondary-900">
                                                <span x-text="option" class="block truncate" :class="{ 'font-semibold': selected === option, 'font-normal': selected !== option }"></span>
                                                <span x-show="selected === option" class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary-600">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </template>

                                        <!-- Create New Option -->
                                        <div x-show="search.length > 0 && !options.some(o => o.toLowerCase() === search.toLowerCase())" 
                                             @click="createNew()"
                                             class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-primary-600 hover:bg-primary-50 border-t border-secondary-100">
                                            <span class="block truncate">
                                                Tambah "<span x-text="search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase())" class="font-bold"></span>"
                                            </span>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                </div>

                                <!-- Kategori (Creatable Select) -->
                                <div class="relative" x-data="{
                                    open: false,
                                    search: '',
                                    selected: '{{ old('category') }}',
                                    options: {{ json_encode($categories) }},
                                    get filteredOptions() {
                                        if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                        return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(value) {
                                        this.selected = value;
                                        this.search = value;
                                        this.$dispatch('update-category', value);
                                        this.open = false;
                                    },
                                    createNew() {
                                        let newValue = this.search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                        this.select(newValue);
                                    },
                                    init() {
                                        if (this.selected) {
                                            this.search = this.selected;
                                            this.$dispatch('update-category', this.selected);
                                        }
                                        this.$watch('itemCategory', value => {
                                            if (value !== this.selected) {
                                                this.selected = value;
                                                this.search = value;
                                            }
                                        });
                                        if (this.itemCategory) {
                                            this.selected = this.itemCategory;
                                            this.search = this.itemCategory;
                                        }
                                    }
                                }" @click.outside="open = false">
                                    <label for="category" class="input-label">Kategori <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="category" x-model="selected">
                                        <input type="text" 
                                               id="category"
                                               class="input-field w-full pr-10 cursor-text" 
                                               :class="{'bg-secondary-100 text-secondary-500': isLocked}"
                                               x-model="search" 
                                               :readonly="isLocked"
                                               @focus="!isLocked && (open = true, $el.select())" 
                                               @input="open = true, selected = search, itemCategory = search" 
                                               @keydown.enter.prevent="createNew()"
                                               placeholder="Pilih atau ketik kategori baru" 
                                               autocomplete="off">
                                        
                                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2 text-secondary-400" @click="!isLocked && (open = !open)" :disabled="isLocked">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Dropdown -->
                                    <div x-show="open" 
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                        
                                        <template x-for="option in filteredOptions" :key="option">
                                            <div @click="select(option)" 
                                                 class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-primary-50 text-secondary-900">
                                                <span x-text="option" class="block truncate" :class="{ 'font-semibold': selected === option, 'font-normal': selected !== option }"></span>
                                                <span x-show="selected === option" class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary-600">
                                                    <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                    </svg>
                                                </span>
                                            </div>
                                        </template>

                                        <!-- Create New Option -->
                                        <div x-show="search.length > 0 && !options.some(o => o.toLowerCase() === search.toLowerCase())" 
                                             @click="createNew()"
                                             class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-primary-600 hover:bg-primary-50 border-t border-secondary-100">
                                            <span class="block truncate">
                                                Tambah "<span x-text="search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase())" class="font-bold"></span>"
                                            </span>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Row 3: Warna & Kondisi -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 col-span-full">
                                <!-- Warna (Creatable Select) -->
                                <div class="relative" x-data="{
                                    open: false,
                                    search: '',
                                    selected: '',
                                    options: {{ json_encode($colors) }},
                                    get filteredOptions() {
                                        if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                        return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(value) {
                                        this.selected = value;
                                        this.search = value;
                                        this.itemColor = value;
                                        this.open = false;
                                    },
                                    createNew() {
                                        let newValue = this.search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                        this.select(newValue);
                                    },
                                    init() {
                                        // Auto fill if old value exists
                                        if (this.itemColor || '{{ old('color') }}') {
                                            this.selected = this.itemColor || '{{ old('color') }}';
                                            this.search = this.selected;
                                            this.itemColor = this.selected;
                                        }
                                    }
                                }" @click.outside="open = false">
                                    <label for="color" class="input-label">Warna</label>
                                    <div class="relative">
                                        <input type="hidden" name="color" x-model="selected">
                                        <input type="text" 
                                               id="color"
                                               class="input-field w-full pr-10 cursor-text" 
                                               x-model="search" 
                                               @focus="open = true; $el.select()" 
                                               @input="open = true; selected = search; itemColor = search" 
                                               @keydown.enter.prevent="createNew()"
                                               placeholder="Contoh: Hitam, Putih, Merah" 
                                               autocomplete="off">
                                        
                                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2 text-secondary-400" @click="open = !open">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <!-- Dropdown -->
                                    <div x-show="open" 
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                        
                                        <template x-for="option in filteredOptions" :key="option">
                                            <div @click="select(option)" 
                                                 class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-primary-50 text-secondary-900">
                                                <span x-text="option" class="block truncate" :class="{ 'font-semibold': selected === option, 'font-normal': selected !== option }"></span>
                                            </div>
                                        </template>

                                        <!-- Create New Option -->
                                        <div x-show="search.length > 0 && !options.some(o => o.toLowerCase() === search.toLowerCase())" 
                                             @click="createNew()"
                                             class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-primary-600 hover:bg-primary-50 border-t border-secondary-100">
                                            <span class="block truncate">
                                                Tambah "<span x-text="search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase())" class="font-bold"></span>"
                                            </span>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                </div>

                                <!-- Kondisi -->
                                <div>
                                    <label for="condition" class="input-label">Kondisi Barang <span class="text-danger-500">*</span></label>
                                    @php
                                        $conditionOptions = [
                                            'Baru' => 'Baru',
                                            'Bekas' => 'Bekas',
                                            'Rusak' => 'Rusak',
                                        ];
                                    @endphp
                                    <x-select name="condition" :options="$conditionOptions" :selected="old('condition')" placeholder="Pilih Kondisi" width="w-full" />
                                    <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Gambar (Optional) -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="image" class="input-label">Gambar Barang</label>
                                
                                <!-- Hidden input for existing image path -->
                                <input type="hidden" name="existing_image" x-model="existingImage">

                                <div x-data="{ isDragging: false, fileName: null }" 
                                     class="mt-1 flex flex-col items-center justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md transition-colors duration-200"
                                     :class="{ 'border-primary-400 bg-primary-50': isDragging, 'border-gray-300 hover:border-primary-400': !isDragging }"
                                     x-on:dragover.prevent="isDragging = true"
                                     x-on:dragleave.prevent="isDragging = false"
                                     x-on:drop.prevent="isDragging = false; fileName = $event.dataTransfer.files[0].name; $refs.fileInput.files = $event.dataTransfer.files; 
                                                      // Create local preview from dropped file
                                                      const file = $event.dataTransfer.files[0];
                                                      const reader = new FileReader();
                                                      reader.onload = (e) => { imagePreview = e.target.result; };
                                                      reader.readAsDataURL(file);
                                     ">
                                    
                                    <!-- Preview Area -->
                                    <template x-if="imagePreview">
                                        <div class="mb-4 relative group">
                                            <img :src="imagePreview" class="h-40 w-auto object-contain rounded-md shadow-sm border border-secondary-200">
                                            <button type="button" @click="imagePreview = null; fileName = null; existingImage = ''; $refs.fileInput.value = ''" 
                                                class="absolute -top-2 -right-2 bg-danger-500 text-white rounded-full p-1 shadow-md hover:bg-danger-600 focus:outline-none transition-colors"
                                                title="Hapus gambar">
                                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                            </button>
                                        </div>
                                    </template>

                                    <!-- Upload Placeholder (Hidden if there's a preview) -->
                                    <div x-show="!imagePreview" class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center items-center gap-1">
                                            <label for="image" class="relative cursor-pointer bg-transparent rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                                <span>Pilih file</span>
                                                <input id="image" name="image" type="file" accept="image/*" class="sr-only" x-ref="fileInput" 
                                                       x-on:change="fileName = $event.target.files[0].name;
                                                                    // Create local preview
                                                                    const file = $event.target.files[0];
                                                                    const reader = new FileReader();
                                                                    reader.onload = (e) => { imagePreview = e.target.result; };
                                                                    reader.readAsDataURL(file);
                                                       ">
                                            </label>
                                            <p>atau seret dan lepas di sini</p>
                                        </div>
                                        <p class="text-xs text-secondary-500">
                                            PNG, JPG, GIF hingga 2MB
                                        </p>
                                    </div>
                                    
                                    <!-- File Name Display (only if no preview logic used, but here we use preview so maybe redundant but kept for fallback) -->
                                    <p x-show="fileName && !imagePreview" x-text="fileName" class="text-sm text-primary-600 font-semibold mt-2 break-all"></p>
                                </div>
                                <x-input-error :messages="$errors->get('image')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 2: Detail Lokasi & Stok -->
                    <div class="card p-6 overflow-visible">
                        <div class="mb-4 border-b border-secondary-100 pb-2">
                            <h3 class="text-lg font-semibold text-secondary-900">Lokasi & Stok</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Lokasi Gudang (Creatable Select) -->
                            <div class="relative" x-data="{
                                open: false,
                                search: '',
                                selected: '{{ old('location') }}',
                                options: ['Tegal', 'Cibubur'],
                                get filteredOptions() {
                                    if (this.search === '' || this.search === this.selected) return this.options;
                                    return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                },
                                select(value) {
                                    this.selected = value;
                                    this.search = value; // Show selected value in input
                                    this.open = false;
                                },
                                init() {
                                    if(this.selected) this.search = this.selected;
                                }
                            }" @click.outside="open = false">
                                <label for="location" class="input-label">Lokasi Penyimpanan <span class="text-danger-500">*</span></label>
                                <div class="relative">
                                    <input type="hidden" name="location" x-model="selected">
                                    <input type="text" 
                                           class="input-field w-full pr-10 cursor-text" 
                                           x-model="search" 
                                           @focus="open = true; $el.select()" 
                                           @input="open = true; selected = search" 
                                           placeholder="Pilih Lokasi Penyimpanan" 
                                           autocomplete="off">
                                    
                                    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2 text-secondary-400" @click="open = !open">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Dropdown -->
                                <div x-show="open" 
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                    class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                    
                                    <template x-for="option in filteredOptions" :key="option">
                                        <div @click="select(option)" 
                                             class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-primary-50 text-secondary-900">
                                            <span x-text="option" class="block truncate" :class="{ 'font-semibold': selected === option, 'font-normal': selected !== option }"></span>
                                            
                                            <span x-show="selected === option" class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary-600">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                                                </svg>
                                            </span>
                                        </div>
                                    </template>

                                    <!-- Create New Option (Superadmin Only) -->
                                    @if(auth()->user()->role === 'superadmin')
                                        <div x-show="search.length > 0 && !filteredOptions.includes(search)" 
                                             @click="select(search); open = false"
                                             class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-primary-600 hover:bg-primary-50 border-t border-secondary-100">
                                            <span class="block truncate">
                                                Tambah "<span x-text="search" class="font-bold"></span>"
                                            </span>
                                        </div>
                                    @else
                                         <div x-show="filteredOptions.length === 0" class="cursor-default select-none relative py-2 pl-3 pr-9 text-secondary-500 italic">
                                            Lokasi tidak ditemukan.
                                        </div>
                                    @endif
                                </div>
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>

                            <!-- Stok Awal -->
                            <div>
                                <label for="stock" class="input-label">Stok Awal <span class="text-danger-500">*</span></label>
                                <input id="stock" class="input-field" type="number" name="stock" value="{{ old('stock', 0) }}" min="0" />
                                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                            </div>
                            
                            <!-- Minimum Stok -->
                            <div>
                                <label for="minimum_stock" class="input-label">Minimum Stok (Alert)</label>
                                <input id="minimum_stock" class="input-field" type="number" name="minimum_stock" value="{{ old('minimum_stock', 5) }}" min="0" />
                                <p class="text-xs text-secondary-400 mt-1">Sistem akan memberi peringatan jika stok di bawah ini.</p>
                                <x-input-error :messages="$errors->get('minimum_stock')" class="mt-2" />
                            </div>
                            
                            <!-- Satuan (Creatable Select) -->
                            <div class="relative" x-data="{
                                open: false,
                                search: '',
                                selected: 'Pcs',
                                options: {{ json_encode($units) }},
                                get filteredOptions() {
                                    if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                    return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                },
                                select(value) {
                                    this.selected = value;
                                    this.search = value;
                                    this.itemUnit = value;
                                    this.open = false;
                                },
                                createNew() {
                                    let newValue = this.search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                    this.select(newValue);
                                },
                                init() {
                                    this.$watch('itemUnit', value => {
                                        if (value !== this.selected) {
                                            this.selected = value;
                                            this.search = value;
                                        }
                                    });
                                    if (this.itemUnit) {
                                        this.selected = this.itemUnit;
                                        this.search = this.itemUnit;
                                    }
                                }
                            }" @click.outside="open = false">
                                <label for="unit" class="input-label">Satuan</label>
                                <div class="relative">
                                    <input type="hidden" name="unit" x-model="selected">
                                    <input type="text" 
                                           id="unit"
                                           class="input-field w-full pr-10 cursor-text" 
                                           x-model="search" 
                                           @focus="open = true; $el.select()" 
                                           @input="open = true; selected = search; itemUnit = search" 
                                           @keydown.enter.prevent="createNew()"
                                           placeholder="Pcs, Set, Unit" 
                                           autocomplete="off">
                                    
                                    <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2 text-secondary-400" @click="open = !open">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                            <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                        </svg>
                                    </button>
                                </div>

                                <!-- Dropdown -->
                                <div x-show="open" 
                                     x-transition:leave="transition ease-in duration-100"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                    
                                    <template x-for="option in filteredOptions" :key="option">
                                        <div @click="select(option)" 
                                             class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-primary-50 text-secondary-900">
                                            <span x-text="option" class="block truncate" :class="{ 'font-semibold': selected === option, 'font-normal': selected !== option }"></span>
                                        </div>
                                    </template>

                                    <!-- Create New Option -->
                                    <div x-show="search.length > 0 && !options.some(o => o.toLowerCase() === search.toLowerCase())" 
                                         @click="createNew()"
                                         class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-primary-600 hover:bg-primary-50 border-t border-secondary-100">
                                        <span class="block truncate">
                                            Tambah "<span x-text="search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase())" class="font-bold"></span>"
                                        </span>
                                    </div>
                                </div>
                                <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Harga & Status -->
                    <div class="card p-6 overflow-visible">
                        <div class="mb-4 border-b border-secondary-100 pb-2">
                            <h3 class="text-lg font-semibold text-secondary-900">Harga & Status</h3>
                        </div>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <!-- Harga -->
                            <div x-show="type === 'sale'">
                                <label for="price" class="input-label">Harga Satuan (Rp) <span class="text-danger-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-secondary-500">Rp</span>
                                    </div>
                                    <input id="price" class="input-field pl-10" type="number" name="price" x-model="itemPrice" placeholder="0" />
                                </div>
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="input-label">Status <span class="text-danger-500">*</span></label>
                                @php
                                    $statusOptions = [
                                        'aktif' => 'Aktif',
                                        'nonaktif' => 'Nonaktif',
                                    ];
                                @endphp
                                <x-select name="status" :options="$statusOptions" :selected="old('status', 'aktif')" placeholder="Pilih Status" width="w-full" />
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                         </div>
                    </div>
                </div>

                <div class="flex items-center justify-end gap-3 mt-8">
                    <a href="{{ route('superadmin.inventory.index') }}" class="btn btn-secondary">
                        {{ __('Batal') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('Simpan Sparepart') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
    <script src='https://cdn.jsdelivr.net/npm/tesseract.js@5/dist/tesseract.min.js'></script>
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('inventoryForm', () => ({
                type: 'sale',
                partNumber: '',
                isLocked: false,
                itemName: '',
                itemBrand: '',
                itemCategory: '',
                itemColor: '', // Added for Color dropdown sync
                itemUnit: 'Pcs',
                itemPrice: '',
                imagePreview: null,
                existingImage: '', // Store path for backend
                isLoading: false,

                async checkPN() {
                    if (!this.partNumber) return;
                    
                    this.isLoading = true;
                    try {
                        const response = await axios.get('{{ route("superadmin.inventory.check-part-number") }}', {
                            params: { part_number: this.partNumber }
                        });

                        if (response.data.exists) {
                            const data = response.data.data;
                            this.itemName = data.name;
                            this.itemBrand = data.brand;
                            this.itemCategory = data.category;
                            this.type = data.type;
                            this.itemUnit = data.unit;
                            this.itemPrice = data.price; // Auto-fill price
                            
                            // Handle Image
                            if (data.image_url) {
                                this.imagePreview = data.image_url;
                                this.existingImage = data.image_path;
                            }

                            this.isLocked = true;
                            console.log('Produk ditemukan, data diisi otomatis.');
                        } else {
                            // Only reset isLocked status, do not clear form if it was manually filled or if OCR was just used
                            // But if we want to be strict that "New PN = Fresh Form", we might want to clear.
                            // For now, let's keep it user-friendly: only unlock fields so they can type.
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
                ocrError: null,
                stream: null,
                debugMode: false,
                debugImage: null,

                ocrError: null,
                stream: null,
                debugMode: false,
                debugImage: null,
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
                    this.ocrError = null;
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
                        const constraints = { 
                            video: {} 
                        };

                        // If we have devices listed, pick by ID. Otherwise default to environment.
                        if (this.videoDevices.length > 0) {
                            const deviceId = this.videoDevices[this.currentDeviceIndex].deviceId;
                            constraints.video.deviceId = { exact: deviceId };
                            this.currentDeviceLabel = this.videoDevices[this.currentDeviceIndex].label;
                        } else {
                            constraints.video.facingMode = 'environment';
                        }

                        this.stream = await navigator.mediaDevices.getUserMedia(constraints);
                        this.$refs.video.srcObject = this.stream;
                        
                        // If we didn't have permission before, labels might be empty. Updating now might get labels.
                        if (!this.currentDeviceLabel && this.videoDevices.length > 0) {
                            this.getVideoDevices().then(() => {
                                if (this.videoDevices[this.currentDeviceIndex]) {
                                    this.currentDeviceLabel = this.videoDevices[this.currentDeviceIndex].label;
                                }
                            });
                        }

                    } catch (err) {
                        console.error("Error detecting camera:", err);
                        this.ocrError = "Tidak dapat mengakses kamera. Pastikan izin diberikan.";
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
                    this.ocrError = null;

                    const video = this.$refs.video;
                    const canvas = document.createElement('canvas');
                    canvas.width = video.videoWidth;
                    canvas.height = video.videoHeight;
                    canvas.getContext('2d').drawImage(video, 0, 0);

                    const image = canvas.toDataURL('image/png');
                    
                    // Preprocess full image (for better general reading)
                    const processedImage = await this.preprocessImage(image);
                    this.debugImage = processedImage; // Show in debug view
                    
                    // Process full text
                    await this.processFullAnalysis(processedImage);
                },

                // Image Preprocessing (Upscale + Sharpen + Contrast)
                async preprocessImage(imageSource) {
                    return new Promise((resolve) => {
                        const img = new Image();
                        img.onload = () => {
                            const canvas = document.createElement('canvas');
                            const ctx = canvas.getContext('2d');
                            
                            // 1. Upscale by 2x for better character recognition
                            const scaleFactor = 2;
                            canvas.width = img.width * scaleFactor;
                            canvas.height = img.height * scaleFactor;
                            
                            // Smoothing disabled for pixel-perfect scaling (optional, but usually better for text to be crisp)
                            ctx.imageSmoothingEnabled = false; 
                            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);

                            const imageData = ctx.getImageData(0, 0, canvas.width, canvas.height);
                            const data = imageData.data;
                            const width = canvas.width;
                            const height = canvas.height;

                            // Buffer for sharpening (so we don't read modified pixels)
                            const outputData = new Uint8ClampedArray(data);

                            // Helper: Get pixel index
                            const getIdx = (x, y) => (y * width + x) * 4;

                            // 2. Grayscale & Stats (Min/Max)
                            let min = 255;
                            let max = 0;

                            // Calculate min/max and convert to gray
                            for (let i = 0; i < data.length; i += 4) {
                                const gray = 0.21 * data[i] + 0.72 * data[i + 1] + 0.07 * data[i + 2];
                                data[i] = gray;
                                data[i + 1] = gray;
                                data[i + 2] = gray;
                                
                                if (gray < min) min = gray;
                                if (gray > max) max = gray;
                            }
                            if (max === min) max = min + 1;

                            // 3. Contrast Stretching Only (No Sharpening)
                            // Sharpening was causing noise artifacts (reading barcodes as text)
                            for (let i = 0; i < data.length; i += 4) {
                                let gray = data[i];
                                gray = ((gray - min) * 255) / (max - min);
                                data[i] = data[i + 1] = data[i + 2] = gray;
                            }

                            ctx.putImageData(imageData, 0, 0);

                            resolve(canvas.toDataURL('image/png'));
                        };
                        img.src = imageSource;
                    });
                },

                handleFileUpload(e) {
                    const file = e.target.files[0];
                    if (!file) return;

                    this.ocrLoading = true;
                    this.ocrError = null;

                    const reader = new FileReader();
                    reader.onload = async (event) => {
                        const processedImage = await this.preprocessImage(event.target.result);
                        this.debugImage = processedImage; // Show in debug view
                        await this.processFullAnalysis(processedImage);
                    };
                    reader.readAsDataURL(file);
                },

                async processFullAnalysis(imageSource) {
                    let worker = null;
                    try {
                        this.ocrLoading = true;
                        
                        worker = await Tesseract.createWorker('eng', 1, {
                            logger: m => {
                                // console.log(m); // Optional logging
                            }
                        });

                        // Set parameters for general block text reading
                        await worker.setParameters({
                            tessedit_pageseg_mode: '11', // PSM 11 = Sparse Text. Finds as much text as possible in no particular order.
                        });

                        const { data: { text } } = await worker.recognize(imageSource);
                        const rawText = text.toUpperCase(); // Normalize to uppercase
                        console.log("Raw Analysis:", rawText);

                        // --- Extraction Logic ---
                        
                        // 1. Brand Detection (List of common IT brands)
                        const knownBrands = ['LENOVO', 'DELL', 'HP', 'ASUS', 'ACER', 'APPLE', 'SAMSUNG', 'TOSHIBA', 'SONY', 'MSI', 'LOGITECH', 'CANON', 'EPSON'];
                        let foundBrand = '';
                        
                        for (const brand of knownBrands) {
                            if (rawText.includes(brand)) {
                                foundBrand = brand;
                                console.log("Found Brand:", foundBrand);
                                // Nice-to-have: Title case (Lenovo instead of LENOVO)
                                foundBrand = brand.charAt(0) + brand.slice(1).toLowerCase(); 
                                break; // Stop after first match (usually enough)
                            }
                        }

                        // 2. Part Number Detection
                        let foundPN = '';
                        
                        // Heuristic A: Explicit Label "PN", "P/N", "Part No", "Orig.PN", etc.
                        // We allow optional characters like 'Orig' or 'Ship' before 'PN'
                        const pnRegex = /(?:ORIG\.?|SHIP|MACHINE)?[\s\.]*(?:P\/N|PN|PART NO|PART NUMBER)[\s.:]*([A-Z0-9\-\/]{3,})/i;
                        const pnMatch = rawText.match(pnRegex);

                        if (pnMatch && pnMatch[1]) {
                            foundPN = pnMatch[1];
                            console.log("Found Explicit PN:", foundPN);
                        } else {
                            // Heuristic B: Fallback - Look for the longest alphanumeric string that "looks like" a PN
                            // We prioritize strings that mix letters and numbers, as pure numbers might be dates/quantities.
                            const tokens = rawText.split(/[\s\n]+/);
                            const potentialPNs = tokens.filter(t => {
                                // Filter out common words/noise
                                if (knownBrands.includes(t)) return false;
                                if (['MODEL', 'REV', 'DATE', 'QTY', 'MADE', 'CHINA', 'WIN', 'MB', 'ORIG', 'SHIP'].includes(t)) return false;
                                
                                // Must be at least 5 chars
                                if (t.length < 5) return false;

                                // Must contain at least one digit AND (one letter OR one dash/slash)
                                // This assumes PNs are usually mixed. 
                                // User said: "kombinasi hurufnya, bukan hanya angka"
                                const hasDigit = /[0-9]/.test(t);
                                const hasLetter = /[A-Z]/.test(t);
                                return hasDigit && hasLetter;
                            });

                            if (potentialPNs.length > 0) {
                                // Pick specific ones that match the user's sample format (e.g. 5B21K...)
                                // For now, taking the longest candidate is often a good heuristic for random labels.
                                foundPN = potentialPNs.reduce((a, b) => a.length > b.length ? a : b);
                                console.log("Found Heuristic PN:", foundPN);
                            }
                        }

                        // --- Result Handling ---

                        if (!foundPN && !foundBrand) {
                            throw new Error("Tidak menemukan informasi yang relevan (PN/Merk).");
                        }

                        // Fill Form
                        if (foundPN) {
                            // Clean PN (remove random leading/trailing non-alphanumeric)
                            this.partNumber = foundPN.replace(/^[^A-Z0-9]+|[^A-Z0-9]+$/g, '');
                        }
                        if (foundBrand) this.itemBrand = foundBrand;

                        // Show Success State (Toast or just close)
                        if (!this.debugMode) {
                            this.closeScanModal();
                        } else {
                            this.ocrLoading = false;
                            // Update Debug info if possible or just log
                            this.ocrError = `Ditemukan: ${foundBrand || '-'} / ${foundPN || '-'}`;
                        }
                        
                        if (this.partNumber) this.checkPN();

                    } catch (err) {
                        console.error("Analysis Error:", err);
                        this.ocrError = "Gagal menganalisis: " + (err.message || "Kesalahan sistem.");
                    } finally {
                        if (worker) await worker.terminate();
                        if (!this.ocrError) this.ocrLoading = false;
                    }
                }
            }))
        })
    </script>
    @endpush
</x-app-layout>
