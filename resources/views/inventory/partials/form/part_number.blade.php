<!-- Part Number -->
                                <div>
                                    <label for="part_number" class="input-label">{{ __('ui.part_number') }} <span class="text-danger-500">*</span></label>
                                    <div class="relative flex gap-2" x-data="{
                                        open: false,
                                        search: @js(old('part_number', $defaultPartNumber ?? '')),
                                        selected: @js(old('part_number', $defaultPartNumber ?? '')),
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
                                            this.$dispatch('trigger-check-pn', false);
                                        },
                                        createNew() {
                                            let term = this.search.toUpperCase();
                                            this.select(term);
                                        },
                                        init() {
                                            if (this.selected) {
                                                this.$dispatch('update-pn', this.selected);
                                                this.$dispatch('trigger-check-pn', true);
                                                this.search = this.selected;
                                            }
                                            this.$watch('partNumber', value => {
                                                if (value !== this.selected) {
                                                    this.selected = value;
                                                    this.search = value;
                                                }
                                            });
                                        }
                                    }" @click.outside="open = false" @keydown.escape.window="open = false">
                                        <div class="relative w-full">
                                            <input type="hidden" name="part_number" x-model="selected">
                                            <input id="part_number" class="input-field pr-10 w-full" type="text" data-testid="input-part-number" 
                                                   x-model="search" 
                                                   @input="!isLocked && (open = true, selected = search, partNumber = search.toUpperCase(), search = search.toUpperCase())"
                                                   @focus="!isLocked && (open = true, $el.select())" 
                                                   @change="checkPN"
                                                   @keydown.enter.prevent="createNew()" 
                                                   placeholder="{{ __('ui.placeholder_pn') }}" 
                                                   autocomplete="off" minlength="3" maxlength="255" pattern="[a-zA-Z0-9\-\_\/]+" title="Part Number hanya boleh berisi huruf, angka, strip (-), dan underscore (_)" required />
                                            
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
                                    
                                    <x-input-error :messages="$errors->get('part_number')" class="mt-2" />
                                </div>