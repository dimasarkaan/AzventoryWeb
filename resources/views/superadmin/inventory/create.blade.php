<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <h2 class="text-2xl font-bold text-secondary-900 tracking-tight">
                    {{ __('Tambah Sparepart Baru') }}
                </h2>
                <p class="mt-1 text-sm text-secondary-500">Isi detail sparepart di bawah ini untuk menambahkan ke inventaris.</p>
            </div>

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 bg-danger-50 border-l-4 border-danger-500 p-4 rounded-md shadow-sm relative">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-danger-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium text-danger-800">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-danger-500 hover:text-danger-700 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            <form action="{{ route('superadmin.inventory.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-4" x-data="inventoryForm()">
                    <!-- Section 1: Informasi Dasar -->
                    <div class="card p-6">
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
                                    <div class="relative">
                                        <input id="part_number" class="input-field" type="text" name="part_number" x-model="partNumber" @change="checkPN" @keydown.enter.prevent="checkPN" placeholder="Contoh: KBD-LOGI-GPRO-X" />
                                        <div x-show="isLoading" class="absolute right-3 top-3">
                                            <svg class="animate-spin h-5 w-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                            </svg>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('part_number')" class="mt-2" />
                                </div>

                                <!-- Nama Barang -->
                                <div>
                                    <label for="name" class="input-label">Nama Barang <span class="text-danger-500">*</span></label>
                                    <input id="name" class="input-field" :class="{'bg-secondary-100 text-secondary-500': isLocked}" type="text" name="name" x-model="itemName" :readonly="isLocked" placeholder="Contoh: Laptop Dell XPS 15 / Keyboard Logitech" />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Row 2: Merk & Kategori -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 col-span-full">
                                <!-- Merk -->
                                <div>
                                    <label for="brand" class="input-label">Merk <span class="text-danger-500">*</span></label>
                                    <input id="brand" class="input-field" :class="{'bg-secondary-100 text-secondary-500': isLocked}" type="text" name="brand" x-model="itemBrand" :readonly="isLocked" placeholder="Contoh: Dell, Logitech, Toyota" />
                                    <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                </div>

                                <!-- Kategori -->
                                <div>
                                    <label for="category" class="input-label">Kategori <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input list="categories" id="category" name="category" class="input-field" :class="{'bg-secondary-100 text-secondary-500': isLocked}" x-model="itemCategory" :readonly="isLocked" placeholder="Pilih atau ketik kategori baru" />
                                        <datalist id="categories">
                                            <option value="Elektronik">
                                            <option value="Mesin">
                                            <option value="Aksesoris">
                                            <option value="Lainnya">
                                        </datalist>
                                    </div>
                                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Row 3: Warna & Kondisi -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 col-span-full">
                                <!-- Warna -->
                                <div>
                                    <label for="color" class="input-label">Warna</label>
                                    <input list="colors" id="color" name="color" class="input-field" value="{{ old('color') }}" placeholder="Contoh: Hitam, Putih, Merah" />
                                    <datalist id="colors">
                                        <option value="Hitam">
                                        <option value="Putih">
                                        <option value="Merah">
                                        <option value="Biru">
                                        <option value="Silver">
                                    </datalist>
                                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                </div>

                                <!-- Kondisi -->
                                <div>
                                    <label for="condition" class="input-label">Kondisi Barang <span class="text-danger-500">*</span></label>
                                    <select id="condition" name="condition" class="input-field">
                                        <option value="" disabled selected>Pilih Kondisi</option>
                                        <option value="Baru" {{ old('condition') == 'Baru' ? 'selected' : '' }}>Baru</option>
                                        <option value="Bekas" {{ old('condition') == 'Bekas' ? 'selected' : '' }}>Bekas</option>
                                        <option value="Rusak" {{ old('condition') == 'Rusak' ? 'selected' : '' }}>Rusak</option>
                                    </select>
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
                    <div class="card p-6">
                        <div class="mb-4 border-b border-secondary-100 pb-2">
                            <h3 class="text-lg font-semibold text-secondary-900">Lokasi & Stok</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Lokasi Gudang -->
                            <div>
                                <label for="location" class="input-label">Lokasi Penyimpanan <span class="text-danger-500">*</span></label>
                                <input id="location" class="input-field" type="text" name="location" value="{{ old('location') }}" placeholder="Contoh: Rak server A1" />
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
                            
                             <!-- Satuan -->
                            <div>
                                <label for="unit" class="input-label">Satuan</label>
                                <input id="unit" class="input-field" :class="{'bg-secondary-100 text-secondary-500': isLocked}" type="text" name="unit" x-model="itemUnit" :readonly="isLocked" placeholder="Pcs, Set, Unit" />
                                <x-input-error :messages="$errors->get('unit')" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Section 3: Harga & Status -->
                    <div class="card p-6">
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
                                <select id="status" name="status" class="input-field">
                                    <option value="aktif" {{ old('status') == 'aktif' ? 'selected' : '' }}>Aktif</option>
                                    <option value="nonaktif" {{ old('status') == 'nonaktif' ? 'selected' : '' }}>Nonaktif</option>
                                </select>
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
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('inventoryForm', () => ({
                type: 'sale',
                partNumber: '',
                isLocked: false,
                itemName: '',
                itemBrand: '',
                itemCategory: '',
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
                            this.isLocked = false;
                            // Reset optional fields if not found? Maybe better to keep them empty/user input
                            // keeping them helps if user made a typo in PN and corrects it
                        }
                    } catch (error) {
                        console.error('Error checking PN:', error);
                    } finally {
                        this.isLoading = false;
                    }
                }
            }))
        })
    </script>
    @endpush
</x-app-layout>
