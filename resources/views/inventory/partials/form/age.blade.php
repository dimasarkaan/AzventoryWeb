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
                                }" @click.outside="open = false" @keydown.escape.window="open = false">
                                    <label for="age-dropdown-btn" class="input-label">Status Pemakaian <span class="text-danger-500">*</span></label>
                                    <div class="relative">
                                        <input type="hidden" name="age" x-model="selected">
                                        <button type="button" 
                                                id="age-dropdown-btn"
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