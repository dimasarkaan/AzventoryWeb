<!-- Warna (Creatable Select) -->
                                <div class="relative" x-data="{
                                    open: false,
                                    search: @js(old('color', $defaultColor ?? '')),
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
                                        if (this.itemColor || @js(old('color', $defaultColor ?? ''))) {
                                            this.selected = this.itemColor || @js(old('color', $defaultColor ?? ''));
                                            this.search = this.selected;
                                            this.itemColor = this.selected;
                                        }
                                    }
                                }" @click.outside="open = false" @keydown.escape.window="open = false">
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
                                               autocomplete="off" minlength="2" maxlength="50" pattern="[a-zA-Z\s\-]+" title="Warna hanya boleh berisi huruf, spasi, dan strip">
                                        
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