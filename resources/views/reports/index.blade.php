<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">
                    {{ __('ui.reports_center') }}
                </h1>
                <p class="mt-1 text-sm text-secondary-500">{{ __('ui.reports_desc') }}</p>
            </div>

            <!-- Main Form Card -->
            <div>
                <form action="{{ route('reports.download') }}" method="GET" 
                    id="reportForm"
                    class="bg-white rounded-xl border border-secondary-200 shadow-card p-6 overflow-visible" 
                    x-data="reportManager"
                    @submit="downloadReport($event)" novalidate>
                    @csrf
                    
                    <!-- Report Categories -->
                    <div class="mb-8">
                        <span id="report_category_label" class="block text-sm font-bold text-secondary-900 mb-4">{{ __('ui.choose_report_type') }}</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" role="radiogroup" aria-labelledby="report_category_label">
                            <!-- Inventaris -->
                            <x-reports.type-card 
                                value="inventory_list" 
                                color="primary" 
                                title="{{ __('ui.inventory_data') }}" 
                                description="{{ __('ui.inventory_data_desc') }}">
                                <x-slot name="icon"><x-icon.inventory class="w-6 h-6" /></x-slot>
                            </x-reports.type-card>

                            <!-- Mutasi Stok -->
                            <x-reports.type-card 
                                value="stock_mutation" 
                                color="warning" 
                                title="{{ __('ui.stock_mutation_history') }}" 
                                description="{{ __('ui.stock_mutation_desc') }}">
                                <x-slot name="icon"><x-icon.mutation class="w-6 h-6" /></x-slot>
                            </x-reports.type-card>

                            <!-- Peminjaman -->
                            <x-reports.type-card 
                                value="borrowing_history" 
                                color="sky" 
                                title="{{ __('ui.borrowing_history_report') }}" 
                                description="{{ __('ui.borrowing_history_desc') }}">
                                <x-slot name="icon"><x-icon.borrow-user class="w-6 h-6" /></x-slot>
                            </x-reports.type-card>

                            <!-- Low Stock -->
                            <x-reports.type-card 
                                value="low_stock" 
                                color="danger" 
                                title="{{ __('ui.low_stock_report') }}" 
                                description="{{ __('ui.low_stock_desc') }}">
                                <x-slot name="icon"><x-icon.low-stock class="w-6 h-6" /></x-slot>
                            </x-reports.type-card>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="border-t border-secondary-200 pt-6">
                        <h3 class="text-sm font-bold text-secondary-900 mb-4 uppercase tracking-wide">{{ __('ui.filter_configuration') }}</h3>
                        
                        <!-- Date Period (Hidden for Inventory/Low Stock snapshot) -->
                        <div class="mb-4 relative" x-show="['stock_mutation', 'borrowing_history'].includes(reportType)" x-transition>
                            <span id="period_label" class="block text-sm font-medium text-secondary-700 mb-2">{{ __('ui.time_period') }}</span>
                            <input type="hidden" name="period" :value="period">
                            
                            <div x-data="{ 
                                open: false, 
                                labels: {
                                    'this_month': '{{ __('ui.this_month') }}',
                                    'last_month': '{{ __('ui.last_month') }}',
                                    'this_year': '{{ __('ui.this_year') }}',
                                    'all': '{{ __('ui.all_time') }}',
                                    'custom': '{{ __('ui.custom_date') }}'
                                }
                            }" @keydown.escape.window="open = false">
                                <button type="button" @click="open = !open" @click.away="open = false" 
                                        aria-labelledby="period_label"
                                        aria-haspopup="listbox"
                                        :aria-expanded="open"
                                        class="input-field w-full text-left flex justify-between items-center rounded-xl py-3 px-4 text-base cursor-pointer hover:border-primary-400 focus:ring-2 ring-primary-500 bg-white">
                                    <span x-text="labels[period]"></span>
                                    <svg class="w-5 h-5 text-secondary-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                        <div x-show="open" 
                                                role="listbox"
                                                aria-labelledby="period_label"
                                                x-transition:enter="transition ease-out duration-100"
                                                x-transition:enter-start="transform opacity-0 scale-95"
                                                x-transition:enter-end="transform opacity-100 scale-100"
                                                x-transition:leave="transition ease-in duration-75"
                                                x-transition:leave-start="transform opacity-100 scale-100"
                                                x-transition:leave-end="transform opacity-0 scale-95"
                                                class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-xl border border-secondary-100 overflow-hidden" 
                                                x-cloak>
                                            <div class="p-2 space-y-1">
                                                <template x-for="(label, key) in labels" :key="key">
                                                    <div @click="period = key; open = false" 
                                                            role="option"
                                                            :aria-selected="period === key"
                                                            class="px-4 py-2 rounded-lg cursor-pointer transition-colors"
                                                            :class="{'bg-primary-50 text-primary-700 font-medium': period === key, 'text-secondary-700 hover:bg-primary-50 hover:text-primary-700': period !== key}"
                                                            x-text="label">
                                                    </div>
                                                </template>
                                            </div>
                                        </div>
                            </div>
                        </div>

                        <!-- Custom Date Range -->
                        <div class="mb-4 flatpickr-range-container" x-show="period === 'custom' && ['stock_mutation', 'borrowing_history'].includes(reportType)" x-transition>
                            <label for="date_range_picker_hidden" class="block text-xs font-semibold text-secondary-600 uppercase tracking-wider mb-2">{{ __('ui.date_range') }}</label>
                            <div class="relative group">
                                <input type="text" id="date_range_picker_hidden" name="date_range"
                                       class="range-picker-input w-full pl-12 pr-4 py-3 text-sm bg-white border-secondary-300 rounded-xl text-secondary-900 focus:ring-4 focus:ring-primary-500/10 focus:border-primary-500 transition-all cursor-pointer font-semibold placeholder:text-secondary-400"
                                       placeholder="{{ __('ui.select_date_range') }}">
                                <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-secondary-400 group-focus-within:text-primary-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                </div>
                                <input type="hidden" name="start_date" id="start_date_input" class="range-start" x-model="startDate">
                                <input type="hidden" name="end_date" id="end_date_input" class="range-end" x-model="endDate">
                            </div>
                            <template x-if="isDateInvalid">
                                <p class="text-[10px] text-danger-600 mt-1">{{ __('ui.invalid_date_range') }}</p>
                            </template>
                        </div>

                        <!-- Location Filter -->
                        <div class="mb-4" x-data="{ 
                            open: false, 
                            selected: '{{ request('location') }}', 
                            selectedLabel: '{{ request('location') ? request('location') : __('ui.all_locations') }}',
                            select(value, label) {
                                this.selected = value;
                                this.selectedLabel = label;
                                this.open = false;
                            }
                        }" @keydown.escape.window="open = false">
                            <span id="location_label" class="block text-sm font-medium text-secondary-700 mb-2">{{ __('ui.warehouse_location') }}</span>
                            <input type="hidden" name="location" :value="selected">
                            
                            <div class="relative">
                                <!-- Trigger Button -->
                                <button type="button" @click="open = !open" @click.away="open = false" 
                                        aria-labelledby="location_label"
                                        aria-haspopup="listbox"
                                        :aria-expanded="open"
                                        class="input-field w-full text-left flex justify-between items-center rounded-xl py-3 px-4 text-base cursor-pointer hover:border-primary-400 focus:ring-2 ring-primary-500 bg-white">
                                    <span x-text="selectedLabel" :class="{'text-secondary-900': selected, 'text-secondary-500': !selected}"></span>
                                    <svg class="w-5 h-5 text-secondary-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <!-- Custom Dropdown Menu -->
                                <div x-show="open" 
                                        role="listbox"
                                        aria-labelledby="location_label"
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-xl border border-secondary-100 overflow-hidden" 
                                        x-cloak>
                                    <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                                        <!-- Default Option -->
                                        <div @click="select('', '{{ __('ui.all_locations') }}')" 
                                                role="option"
                                                :aria-selected="selected === ''"
                                                class="px-4 py-2 rounded-lg cursor-pointer hover:bg-primary-50 hover:text-primary-700 transition-colors"
                                                :class="{'bg-primary-50 text-primary-700 font-medium': selected === ''}">
                                            {{ __('ui.all_locations') }}
                                        </div>
                                        
                                        @foreach($locations as $loc)
                                            <div @click="select('{{ $loc }}', '{{ $loc }}')" 
                                                    role="option"
                                                    :aria-selected="selected === '{{ $loc }}'"
                                                    class="px-4 py-2 rounded-lg cursor-pointer text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors"
                                                    :class="{'bg-primary-50 text-primary-700 font-medium': selected === '{{ $loc }}'}">
                                                {{ $loc }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                            <p class="text-xs text-secondary-400 mt-1 italic">{{ __('ui.location_placeholder_desc') }}</p>
                        </div>
                    </div>

                    <!-- Format & Action -->
                    <div class="bg-secondary-50 -mx-6 -mb-6 p-6 mt-8 rounded-b-lg flex flex-col sm:flex-row items-center justify-between gap-4 border-t border-secondary-100">
                        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-3 sm:gap-6 w-full sm:w-auto">
                            <span class="text-sm font-medium text-secondary-700">{{ __('ui.format_label') }}</span>
                            
                            <div class="flex p-1 bg-secondary-200/50 rounded-xl w-full sm:w-auto gap-1 sm:gap-0">
                                <!-- PDF Option -->
                                <label class="relative cursor-pointer flex-1 sm:flex-none">
                                    <input type="radio" name="export_format" value="pdf" checked class="peer sr-only">
                                    <div class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 px-2 sm:px-4 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm font-bold text-secondary-500 hover:text-secondary-700 transition-all peer-checked:bg-white peer-checked:text-danger-600 peer-checked:shadow-sm peer-checked:ring-1 peer-checked:ring-secondary-200">
                                        <svg class="w-5 h-5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                        <span class="whitespace-nowrap text-center">{{ __('ui.pdf_document') }}</span>
                                    </div>
                                </label>
                                
                                <!-- Excel Option -->
                                <label class="relative cursor-pointer flex-1 sm:flex-none">
                                    <input type="radio" name="export_format" value="excel" class="peer sr-only">
                                    <div class="flex flex-col sm:flex-row items-center justify-center gap-1 sm:gap-2 px-2 sm:px-4 py-2 sm:py-2.5 rounded-lg text-xs sm:text-sm font-bold text-secondary-500 hover:text-secondary-700 transition-all peer-checked:bg-white peer-checked:text-success-600 peer-checked:shadow-sm peer-checked:ring-1 peer-checked:ring-secondary-200">
                                        <svg class="w-5 h-5 sm:w-4 sm:h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <span class="whitespace-nowrap text-center">{{ __('ui.excel_document') }}</span>
                                    </div>
                                </label>
                            </div>
                        </div>
                        <button type="submit" 
                            :disabled="loading || isDateInvalid"
                            class="btn btn-primary px-8 py-3 text-base flex items-center justify-center gap-2 shadow-lg shadow-primary-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            
                            <!-- State: Normal -->
                            <svg x-show="!loading" style="display: inline-block;" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span x-show="!loading" style="display: inline;" class="whitespace-nowrap font-bold">{{ __('ui.download_report') }}</span>
                            
                            <!-- State: Loading -->
                            <svg x-show="loading" x-cloak class="animate-spin h-5 w-5 text-white flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="loading" x-cloak>{{ __('ui.processing') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    @include('reports.partials._report_scripts')
    @endpush
    
    <x-flatpickr />
</x-app-layout>


