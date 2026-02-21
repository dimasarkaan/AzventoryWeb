<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                    {{ __('ui.reports_center') }}
                </h2>
                <p class="mt-1 text-sm text-secondary-500">{{ __('ui.reports_desc') }}</p>
            </div>

            <!-- Main Form Card -->
            <div>
                <form action="{{ route('reports.download') }}" method="GET" class="bg-white rounded-xl border border-secondary-200 shadow-card p-6 overflow-visible" x-data="{ reportType: 'inventory_list', period: 'this_month' }">
                    @csrf
                    
                    <!-- Report Categories -->
                    <div class="mb-8">
                        <label class="block text-sm font-bold text-secondary-900 mb-4">{{ __('ui.choose_report_type') }}</label>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                            <!-- Inventaris -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="report_type" value="inventory_list" x-model="reportType" class="peer sr-only">
                                <div class="p-5 rounded-xl border-2 border-secondary-100 hover:border-primary-400 peer-checked:border-primary-600 peer-checked:bg-primary-50 transition-all h-full flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full bg-primary-100 text-primary-600 flex items-center justify-center mb-3 group-hover:bg-primary-200 transition-colors">
                                        <x-icon.inventory class="w-6 h-6" />
                                    </div>
                                    <span class="font-bold text-secondary-900 block mb-1">{{ __('ui.inventory_data') }}</span>
                                    <span class="text-xs text-secondary-500 leading-tight">{{ __('ui.inventory_data_desc') }}</span>
                                </div>
                                <div class="absolute inset-0 border-2 border-primary-600 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                            </label>

                            <!-- Mutasi Stok -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="report_type" value="stock_mutation" x-model="reportType" class="peer sr-only">
                                <div class="p-5 rounded-xl border-2 border-secondary-100 hover:border-primary-400 peer-checked:border-primary-600 peer-checked:bg-primary-50 transition-all h-full flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full bg-warning-100 text-warning-600 flex items-center justify-center mb-3 group-hover:bg-warning-200 transition-colors">
                                        <x-icon.mutation class="w-6 h-6" />
                                    </div>
                                    <span class="font-bold text-secondary-900 block mb-1">{{ __('ui.stock_mutation_history') }}</span>
                                    <span class="text-xs text-secondary-500 leading-tight">{{ __('ui.stock_mutation_desc') }}</span>
                                </div>
                                <div class="absolute inset-0 border-2 border-primary-600 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                            </label>

                            <!-- Peminjaman -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="report_type" value="borrowing_history" x-model="reportType" class="peer sr-only">
                                <div class="p-5 rounded-xl border-2 border-secondary-100 hover:border-sky-400 peer-checked:border-sky-600 peer-checked:bg-sky-50 transition-all h-full flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full bg-sky-100 text-sky-600 flex items-center justify-center mb-3 group-hover:bg-sky-200 transition-colors">
                                        <x-icon.borrow-user class="w-6 h-6" />
                                    </div>
                                    <span class="font-bold text-secondary-900 block mb-1">{{ __('ui.borrowing_history_report') }}</span>
                                    <span class="text-xs text-secondary-500 leading-tight">{{ __('ui.borrowing_history_desc') }}</span>
                                </div>
                                <div class="absolute inset-0 border-2 border-sky-600 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                            </label>

                                <!-- Low Stock -->
                            <label class="cursor-pointer relative group">
                                <input type="radio" name="report_type" value="low_stock" x-model="reportType" class="peer sr-only">
                                <div class="p-5 rounded-xl border-2 border-secondary-100 hover:border-primary-400 peer-checked:border-primary-600 peer-checked:bg-primary-50 transition-all h-full flex flex-col items-center text-center">
                                    <div class="w-12 h-12 rounded-full bg-danger-100 text-danger-600 flex items-center justify-center mb-3 group-hover:bg-danger-200 transition-colors">
                                        <x-icon.low-stock class="w-6 h-6" />
                                    </div>
                                    <span class="font-bold text-secondary-900 block mb-1">{{ __('ui.low_stock_report') }}</span>
                                    <span class="text-xs text-secondary-500 leading-tight">{{ __('ui.low_stock_desc') }}</span>
                                </div>
                                <div class="absolute inset-0 border-2 border-primary-600 rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                            </label>
                        </div>
                    </div>

                    <!-- Filters Section -->
                    <div class="border-t border-secondary-200 pt-6">
                        <h3 class="text-sm font-bold text-secondary-900 mb-4 uppercase tracking-wide">{{ __('ui.filter_configuration') }}</h3>
                        
                        <!-- Date Period (Hidden for Inventory/Low Stock snapshot) -->
                        <div class="mb-4 relative" x-show="['stock_mutation', 'borrowing_history'].includes(reportType)" x-transition>
                            <label class="block text-sm font-medium text-secondary-700 mb-2">{{ __('ui.time_period') }}</label>
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
                            }">
                                <button type="button" @click="open = !open" @click.away="open = false" 
                                        class="input-field w-full text-left flex justify-between items-center rounded-xl py-3 px-4 text-base cursor-pointer hover:border-primary-400 focus:ring-2 ring-primary-500 bg-white">
                                    <span x-text="labels[period]"></span>
                                    <svg class="w-5 h-5 text-secondary-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <div x-show="open" 
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-xl border border-secondary-100 overflow-hidden" 
                                        style="display: none;">
                                    <div class="p-2 space-y-1">
                                        <template x-for="(label, key) in labels" :key="key">
                                            <div @click="period = key; open = false" 
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
                        <div class="grid grid-cols-2 gap-4 mb-4" x-show="period === 'custom' && ['stock_mutation', 'borrowing_history'].includes(reportType)" x-transition>
                            <div>
                                <label class="block text-xs text-secondary-500 mb-1">{{ __('ui.start_date') }}</label>
                                <input type="date" name="start_date" class="input-field w-full">
                            </div>
                            <div>
                                <label class="block text-xs text-secondary-500 mb-1">{{ __('ui.end_date') }}</label>
                                <input type="date" name="end_date" class="input-field w-full">
                            </div>
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
                        }">
                            <label class="block text-sm font-medium text-secondary-700 mb-2">{{ __('ui.warehouse_location') }}</label>
                            <input type="hidden" name="location" :value="selected">
                            
                            <div class="relative">
                                <!-- Trigger Button -->
                                <button type="button" @click="open = !open" @click.away="open = false" 
                                        class="input-field w-full text-left flex justify-between items-center rounded-xl py-3 px-4 text-base cursor-pointer hover:border-primary-400 focus:ring-2 ring-primary-500 bg-white">
                                    <span x-text="selectedLabel" :class="{'text-secondary-900': selected, 'text-secondary-500': !selected}"></span>
                                    <svg class="w-5 h-5 text-secondary-400 transition-transform duration-200" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <!-- Custom Dropdown Menu -->
                                <div x-show="open" 
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="transform opacity-0 scale-95"
                                        x-transition:enter-end="transform opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="transform opacity-100 scale-100"
                                        x-transition:leave-end="transform opacity-0 scale-95"
                                        class="absolute z-50 mt-2 w-full bg-white rounded-xl shadow-xl border border-secondary-100 overflow-hidden" 
                                        style="display: none;">
                                    <div class="max-h-60 overflow-y-auto p-2 space-y-1">
                                        <!-- Default Option -->
                                        <div @click="select('', '{{ __('ui.all_locations') }}')" 
                                                class="px-4 py-2 rounded-lg cursor-pointer hover:bg-primary-50 hover:text-primary-700 transition-colors"
                                                :class="{'bg-primary-50 text-primary-700 font-medium': selected === ''}">
                                            {{ __('ui.all_locations') }}
                                        </div>
                                        
                                        @foreach($locations as $loc)
                                            <div @click="select('{{ $loc }}', '{{ $loc }}')" 
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
                        <div class="flex items-center space-x-6">
                            <span class="text-sm font-medium text-secondary-700">{{ __('ui.format_label') }}</span>
                                <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="export_format" value="pdf" checked class="text-primary-600 focus:ring-primary-500 h-4 w-4 border-gray-300">
                                <span class="ml-2 text-sm text-secondary-700 font-medium">{{ __('ui.pdf_document') }}</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="export_format" value="excel" class="text-success-600 focus:ring-success-500 h-4 w-4 border-gray-300">
                                <span class="ml-2 text-sm text-secondary-700 font-medium">{{ __('ui.excel_document') }}</span>
                            </label>
                        </div>
                        <button type="submit" class="btn btn-primary px-8 py-3 text-base flex items-center gap-2 shadow-lg shadow-primary-200">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span>{{ __('ui.download_report') }}</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
