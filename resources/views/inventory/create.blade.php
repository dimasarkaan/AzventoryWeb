<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                    {{ __('ui.create_inventory_title') }}
                </h2>
                <p class="mt-1 text-sm text-secondary-500">{{ __('ui.create_inventory_subtitle') }}</p>
            </div>



            <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data">
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
                            <h3 class="text-lg font-semibold text-secondary-900">{{ __('ui.section_basic') }}</h3>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Tipe Barang -->
                            <div class="col-span-1 md:col-span-2">
                                <label class="input-label mb-3 block">{{ __('ui.item_type') }} <span class="text-danger-500">*</span></label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-4 hover:bg-secondary-50 transition-all duration-200" :class="{ 'border-primary-500 bg-primary-50 ring-1 ring-primary-500': type === 'sale', 'border-secondary-200': type !== 'sale' }">
                                        <div class="mt-1">
                                            <input type="radio" name="type" value="sale" x-model="type" class="text-primary-600 focus:ring-primary-500 w-5 h-5">
                                        </div>
                                        <div>
                                            <span class="block font-semibold text-secondary-900 text-base">{{ __('ui.type_sale') }}</span>
                                            <span class="block text-sm text-secondary-500 mt-1">{{ __('ui.type_sale_desc') }}</span>
                                        </div>
                                    </label>
                                    <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-4 hover:bg-secondary-50 transition-all duration-200" :class="{ 'border-primary-500 bg-primary-50 ring-1 ring-primary-500': type === 'asset', 'border-secondary-200': type !== 'asset' }">
                                        <div class="mt-1">
                                            <input type="radio" name="type" value="asset" x-model="type" class="text-primary-600 focus:ring-primary-500 w-5 h-5">
                                        </div>
                                        <div>
                                            <span class="block font-semibold text-secondary-900 text-base">{{ __('ui.type_asset') }}</span>
                                            <span class="block text-sm text-secondary-500 mt-1">{{ __('ui.type_asset_desc') }}</span>
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
                                    <label for="part_number" class="input-label">{{ __('ui.part_number') }} <span class="text-danger-500">*</span></label>
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
                                                   placeholder="{{ __('ui.placeholder_pn') }}" 
                                                   autocomplete="off" />
                                            
                                            <!-- Chevron Button -->
                                            <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2 text-secondary-400" @click="!isLocked && (open = !open)" :disabled="isLocked">
                                                <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                    <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                                </svg>
                                            </button>

                                            <!-- Loading Spinner -->
                                            <div x-show="isLoading" class="absolute right-10 top-3">
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

                                                <!-- No Data State -->
                                                <div x-show="filteredOptions.length === 0 && search.length === 0" class="px-3 py-2 text-sm text-secondary-500 italic">
                                                    {{ __('ui.no_data') }}
                                                </div>

                                                <!-- Create New Option -->
                                                <div x-show="search.length > 0 && !options.some(o => o === search)" 
                                                     @click="createNew()"
                                                     class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-primary-600 hover:bg-primary-50 border-t border-secondary-100">
                                                    <span class="block truncate">
                                                        {!! __('ui.use_search', ['search' => '<span x-text="search" class="font-bold"></span>']) !!}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                        <button type="button" @click="window.triggerScanModal()" class="btn btn-secondary px-3" title="{{ __('ui.scan_pn') }}">
                                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 013.75 9.375v-4.5zM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 01-1.125-1.125v-4.5zM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0113.5 9.375v-4.5z" />
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75zM6.75 16.5h.75v.75h-.75v-.75zM16.5 6.75h.75v.75h-.75v-.75zM13.5 13.5h.75v.75h-.75v-.75zM13.5 19.5h.75v.75h-.75v-.75zM19.5 13.5h.75v.75h-.75v-.75zM16.5 16.5h.75v.75h-.75v-.75zM16.5 19.5h.75v.75h-.75v-.75z" />
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Scan Modal -->
                                    <!-- Scan Modal -->
                                    @include('inventory.partials.scan-modal')
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
                                    <label for="name" class="input-label">{{ __('ui.name') }} <span class="text-danger-500">*</span></label>
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
                                               placeholder="{{ __('ui.placeholder_name') }}" 
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
                                                {!! __('ui.add_new', ['search' => '<span x-text="search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase())" class="font-bold"></span>']) !!}
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
                                    <label for="brand" class="input-label">{{ __('ui.brand') }} <span class="text-danger-500">*</span></label>
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
                                               placeholder="{{ __('ui.placeholder_brand') }}" 
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
                                                {!! __('ui.add_new', ['search' => '<span x-text="search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase())" class="font-bold"></span>']) !!}
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
                                    <label for="category" class="input-label">{{ __('ui.category') }} <span class="text-danger-500">*</span></label>
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
                                               placeholder="{{ __('ui.placeholder_category') }}" 
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
                                                {!! __('ui.add_new', ['search' => '<span x-text="search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase())" class="font-bold"></span>']) !!}
                                            </span>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('category')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Row 3: Warna, Usia & Kondisi -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 col-span-full" x-data="{ 
                                selectedAge: '{{ old("age", "") }}',
                                selectedCondition: '{{ old("condition", "") }}'
                            }" x-effect="if(selectedAge === 'Baru' && !selectedCondition) { selectedCondition = 'Baik'; }">
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
                                        if (this.itemColor || '{{ old('color') }}') {
                                            this.selected = this.itemColor || '{{ old('color') }}';
                                            this.search = this.selected;
                                            this.itemColor = this.selected;
                                        }
                                    }
                                }" @click.outside="open = false">
                                    <label for="color" class="input-label">{{ __('ui.color') }}</label>
                                    <div class="relative">
                                        <input type="hidden" name="color" x-model="selected">
                                        <input type="text" 
                                               id="color"
                                               class="input-field w-full pr-10 cursor-text" 
                                               x-model="search" 
                                               @focus="open = true; $el.select()" 
                                               @input="open = true; selected = search; itemColor = search" 
                                               @keydown.enter.prevent="createNew()"
                                               placeholder="{{ __('ui.placeholder_color') }}" 
                                               autocomplete="off">
                                        
                                        <button type="button" class="absolute inset-y-0 right-0 flex items-center pr-2 text-secondary-400" @click="open = !open">
                                            <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

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

                                        <div x-show="search.length > 0 && !options.some(o => o.toLowerCase() === search.toLowerCase())" 
                                             @click="createNew()"
                                             class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-primary-600 hover:bg-primary-50 border-t border-secondary-100">
                                            <span class="block truncate">
                                                {!! __('ui.add_new', ['search' => '<span x-text="search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase())" class="font-bold"></span>']) !!}
                                            </span>
                                        </div>
                                    </div>
                                    <x-input-error :messages="$errors->get('color')" class="mt-2" />
                                </div>

                                <!-- Status Pemakaian (Age) - Custom Dropdown -->
                                <div class="relative" x-data="{
                                    open: false,
                                    selected: selectedAge,
                                    options: ['{{ __('ui.age_new') }}', '{{ __('ui.age_used') }}'],
                                    placeholder: '{{ __('ui.select_age') }}',
                                    select(value) {
                                        this.selected = value;
                                        selectedAge = value;
                                        this.open = false;
                                    }
                                }" @click.outside="open = false">
                                    <label for="age" class="input-label">Status Pemakaian <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="age" x-model="selected">
                                        <button type="button" 
                                                @click="open = !open"
                                                class="input-field w-full text-left pr-10"
                                                :class="{'text-secondary-400': !selected, 'text-secondary-900': selected}">
                                            <span x-text="selected || placeholder"></span>
                                            <svg class="h-5 w-5 text-secondary-400 absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="margin: auto 0.5rem auto auto;">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div x-show="open" 
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                        <template x-for="option in options" :key="option">
                                            <div @click="select(option)" 
                                                 class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-primary-50 text-secondary-900"
                                                 :class="{'bg-primary-50': selected === option}">
                                                <span x-text="option" class="block truncate" :class="{ 'font-semibold': selected === option, 'font-normal': selected !== option }"></span>
                                            </div>
                                        </template>
                                    </div>
                                    <x-input-error :messages="$errors->get('age')" class="mt-2" />
                                </div>

                                <!-- Kondisi Barang (Condition) - Custom Dropdown -->
                                <div class="relative" x-data="{
                                    open: false,
                                    selected: selectedCondition,
                                    options: ['{{ __('ui.condition_good') }}', '{{ __('ui.condition_bad') }}', '{{ __('ui.condition_lost') }}'],
                                    placeholder: '{{ __('ui.select_condition') }}',
                                    select(value) {
                                        this.selected = value;
                                        selectedCondition = value;
                                        this.open = false;
                                    }
                                }" @click.outside="open = false" x-effect="selected = selectedCondition">
                                    <label for="condition" class="input-label">Kondisi Barang <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="condition" x-model="selected">
                                        <button type="button" 
                                                @click="open = !open"
                                                class="input-field w-full text-left pr-10"
                                                :class="{'text-secondary-400': !selected, 'text-secondary-900': selected}">
                                            <span x-text="selected || placeholder"></span>
                                            <svg class="h-5 w-5 text-secondary-400 absolute inset-y-0 right-0 flex items-center pr-2 pointer-events-none" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" style="margin: auto 0.5rem auto auto;">
                                                <path fill-rule="evenodd" d="M10 3a1 1 0 01.707.293l3 3a1 1 0 01-1.414 1.414L10 5.414 7.707 7.707a1 1 0 01-1.414-1.414l3-3A1 1 0 0110 3zm-3.707 9.293a1 1 0 011.414 0L10 14.586l2.293-2.293a1 1 0 011.414 1.414l-3 3a1 1 0 01-1.414 0l-3-3a1 1 0 010-1.414z" clip-rule="evenodd" />
                                            </svg>
                                        </button>
                                    </div>

                                    <div x-show="open" 
                                         x-transition:leave="transition ease-in duration-100"
                                         x-transition:leave-start="opacity-100"
                                         x-transition:leave-end="opacity-0"
                                         class="absolute z-50 mt-1 w-full bg-white shadow-lg max-h-60 rounded-md py-1 text-base ring-1 ring-black ring-opacity-5 overflow-auto focus:outline-none sm:text-sm">
                                        <template x-for="option in options" :key="option">
                                            <div @click="select(option)" 
                                                 class="cursor-pointer select-none relative py-2 pl-3 pr-9 hover:bg-primary-50 text-secondary-900"
                                                 :class="{'bg-primary-50': selected === option}">
                                                <span x-text="option" class="block truncate" :class="{ 'font-semibold': selected === option, 'font-normal': selected !== option }"></span>
                                            </div>
                                        </template>
                                    </div>

                                    <x-input-error :messages="$errors->get('condition')" class="mt-2" />
                                </div>
                            </div>

                            <!-- Gambar (Optional) -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="image" class="input-label">{{ __('ui.image') }}</label>
                                
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
                                                      reader.onload = (e) => { 
                                                          imagePreview = e.target.result; 
                                                          localStorage.setItem('temp_inventory_image', e.target.result);
                                                      };
                                                      reader.readAsDataURL(file);
                                     ">
                                    
                                    <!-- Preview Area -->
                                    <template x-if="imagePreview">
                                        <div class="mb-4 relative group">
                                            <img :src="imagePreview" class="h-40 w-auto object-contain rounded-md shadow-sm border border-secondary-200">
                                            <button type="button" @click="imagePreview = null; fileName = null; existingImage = ''; $refs.fileInput.value = ''; localStorage.removeItem('temp_inventory_image');" 
                                                class="absolute -top-2 -right-2 bg-danger-500 text-white rounded-full p-1 shadow-md hover:bg-danger-600 focus:outline-none transition-colors"
                                                title="{{ __('ui.delete') }}">
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
                                                <span>{{ __('ui.choose_file') }}</span>
                                                <input id="image" name="image" type="file" accept="image/*" class="sr-only" x-ref="fileInput" 
                                                       x-on:change="fileName = $event.target.files[0].name;
                                                                    // Create local preview
                                                                    const file = $event.target.files[0];
                                                                    const reader = new FileReader();
                                                                    reader.onload = (e) => { 
                                                                        imagePreview = e.target.result; 
                                                                        localStorage.setItem('temp_inventory_image', e.target.result);
                                                                    };
                                                                    reader.readAsDataURL(file);
                                                       ">
                                            </label>
                                            <p>{{ __('ui.drag_drop') }}</p>
                                        </div>
                                        <p class="text-xs text-secondary-500">
                                            {{ __('ui.image_help') }}
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
                            <h3 class="text-lg font-semibold text-secondary-900">{{ __('ui.section_location_stock') }}</h3>
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
                                <label for="location" class="input-label">{{ __('ui.location') }} <span class="text-danger-500">*</span></label>
                                <div class="relative">
                                    <input type="hidden" name="location" x-model="selected">
                                    <input type="text" 
                                           class="input-field w-full pr-10 cursor-text" 
                                           x-model="search" 
                                           @focus="open = true; $el.select()" 
                                           @input="open = true; selected = search" 
                                           placeholder="{{ __('ui.select_location') }}" 
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
                                    @can('create', \App\Models\Location::class)
                                        <div x-show="search.length > 0 && !filteredOptions.includes(search)" 
                                             @click="select(search); open = false"
                                             class="cursor-pointer select-none relative py-2 pl-3 pr-9 text-primary-600 hover:bg-primary-50 border-t border-secondary-100">
                                            <span class="block truncate">
                                                {!! __('ui.add_new', ['search' => '<span x-text="search" class="font-bold"></span>']) !!}
                                            </span>
                                        </div>
                                    @else
                                         <div x-show="filteredOptions.length === 0" class="cursor-default select-none relative py-2 pl-3 pr-9 text-secondary-500 italic">
                                            {{ __('ui.location_not_found') }}
                                        </div>
                                    @endcan
                                </div>
                                <x-input-error :messages="$errors->get('location')" class="mt-2" />
                            </div>

                            <!-- Minimum Stok -->
                            <div>
                                <label for="minimum_stock" class="input-label">{{ __('ui.minimum_stock') }}</label>
                                <input id="minimum_stock" class="input-field" type="number" name="minimum_stock" value="{{ old('minimum_stock', 5) }}" min="0" @keypress="if(!/[0-9]/.test($event.key)) $event.preventDefault()" />
                                <p class="text-xs text-secondary-400 mt-1">{{ __('ui.minimum_stock_help') }}</p>
                                <x-input-error :messages="$errors->get('minimum_stock')" class="mt-2" />
                            </div>
                            
                            <!-- Stok Saat Ini -->
                            <div>
                                <label for="stock" class="input-label">{{ __('ui.current_stock') }} <span class="text-danger-500">*</span></label>
                                <input id="stock" class="input-field" type="number" name="stock" value="{{ old('stock') }}" min="0" @keypress="if(!/[0-9]/.test($event.key)) $event.preventDefault()" />
                                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
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
                                <label for="unit" class="input-label">{{ __('ui.unit') }}</label>
                                <div class="relative">
                                    <input type="hidden" name="unit" x-model="selected">
                                    <input type="text" 
                                           id="unit"
                                           class="input-field w-full pr-10 cursor-text" 
                                           x-model="search" 
                                           @focus="open = true; $el.select()" 
                                           @input="open = true; selected = search; itemUnit = search" 
                                           @keydown.enter.prevent="createNew()"
                                           placeholder="{{ __('ui.placeholder_unit') }}" 
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
                                            {!! __('ui.add_new', ['search' => '<span x-text="search.toLowerCase().replace(/\b\w/g, s => s.toUpperCase())" class="font-bold"></span>']) !!}
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
                            <h3 class="text-lg font-semibold text-secondary-900">{{ __('ui.section_price_status') }}</h3>
                        </div>
                         <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                             <!-- Harga -->
                            <div x-show="type === 'sale'" x-data="{
                                displayPrice: '',
                                rawPrice: '',
                                formatPrice() {
                                    // Remove all non-digit characters
                                    let value = this.displayPrice.replace(/[^0-9]/g, '');
                                    this.rawPrice = value;
                                    
                                    // Format with thousand separator (dot)
                                    if (value) {
                                        this.displayPrice = parseInt(value).toLocaleString('id-ID');
                                    } else {
                                        this.displayPrice = '';
                                    }
                                    
                                    // Update Alpine itemPrice for form submission
                                    this.itemPrice = value;
                                },
                                init() {
                                    // Watch for external updates (from PN lookup)
                                    this.$watch('itemPrice', (value) => {
                                        if (value && value !== this.rawPrice) {
                                            this.rawPrice = value.toString();
                                            this.displayPrice = parseInt(value).toLocaleString('id-ID');
                                        }
                                    });
                                    
                                    // Initialize with existing value
                                    if (this.itemPrice) {
                                        this.rawPrice = this.itemPrice.toString();
                                        this.displayPrice = parseInt(this.itemPrice).toLocaleString('id-ID');
                                    }
                                }
                            }">
                                <label for="price" class="input-label">{{ __('ui.unit_price') }} <span class="text-danger-500">*</span></label>
                                <div class="relative">
                                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                        <span class="text-secondary-500 font-medium">Rp</span>
                                    </div>
                                    <input type="hidden" name="price" x-model="rawPrice">
                                    <input 
                                        id="price" 
                                        class="input-field pl-10 {{ auth()->user()->role === \App\Enums\UserRole::ADMIN ? 'bg-gray-100 text-gray-500 cursor-not-allowed' : '' }}" 
                                        type="text" 
                                        x-model="displayPrice"
                                        @if(auth()->user()->role !== \App\Enums\UserRole::ADMIN)
                                            @input="formatPrice()"
                                            @keypress="if(!/[0-9]/.test($event.key)) $event.preventDefault()"
                                        @endif
                                        placeholder="0" 
                                        autocomplete="off"
                                        {{ auth()->user()->role === \App\Enums\UserRole::ADMIN ? 'readonly' : '' }}
                                    />
                                </div>
                                <x-input-error :messages="$errors->get('price')" class="mt-2" />
                            </div>

                            <!-- Status -->
                            <div>
                                <label for="status" class="input-label">{{ __('ui.status') }} <span class="text-danger-500">*</span></label>
                                @php
                                    $statusOptions = [
                                        'aktif' => 'Aktif',
                                        'nonaktif' => 'Nonaktif',
                                    ];
                                @endphp
                                <x-select name="status" :options="$statusOptions" :selected="old('status', 'aktif')" placeholder="{{ __('ui.select_status') }}" width="w-full" />
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                         </div>
                    </div>
                    @include('inventory.partials.scan-modal')
                </div>

                <div class="flex items-center justify-end gap-3 mt-4">
                    <a href="{{ route('inventory.index') }}" class="btn btn-secondary">
                        {{ __('ui.cancel') }}
                    </a>
                    <button type="submit" class="btn btn-primary">
                        {{ __('ui.save_sparepart') }}
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
                type: '{{ old('type', 'sale') }}',
                partNumber: '{{ old('part_number') }}',
                isLocked: false,
                itemName: '{{ old('name') }}',
                itemBrand: '{{ old('brand') }}',
                itemCategory: '{{ old('category') }}',
                itemColor: '{{ old('color') }}', 
                itemUnit: '{{ old('unit', 'Pcs') }}',
                itemPrice: '{{ old('price') }}',
                imagePreview: null,
                existingImage: '', // Store path for backend
                isLoading: false,

                init() {
                    // 1. Setup Global Trigger for Scan Modal
                    window.triggerScanModal = () => {
                        console.log('Trigger Scan Modal via Global Function');
                        this.openScanModal();
                    }

                    // 2. Check for Pre-filled PN
                    if (this.partNumber) {
                         this.checkPN();
                    }

                    // 3. Restore Image from LocalStorage if Validation Failed
                    const hasErrors = {{ $errors->any() ? 'true' : 'false' }};
                    if (hasErrors) {
                        const storedImage = localStorage.getItem('temp_inventory_image');
                        if (storedImage) {
                            this.imagePreview = storedImage;
                            // Restore file input logic
                            fetch(storedImage)
                                .then(res => res.blob())
                                .then(blob => {
                                    const file = new File([blob], "restored-image.png", { type: blob.type });
                                    const dataTransfer = new DataTransfer();
                                    dataTransfer.items.add(file);
                                    // Use x-ref to access input from parent scope (wait for DOM)
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
                    }
                },

                async checkPN() {
                    if (!this.partNumber) return;
                    
                    this.isLoading = true;
                    try {
                        const response = await axios.get('{{ route("inventory.check-part-number") }}', {
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
                
                scanErrorMsg: null,
                stream: null,
                debugMode: false,
                debugImage: null,
                videoDevices: [],
                currentDeviceIndex: 0,
                currentDeviceLabel: '',

                openScanModal() {
                    console.log('Open Scan Modal Triggered');
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
                    this.scanErrorMsg = null;

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
                            throw new Error("{{ __('ui.ocr_no_data') }}");
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
                            const successMsg = @json(__('ui.ocr_success', ['brand' => '__BRAND__', 'pn' => '__PN__']));
                            this.scanErrorMsg = successMsg.replace('__BRAND__', foundBrand || '-').replace('__PN__', foundPN || '-');
                        }
                        
                        if (this.partNumber) this.checkPN();

                    } catch (err) {
                        console.error("Analysis Error:", err);
                        this.scanErrorMsg = "{{ __('ui.ocr_error') }}: " + (err.message || "Kesalahan sistem.");
                    } finally {
                        if (worker) await worker.terminate();
                        if (!this.scanErrorMsg) this.ocrLoading = false;
                    }
                }
            }))
        })
    </script>
    @endpush
</x-app-layout>
