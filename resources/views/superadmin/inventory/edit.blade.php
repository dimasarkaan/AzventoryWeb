<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                    {{ __('Edit Sparepart') }}
                </h2>
                <p class="mt-1 text-sm text-secondary-500">Perbarui informasi sparepart.</p>
            </div>

            <form action="{{ route('superadmin.inventory.update', $sparepart) }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="space-y-4" x-data="{ type: '{{ old('type', $sparepart->type ?? 'sale') }}' }">
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Part Number -->
                                <div>
                                    <label for="part_number" class="input-label">Part Number (PN) <span class="text-danger-500">*</span></label>
                                    <div class="relative" x-data="{
                                        open: false,
                                        search: '',
                                        selected: '{{ old('part_number', $sparepart->part_number) }}',
                                        options: {{ json_encode($partNumbers) }} || [],
                                        get filteredOptions() {
                                            if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                            return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                        },
                                        select(value) {
                                            this.selected = value;
                                            this.search = value;
                                            this.open = false;
                                        },
                                        createNew() {
                                            let newValue = this.search.toUpperCase();
                                            this.select(newValue);
                                        },
                                        init() {
                                            if (this.selected && !this.options.includes(this.selected)) {
                                                this.options.push(this.selected);
                                            }
                                            this.search = this.selected || '';
                                        }
                                    }" @click.outside="open = false">
                                        <div class="relative w-full">
                                            <input type="hidden" name="part_number" x-model="selected">
                                            <input id="part_number" class="input-field w-full pr-10" type="text" 
                                                   x-model="search" 
                                                   @focus="open = true, $el.select()" 
                                                   @input="open = true, selected = search, search = search.toUpperCase()"
                                                   @keydown.enter.prevent="createNew()"
                                                   placeholder="Contoh: KBD-LOGI-GPRO-X" 
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
                                    <x-input-error :messages="$errors->get('part_number')" class="mt-2" />
                                </div>

                                <!-- Nama Barang (Creatable Select) -->
                                <div class="relative" x-data="{
                                    open: false,
                                    search: '',
                                    selected: '{{ old('name', $sparepart->name) }}',
                                    options: {{ json_encode($names) }} || [],
                                    get filteredOptions() {
                                        if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                        return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(value) {
                                        this.selected = value;
                                        this.search = value;
                                        this.open = false;
                                    },
                                    createNew() {
                                        let newValue = this.search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                        this.select(newValue);
                                    },
                                    init() {
                                        // Ensure existing non-standard value is in options
                                        if (this.selected && !this.options.includes(this.selected)) {
                                            this.options.push(this.selected);
                                        }
                                        this.search = this.selected || '';
                                    }
                                }" @click.outside="open = false">
                                    <label for="name" class="input-label">Nama Barang <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="name" x-model="selected">
                                        <input type="text" 
                                               id="name"
                                               class="input-field w-full pr-10 cursor-text" 
                                               x-model="search" 
                                               @focus="open = true, $el.select()" 
                                               @input="open = true, selected = search"
                                               @keydown.enter.prevent="createNew()"
                                               placeholder="Contoh: Laptop Dell XPS 15" 
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
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- Merk (Creatable Select) -->
                                <div class="relative" x-data="{
                                    open: false,
                                    search: '',
                                    selected: '{{ old('brand', $sparepart->brand) }}',
                                    options: {{ json_encode($brands) }},
                                    get filteredOptions() {
                                        if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                        return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(value) {
                                        this.selected = value;
                                        this.search = value;
                                        this.open = false;
                                    },
                                    createNew() {
                                        let newValue = this.search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                        this.select(newValue);
                                    },
                                    init() {
                                        // Ensure existing non-standard value is in options
                                        if (this.selected && !this.options.includes(this.selected)) {
                                            this.options.push(this.selected);
                                        }
                                        this.search = this.selected || '';
                                    }
                                }" @click.outside="open = false">
                                    <label for="brand" class="input-label">Merk <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="brand" x-model="selected">
                                        <input type="text" 
                                               id="brand"
                                               class="input-field w-full pr-10 cursor-text" 
                                               x-model="search" 
                                               @focus="open = true, $el.select()" 
                                               @input="open = true, selected = search"
                                               @keydown.enter.prevent="createNew()"
                                               placeholder="Contoh: Dell, Logitech" 
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
                                    <x-input-error :messages="$errors->get('brand')" class="mt-2" />
                                </div>

                                <!-- Kategori (Creatable Select) -->
                                <div class="relative" x-data="{
                                    open: false,
                                    search: '',
                                    selected: '{{ old('category', $sparepart->category) }}',
                                    options: {{ json_encode($categories) }},
                                    get filteredOptions() {
                                        if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                        return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(value) {
                                        this.selected = value;
                                        this.search = value;
                                        this.open = false;
                                    },
                                    createNew() {
                                        let newValue = this.search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                        this.select(newValue);
                                    },
                                    init() {
                                        if (this.selected && !this.options.includes(this.selected)) {
                                            this.options.push(this.selected);
                                        }
                                        this.search = this.selected || '';
                                    }
                                }" @click.outside="open = false">
                                    <label for="category" class="input-label">Kategori <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="category" x-model="selected">
                                        <input type="text" 
                                               id="category"
                                               class="input-field w-full pr-10 cursor-text" 
                                               x-model="search" 
                                               @focus="open = true, $el.select()" 
                                               @input="open = true, selected = search"
                                               @keydown.enter.prevent="createNew()"
                                               placeholder="Pilih atau ketik kategori baru" 
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
                                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Row 3: Warna & Kondisi -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <!-- Warna (Creatable Select) -->
                                <div class="relative" x-data="{
                                    open: false,
                                    search: '',
                                    selected: '{{ old('color', $sparepart->color) }}',
                                    options: {{ json_encode($colors) }},
                                    get filteredOptions() {
                                        if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                        return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                    },
                                    select(value) {
                                        this.selected = value;
                                        this.search = value;
                                        this.open = false;
                                    },
                                    createNew() {
                                        let newValue = this.search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                        this.select(newValue);
                                    },
                                    init() {
                                        if (this.selected && !this.options.includes(this.selected)) {
                                            this.options.push(this.selected);
                                        }
                                        this.search = this.selected || '';
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
                                               @input="open = true; selected = search"
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

                                <!-- Kondisi Barang -->
                                <div>
                                    <label for="condition" class="input-label">Kondisi Barang <span class="text-danger-500">*</span></label>
                                    @php
                                        $conditionOptions = [
                                            'Baru' => 'Baru',
                                            'Bekas' => 'Bekas',
                                            'Rusak' => 'Rusak',
                                        ];
                                    @endphp
                                    <x-select name="condition" :options="$conditionOptions" :selected="old('condition', $sparepart->condition)" placeholder="Pilih Kondisi" width="w-full" />
                                    <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                                </div>
                            </div>            
                            <!-- Gambar (Optional) -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="image" class="input-label">Gambar Barang</label>
                                <div class="mt-1" x-data="{ isDragging: false, fileName: null, imagePreview: null }">
                                    <!-- Existing Image (Visible only if no new image selected) -->
                                    @if($sparepart->image)
                                        <div class="mb-4 flex items-center gap-4" x-show="!imagePreview">
                                            <div class="flex-shrink-0">
                                                <span class="block text-xs text-secondary-500 mb-1">Gambar Saat Ini:</span>
                                                <img src="{{ asset('storage/' . $sparepart->image) }}" class="h-20 w-20 object-cover rounded-lg border border-secondary-200" alt="Current Image">
                                            </div>
                                        </div>
                                    @endif

                                    <div class="w-full flex justify-center px-6 pt-5 pb-6 border-2 border-dashed rounded-md transition-colors duration-200"
                                         :class="{ 'border-primary-400 bg-primary-50': isDragging, 'border-gray-300 hover:border-primary-400': !isDragging }"
                                         x-on:dragover.prevent="isDragging = true"
                                         x-on:dragleave.prevent="isDragging = false"
                                         x-on:drop.prevent="isDragging = false; fileName = $event.dataTransfer.files[0].name; $refs.fileInput.files = $event.dataTransfer.files;
                                                          const file = $event.dataTransfer.files[0];
                                                          const reader = new FileReader();
                                                          reader.onload = (e) => { imagePreview = e.target.result; };
                                                          reader.readAsDataURL(file);
                                         ">
                                        
                                        <!-- New Image Preview -->
                                        <template x-if="imagePreview">
                                            <div class="relative group">
                                                <img :src="imagePreview" class="h-40 w-auto object-contain rounded-md shadow-sm border border-secondary-200">
                                                <button type="button" @click="imagePreview = null; fileName = null; $refs.fileInput.value = ''" 
                                                    class="absolute -top-2 -right-2 bg-danger-500 text-white rounded-full p-1 shadow-md hover:bg-danger-600 focus:outline-none transition-colors"
                                                    title="Hapus gambar">
                                                    <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </button>
                                                <p class="mt-2 text-sm text-primary-600 font-semibold break-all text-center" x-text="fileName"></p>
                                            </div>
                                        </template>

                                        <!-- Upload Placeholder -->
                                        <div x-show="!imagePreview" class="space-y-1 text-center">
                                            <svg class="mx-auto h-12 w-12 text-gray-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true">
                                                <path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" />
                                            </svg>
                                            <div class="flex text-sm text-gray-600 justify-center">
                                                <label for="image" class="relative cursor-pointer bg-transparent rounded-md font-medium text-primary-600 hover:text-primary-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary-500">
                                                    <span>Pilih file baru</span>
                                                    <input id="image" name="image" type="file" accept="image/*" class="sr-only" x-ref="fileInput" 
                                                           x-on:change="fileName = $event.target.files[0].name;
                                                                        const file = $event.target.files[0];
                                                                        const reader = new FileReader();
                                                                        reader.onload = (e) => { imagePreview = e.target.result; };
                                                                        reader.readAsDataURL(file);
                                                           ">
                                                </label>
                                                <p class="pl-1">atau seret ke sini</p>
                                            </div>
                                            <p class="text-xs text-secondary-500">
                                                PNG, JPG, GIF hingga 2MB
                                            </p>
                                        </div>
                                    </div>
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
                                selected: '{{ old('location', $sparepart->location) }}',
                                options: ['Tegal', 'Cibubur'],
                                init() {
                                    // Ensure existing non-standard location is in options
                                    if (this.selected && !this.options.includes(this.selected)) {
                                        this.options.push(this.selected);
                                    }
                                    this.search = this.selected || '';
                                },
                                get filteredOptions() {
                                    if (this.search === '' || this.search === this.selected) return this.options;
                                    return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                },
                                select(value) {
                                    this.selected = value;
                                    this.search = value;
                                    this.open = false;
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

                            <!-- Stok (Read Only / Editable based on policy) - Making Editable for now but usually done via Stock UI -->
                            <div>
                                <label for="stock" class="input-label">Stok Saat Ini <span class="text-danger-500">*</span></label>
                                <input id="stock" class="input-field bg-secondary-50" type="number" name="stock" value="{{ old('stock', $sparepart->stock) }}" />
                                <p class="text-xs text-secondary-400 mt-1">Disarankan menggunakan menu 'Stock In/Out' untuk perubahan stok rutin.</p>
                                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                            </div>
                            
                            <!-- Minimum Stok -->
                            <div>
                                <label for="minimum_stock" class="input-label">Minimum Stok (Alert)</label>
                                <input id="minimum_stock" class="input-field" type="number" name="minimum_stock" value="{{ old('minimum_stock', $sparepart->minimum_stock ?? 5) }}" min="0" />
                                <x-input-error :messages="$errors->get('minimum_stock')" class="mt-2" />
                            </div>
                            
                             <!-- Satuan (Creatable Select) -->
                            <div class="relative" x-data="{
                                open: false,
                                search: '',
                                selected: '{{ old('unit', $sparepart->unit ?? 'Pcs') }}',
                                options: {{ json_encode($units) }},
                                get filteredOptions() {
                                    if (this.search === '' || (this.options.includes(this.search) && this.search === this.selected)) return this.options;
                                    return this.options.filter(option => option.toLowerCase().includes(this.search.toLowerCase()));
                                },
                                select(value) {
                                    this.selected = value;
                                    this.search = value;
                                    this.open = false;
                                },
                                createNew() {
                                    let newValue = this.search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase());
                                    this.select(newValue);
                                },
                                init() {
                                    if (this.selected && !this.options.includes(this.selected)) {
                                        this.options.push(this.selected);
                                    }
                                    this.search = this.selected || '';
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
                                           @input="open = true; selected = search"
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
                                    <input id="price" class="input-field pl-10" type="number" name="price" value="{{ old('price', $sparepart->price) }}" />
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
                                <x-select name="status" :options="$statusOptions" :selected="old('status', $sparepart->status)" placeholder="Pilih Status" width="w-full" />
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
                        {{ __('Simpan Perubahan') }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
