<!-- Nama Barang (Creatable Select) -->
                                <div class="relative" x-data="{
                                    open: false,
                                    search: @js(old('name', $defaultName ?? '')),
                                    selected: @js(old('name', $defaultName ?? '')),
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
                                }" @click.outside="open = false" @keydown.escape.window="open = false">
                                    <label for="name" class="input-label">{{ __('ui.name') }} <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="name" x-model="selected">
                                        <input type="text" 
                                               id="name"
                                               data-testid="input-name"
                                               class="input-field w-full pr-10 cursor-text" 
                                               :class="{'bg-secondary-100 text-secondary-500': isLocked}"
                                               x-model="search"
                                               :readonly="isLocked" 
                                               @focus="!isLocked && (open = true, $el.select())" 
                                               @input="!isLocked && (open = true, selected = search, itemName = search)" 
                                               @keydown.enter.prevent="createNew()"
                                               placeholder="{{ __('ui.placeholder_name') }}" 
                                               autocomplete="off" minlength="3" maxlength="255" pattern="^(?=.*[a-zA-Z])[a-zA-Z0-9][a-zA-Z0-9\s\.\,\&\-\(\)\/]*$" title="Nama barang harus mengandung huruf, diawali huruf/angka, serta hanya berisi huruf/angka/spasi/simbol (.,&-)" required>
                                        
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