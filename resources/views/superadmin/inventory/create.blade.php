<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-4">
                <h2 class="text-2xl font-bold text-secondary-900 tracking-tight">
                    {{ __('Tambah Sparepart Baru') }}
                </h2>
                <p class="mt-1 text-sm text-secondary-500">Isi detail sparepart di bawah ini untuk menambahkan ke inventaris.</p>
            </div>

            <form action="{{ route('superadmin.inventory.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                
                <div class="space-y-4" x-data="{ type: 'sale' }">
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
                                    <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-4 hover:bg-secondary-50 transition-all duration-200" :class="{ 'border-primary-500 bg-primary-50 ring-1 ring-primary-500': type === 'sale', 'border-secondary-200': type !== 'sale' }">
                                        <div class="mt-1">
                                            <input type="radio" name="type" value="sale" x-model="type" class="text-primary-600 focus:ring-primary-500 w-5 h-5">
                                        </div>
                                        <div>
                                            <span class="block font-semibold text-secondary-900 text-base">Barang Dijual (Sale)</span>
                                            <span class="block text-sm text-secondary-500 mt-1">Barang dagangan dengan stok dan harga jual.</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-4 hover:bg-secondary-50 transition-all duration-200" :class="{ 'border-primary-500 bg-primary-50 ring-1 ring-primary-500': type === 'asset', 'border-secondary-200': type !== 'asset' }">
                                        <div class="mt-1">
                                            <input type="radio" name="type" value="asset" x-model="type" class="text-primary-600 focus:ring-primary-500 w-5 h-5">
                                        </div>
                                        <div>
                                            <span class="block font-semibold text-secondary-900 text-base">Inventaris (Asset)</span>
                                            <span class="block text-sm text-secondary-500 mt-1">Aset internal kantor, dapat dipinjamkan.</span>
                                        </div>
                                    </label>
                                </div>
                                <x-input-error :messages="$errors->get('type')" class="mt-2" />
                            </div>

                            <!-- Row 1: PN & Name -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 col-span-full">
                                <!-- Part Number -->
                                <div>
                                    <label for="part_number" class="input-label">Part Number (PN) <span class="text-danger-500">*</span></label>
                                    <input id="part_number" class="input-field" type="text" name="part_number" value="{{ old('part_number') }}" placeholder="Contoh: KBD-LOGI-GPRO-X" />
                                    <x-input-error :messages="$errors->get('part_number')" class="mt-2" />
                                </div>

                                <!-- Nama Barang -->
                                <div>
                                    <label for="name" class="input-label">Nama Barang <span class="text-danger-500">*</span></label>
                                    <input id="name" class="input-field" type="text" name="name" value="{{ old('name') }}" placeholder="Contoh: Laptop Dell XPS 15 / Keyboard Logitech" autofocus />
                                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Row 2: Merk & Kategori -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 col-span-full">
                                <!-- Merk -->
                                <div>
                                    <label for="brand" class="input-label">Merk <span class="text-danger-500">*</span></label>
                                    <input id="brand" class="input-field" type="text" name="brand" value="{{ old('brand') }}" placeholder="Contoh: Dell, Logitech, Toyota" />
                                    <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                </div>

                                <!-- Kategori -->
                                <div>
                                    <label for="category" class="input-label">Kategori <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input list="categories" id="category" name="category" class="input-field" value="{{ old('category') }}" placeholder="Pilih atau ketik kategori baru" />
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
                                <div x-data="{ isDragging: false, fileName: null }" 
                                     class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md transition-colors duration-200"
                                     :class="{ 'border-primary-400 bg-primary-50': isDragging, 'border-gray-300 hover:border-primary-400': !isDragging }"
                                     x-on:dragover.prevent="isDragging = true"
                                     x-on:dragleave.prevent="isDragging = false"
                                     x-on:drop.prevent="isDragging = false; fileName = $event.dataTransfer.files[0].name; $refs.fileInput.files = $event.dataTransfer.files">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                            <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                        </svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="image" class="relative cursor-pointer bg-transparent rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                                <span>Pilih file</span>
                                                <input id="image" name="image" type="file" accept="image/*" class="sr-only" x-ref="fileInput" x-on:change="fileName = $event.target.files[0].name">
                                            </label>
                                            <p class="pl-1">atau seret dan lepas di sini</p>
                                        </div>
                                        <p class="text-xs text-secondary-500">
                                            PNG, JPG, GIF hingga 2MB
                                        </p>
                                        <p x-show="fileName" x-text="fileName" class="text-sm text-primary-600 font-semibold mt-2 break-all"></p>
                                    </div>
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
                                <input id="unit" class="input-field" type="text" name="unit" value="{{ old('unit', 'Pcs') }}" placeholder="Pcs, Set, Unit" />
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
                                    <input id="price" class="input-field pl-10" type="number" name="price" value="{{ old('price') }}" placeholder="0" />
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
</x-app-layout>
