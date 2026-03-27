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
                <form action="{{ route('reports.download') }}" method="GET" 
                    id="reportForm"
                    class="bg-white rounded-xl border border-secondary-200 shadow-card p-6 overflow-visible" 
                    x-data="reportManager"
                    @submit="downloadReport($event)">
                    @csrf
                    
                    <!-- Report Categories -->
                    <div class="mb-8">
                        <span id="report_category_label" class="block text-sm font-bold text-secondary-900 mb-4">{{ __('ui.choose_report_type') }}</span>
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4" role="radiogroup" aria-labelledby="report_category_label">
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
                            }">
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
                                                style="display: none;">
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
                        <div class="grid grid-cols-2 gap-4 mb-4" x-show="period === 'custom' && ['stock_mutation', 'borrowing_history'].includes(reportType)" x-transition>
                            <div>
                                <label for="start_date_input" class="block text-xs text-secondary-500 mb-1">{{ __('ui.start_date') }}</label>
                                <input type="date" id="start_date_input" name="start_date" x-model="startDate" class="input-field w-full" :class="{'border-danger-500 focus:ring-danger-500': isDateInvalid}">
                            </div>
                            <div>
                                <label for="end_date_input" class="block text-xs text-secondary-500 mb-1">{{ __('ui.end_date') }}</label>
                                <input type="date" id="end_date_input" name="end_date" x-model="endDate" class="input-field w-full" :class="{'border-danger-500 focus:ring-danger-500': isDateInvalid}">
                                <template x-if="isDateInvalid">
                                    <p class="text-[10px] text-danger-600 mt-1">Tanggal akhir tidak boleh lebih awal dari mulai.</p>
                                </template>
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
                                        style="display: none;">
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
                        <div class="flex items-center space-x-6">
                            <span class="text-sm font-medium text-secondary-700">{{ __('ui.format_label') }}</span>
                                <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="export_format" value="pdf" checked id="format_pdf" class="text-primary-600 focus:ring-primary-500 h-4 w-4 border-gray-300">
                                <span class="ml-2 text-sm text-secondary-700 font-medium">{{ __('ui.pdf_document') }}</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer">
                                <input type="radio" name="export_format" value="excel" id="format_excel" class="text-success-600 focus:ring-success-500 h-4 w-4 border-gray-300">
                                <span class="ml-2 text-sm text-secondary-700 font-medium">{{ __('ui.excel_document') }}</span>
                            </label>
                        </div>
                        <button type="submit" 
                            :disabled="loading || isDateInvalid"
                            class="btn btn-primary px-8 py-3 text-base flex items-center justify-center gap-2 shadow-lg shadow-primary-200 disabled:opacity-50 disabled:cursor-not-allowed">
                            
                            <!-- State: Normal -->
                            <svg x-show="!loading" style="display: inline-block;" class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            <span x-show="!loading" style="display: inline;" class="whitespace-nowrap font-bold">{{ __('ui.download_report') }}</span>
                            
                            <!-- State: Loading -->
                            <svg x-show="loading" style="display: none;" class="animate-spin h-5 w-5 text-white flex-shrink-0" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                            </svg>
                            <span x-show="loading" style="display: none;">Memproses...</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('reportManager', () => ({
                reportType: 'inventory_list', 
                period: 'this_month',
                startDate: '',
                endDate: '',
                loading: false,
                
                get isDateInvalid() {
                    if (this.period === 'custom' && this.startDate && this.endDate) {
                        return new Date(this.startDate) > new Date(this.endDate);
                    }
                    return false;
                },

                async downloadReport(e) {
                    if (this.isDateInvalid) {
                        e.preventDefault();
                        return;
                    }

                    const format = document.querySelector('input[name=export_format]:checked').value;
                    
                    if (format === 'excel') {
                        this.loading = true;
                        setTimeout(() => this.loading = false, 3000);
                        return;
                    }

                    e.preventDefault();
                    this.loading = true;

                    if (window.showToast) {
                        window.showToast('info', 'Laporan sedang diproses. Mohon tunggu...');
                    }

                    try {
                        const formData = new FormData(e.target);
                        const params = new URLSearchParams(formData);
                        
                        const response = await fetch(`${e.target.action}?${params.toString()}`, {
                            headers: {
                                'Accept': 'application/json, application/pdf',
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        const contentType = response.headers.get('content-type');

                        if (response.ok && contentType && contentType.includes('application/pdf')) {
                            const blob = await response.blob();
                            const url = window.URL.createObjectURL(blob);
                            const a = document.createElement('a');
                            a.href = url;
                            
                            const disposition = response.headers.get('content-disposition');
                            let filename = 'laporan.pdf';
                            if (disposition && disposition.indexOf('attachment') !== -1) {
                                const filenameRegex = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/;
                                const matches = filenameRegex.exec(disposition);
                                if (matches != null && matches[1]) { 
                                    filename = matches[1].replace(/['"]/g, '');
                                }
                            }
                            
                            a.download = filename;
                            document.body.appendChild(a);
                            a.click();
                            window.URL.revokeObjectURL(url);
                            
                            if (window.showToast) {
                                window.showToast('success', 'Laporan berhasil diunduh.');
                            }
                        } else if (response.ok && contentType && contentType.includes('application/json')) {
                            const data = await response.json();
                            if (data.success) {
                                if (window.showToast && data.message) {
                                    window.showToast('info', data.message);
                                }
                            } else {
                                window.showToast('error', data.message || 'Gagal mengirim permintaan.');
                            }
                        } else {
                            throw new Error('Respons tidak dikenali atau server error');
                        }
                    } catch (error) {
                        console.error('Error:', error);
                        if (window.showToast) {
                            window.showToast('error', 'Terjadi kesalahan sistem saat mengunduh.');
                        }
                    } finally {
                        this.loading = false;
                    }
                }
            }));
        });
    </script>
    @endpush
</x-app-layout>
