<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">
                    {{ __('ui.create_inventory_title') }}
                </h1>
                <p class="mt-1 text-sm text-secondary-500">{{ __('ui.create_inventory_subtitle') }}</p>
            </div>



            <form action="{{ route('inventory.store') }}" method="POST" enctype="multipart/form-data" 
                  @submit="isSubmitting = true" 
                  x-data="inventoryForm()"
                  @trigger-check-pn="checkPN($event.detail)"
                  @update-pn="partNumber = $event.detail"
                  @update-name="itemName = $event.detail"
                  @update-brand="itemBrand = $event.detail"
                  @update-category="itemCategory = $event.detail" novalidate>
                @csrf
                
                <div class="space-y-4">
                    <!-- Section 1: Informasi Dasar -->
                    <div class="card p-6 overflow-visible">
                        <div class="mb-4 border-b border-secondary-100 pb-2">
                            <h2 class="text-lg font-semibold text-secondary-900">{{ __('ui.section_basic') }}</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Tipe Barang -->
                            <div class="col-span-1 md:col-span-2">
                                <label for="type_sale" class="input-label mb-3 block">{{ __('ui.item_type') }} <span class="text-danger-500">*</span></label>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                    <label class="cursor-pointer border rounded-xl p-4 flex items-start gap-4 hover:bg-secondary-50 transition-all duration-200" :class="{ 'border-primary-500 bg-primary-50 ring-1 ring-primary-500': type === 'sale', 'border-secondary-200': type !== 'sale' }">
                                        <div class="mt-1">
                                            <input type="radio" id="type_sale" name="type" value="sale" x-model="type" class="text-primary-600 focus:ring-primary-500 w-5 h-5">
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
                                <!-- Part Number Component -->
                                @include('inventory.partials.form.part_number')

                                <!-- Name Component -->
                                @include('inventory.partials.form.name')
                            </div>

                            <!-- Row 2: Merk & Kategori -->
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 col-span-full">
                                <!-- Brand Component -->
                                @include('inventory.partials.form.brand')

                                <!-- Category Component -->
                                @include('inventory.partials.form.category')
                            </div>

                            <!-- Row 3: Warna, Usia & Kondisi -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 col-span-full" x-data="{ 
                                selectedAge: @js(old("age", "")),
                                selectedCondition: @js(old("condition", ""))
                            }" x-effect="if(selectedAge === 'Baru' && !selectedCondition) { selectedCondition = 'Baik'; }">
                                <!-- Color Component -->
                                @include('inventory.partials.form.color')

                                <!-- Age Component -->
                                @include('inventory.partials.form.age')

                                <!-- Condition Component -->
                                @include('inventory.partials.form.condition')
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
                                     x-on:drop.prevent="isDragging = false; 
                                                      const file = $event.dataTransfer.files[0];
                                                      if (file.size > 17 * 1024 * 1024) {
                                                          window.showAlert('Error', 'Ukuran gambar maksimal 17MB', 'error');
                                                          $refs.fileInput.value = '';
                                                          return;
                                                      }
                                                      fileName = file.name; 
                                                      $refs.fileInput.files = $event.dataTransfer.files; 
                                                      // Create local preview from dropped file
                                                      const reader = new FileReader();
                                                      reader.onload = (e) => { 
                                                          imagePreview = e.target.result; 
                                                          localStorage.setItem('temp_inventory_image', e.target.result);
                                                      };
                                                      reader.readAsDataURL(file);
                                     "
                                     @paste.window="
                                        const items = ($event.clipboardData || $event.originalEvent.clipboardData).items;
                                        for (let index in items) {
                                            const item = items[index];
                                            if (item.kind === 'file' && item.type.startsWith('image/')) {
                                                $event.preventDefault();
                                                const file = item.getAsFile();
                                                if (file.size > 17 * 1024 * 1024) {
                                                    window.showAlert('Error', 'Ukuran gambar maksimal 17MB', 'error');
                                                    return;
                                                }
                                                fileName = file.name;
                                                const dataTransfer = new DataTransfer();
                                                dataTransfer.items.add(file);
                                                $refs.fileInput.files = dataTransfer.files;
                                                const reader = new FileReader();
                                                reader.onload = (e) => { 
                                                    imagePreview = e.target.result; 
                                                    localStorage.setItem('temp_inventory_image', e.target.result);
                                                };
                                                reader.readAsDataURL(file);
                                                break;
                                            }
                                        }
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
                                                       x-on:change="
                                                                    const file = $event.target.files[0];
                                                                    if (file && file.size > 17 * 1024 * 1024) {
                                                                        window.showAlert('Error', 'Ukuran gambar maksimal 17MB', 'error');
                                                                        $event.target.value = '';
                                                                        return;
                                                                    }
                                                                    if (file) {
                                                                        fileName = file.name;
                                                                        // Create local preview
                                                                        const reader = new FileReader();
                                                                        reader.onload = (e) => { 
                                                                            imagePreview = e.target.result; 
                                                                            localStorage.setItem('temp_inventory_image', e.target.result);
                                                                        };
                                                                        reader.readAsDataURL(file);
                                                                    }
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
                            <h2 class="text-lg font-semibold text-secondary-900">{{ __('ui.section_location_stock') }}</h2>
                        </div>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Location Component -->
                            @include('inventory.partials.form.location')

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
                                <input id="stock" class="input-field" type="number" name="stock" value="{{ old('stock') }}" min="0" @keypress="if(!/[0-9]/.test($event.key)) $event.preventDefault()" data-testid="input-stock" required />
                                <x-input-error :messages="$errors->get('stock')" class="mt-2" />
                            </div>
                            
                            <!-- Satuan (Creatable Select) -->
                            <div class="relative" x-data="{
                                open: false,
                                search: @js(old('unit', '')),
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
                            }" @click.outside="open = false" @keydown.escape.window="open = false">
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
                                           autocomplete="off" minlength="1" maxlength="20" pattern="[a-zA-Z0-9\s]+" title="Satuan hanya boleh berisi huruf, angka, dan spasi">
                                    
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
                            <h2 class="text-lg font-semibold text-secondary-900">{{ __('ui.section_price_status') }}</h2>
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
                                        if (value != null && value.toString() !== this.rawPrice) {
                                            const numVal = parseInt(value) || 0;
                                            this.rawPrice = numVal.toString();
                                            this.displayPrice = !isNaN(numVal) ? numVal.toLocaleString('id-ID') : '';
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
                                        'aktif' => __('ui.active'),
                                        'nonaktif' => __('ui.inactive'),
                                    ];
                                @endphp
                                <x-select name="status" :options="$statusOptions" :selected="old('status', 'aktif')" placeholder="{{ __('ui.select_status') }}" width="w-full" />
                                <x-input-error :messages="$errors->get('status')" class="mt-2" />
                            </div>
                         </div>
                    </div>
                    @include('inventory.partials.scan-modal')
                </div>

                <div class="flex flex-col sm:flex-row items-center justify-between mt-6 gap-4">
                    <!-- Left side: Draft status / Auto-save indicator -->
                    <div class="flex items-center gap-2 text-xs sm:text-sm text-secondary-500 font-medium order-2 sm:order-1 w-full justify-center sm:justify-start">
                        <div class="relative flex h-3 w-3">
                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary-400 opacity-75" x-show="isSavingDraft"></span>
                            <span class="relative inline-flex rounded-full h-3 w-3" :class="isSavingDraft ? 'bg-primary-500' : (hasDraft ? 'bg-success-500' : 'bg-secondary-300')"></span>
                        </div>
                        <span x-show="!hasDraft && !isSavingDraft">{{ __('ui.draft_empty') }}</span>
                        <span x-show="isSavingDraft">{{ __('ui.draft_saving') }}</span>
                        <span x-show="hasDraft && !isSavingDraft">{{ __('ui.draft_auto') }}</span>
                    </div>

                    <!-- Right side: Actions -->
                    <div class="flex items-center gap-3 w-full sm:w-auto order-1 sm:order-2">
                        <a href="{{ route('inventory.index') }}" class="btn btn-secondary flex-1 sm:flex-none justify-center" @click="clearDraft()">
                            {{ __('ui.cancel') }}
                        </a>
                        <button type="submit" data-testid="btn-submit-inventory" class="btn btn-primary flex-1 sm:flex-none justify-center" :disabled="isSubmitting" :class="{ 'opacity-75 cursor-not-allowed': isSubmitting }">
                            <span x-show="!isSubmitting">{{ __('ui.save_sparepart') }}</span>
                            <span x-show="isSubmitting" class="flex items-center gap-2">
                                <svg class="animate-spin h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                                {{ __('ui.saving') }}
                            </span>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
    @push('scripts')
    @include('inventory.partials._create_scripts')
    @endpush
    
    @include('inventory.partials.success-modal')
    <x-unsaved-changes-warning />
</x-app-layout>


