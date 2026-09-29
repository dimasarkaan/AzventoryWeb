<x-app-layout>
    <div class="py-6" x-data="{ isFiltering: false }" @filter-done.window="isFiltering = false">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->

            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.inventory_management') }}
                    </h1>
                    <p class="mt-1 text-sm text-secondary-500">{{ __('ui.inventory_management_desc') }}</p>
                </div>
                <div class="flex items-center gap-2">
                     <!-- Legend Popover -->
                    <div x-data="{ showLegend: false }" class="relative z-30">
                        <button @click="showLegend = !showLegend" class="btn btn-secondary flex items-center justify-center p-2.5" title="{{ __('ui.legend_title') }}">
                            <x-icon.info class="w-5 h-5 text-secondary-600" />
                        </button>

                        <div x-show="showLegend" 
                             @click.away="showLegend = false"
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="opacity-0 translate-y-2"
                             x-transition:enter-end="opacity-100 translate-y-0"
                             x-transition:leave="transition ease-in duration-150"
                             x-transition:leave-start="opacity-100 translate-y-0"
                             x-transition:leave-end="opacity-0 translate-y-2"
                             class="absolute left-0 sm:left-auto sm:right-0 mt-2 w-64 bg-white rounded-xl shadow-lg border border-secondary-200 p-4 z-50 text-left"
                             x-cloak>
                            <div class="flex items-center justify-between mb-3 border-b border-secondary-100 pb-2">
                                <h3 class="font-bold text-sm text-secondary-900">{{ __('ui.legend_title') }}</h3>
                                <button @click="showLegend = false" class="text-secondary-400 hover:text-secondary-600">
                                    <x-icon.close class="w-4 h-4" />
                                </button>
                            </div>
                            
                            <!-- Tipe Barang -->
                            <div class="mb-4">
                                <span class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider block mb-2">{{ __('ui.legend_type') }}</span>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-1 h-6 rounded-full bg-blue-600"></div>
                                        <span class="text-xs text-secondary-700 font-medium">{{ __('ui.legend_asset') }}</span>
                                    </div>
                                    <div class="flex items-center gap-2">
                                        <div class="w-1 h-6 rounded-full bg-green-600"></div>
                                        <span class="text-xs text-secondary-700 font-medium">{{ __('ui.legend_sale') }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status Dot -->
                            <div>
                                <span class="text-[10px] font-bold text-secondary-400 uppercase tracking-wider block mb-2">{{ __('ui.legend_status') }}</span>
                                <div class="space-y-2">
                                    <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-success-500 border border-white ring-1 ring-secondary-100"></div>
                                        <span class="text-xs text-secondary-700 font-medium">{{ __('ui.legend_active') }}</span>
                                    </div>
                                     <div class="flex items-center gap-2">
                                        <div class="w-2 h-2 rounded-full bg-danger-500 border border-white ring-1 ring-secondary-100"></div>
                                        <span class="text-xs text-secondary-700 font-medium">{{ __('ui.legend_damaged') }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @if(auth()->user()->role === \App\Enums\UserRole::SUPERADMIN)
                     <!-- Trash Toggle Button -->
                     <a href="{{ request('trash') ? route('inventory.index') : route('inventory.index', ['trash' => 'true']) }}" 
                        onclick="clearBulkSelection()"
                        class="btn flex items-center justify-center p-2.5 {{ request('trash') ? 'btn-danger' : 'btn-secondary' }}" 
                        title="{{ request('trash') ? __('ui.exit_trash') : __('ui.view_trash') }}">
                        @if(request('trash'))
                            <!-- Icon: Arrow Left / Back -->
                            <x-icon.back class="w-5 h-5" />
                        @else
                            <!-- Icon: Trash -->
                            <x-icon.trash class="w-5 h-5 text-secondary-600" />
                        @endif
                    </a>
                    @endif
                    
                    @if(!request('trash'))
                    @can('create', App\Models\Sparepart::class)
                    <a href="{{ route('inventory.create') }}" class="btn btn-primary flex items-center gap-2">
                        <x-icon.plus class="w-5 h-5" />
                        {{ __('ui.add_inventory') }}
                    </a>
                    @endcan
                    @endif
                </div>
            </div>

            @if(auth()->user()->role !== \App\Enums\UserRole::OPERATOR)
            <!-- Floating Bulk Action Bar -->
            <div id="bulk-action-bar" 
                 data-bulk-print-route="{{ route('inventory.qr.bulk-print') }}"
                 data-bulk-destroy-route="{{ route('inventory.bulk-destroy') }}"
                 class="fixed bottom-4 sm:bottom-6 left-1/2 transform -translate-x-1/2 bg-white/95 backdrop-blur-md rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.12)] border border-secondary-200 p-2 sm:p-3 flex items-center justify-center gap-3 sm:gap-4 z-50 transition-all duration-300 translate-y-24 opacity-0 w-auto max-w-[95vw]">
                
                <!-- Left Side: Selection Count & Clear -->
                <div class="flex items-center gap-3 pl-2 sm:pl-3 border-r border-secondary-200 pr-3 sm:pr-4">
                    <button type="button" onclick="clearBulkSelection()" class="flex items-center justify-center w-8 h-8 rounded-full bg-secondary-100 text-secondary-500 hover:text-danger-600 hover:bg-danger-50 transition-colors" title="{{ __('ui.clear_selection') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                    <div class="flex items-baseline gap-1.5">
                        <span class="font-bold text-lg text-primary-600" id="selected-count">0</span>
                        <span class="text-xs sm:text-sm text-secondary-500 font-medium">{{ __('ui.selected') }}</span>
                    </div>
                </div>
                
                <!-- Right Side: Actions -->
                <div class="flex items-center gap-2">
                    @if(request('trash'))
                        @if(auth()->user()->role === \App\Enums\UserRole::SUPERADMIN)
                        <form id="bulk-restore-form" action="{{ route('inventory.bulk-restore') }}" method="POST" novalidate class="m-0">
                            @csrf
                            <div id="bulk-restore-inputs"></div>
                            <button type="button" onclick="submitInventoryBulkRestore()" class="btn btn-success border-0 bg-success-50 hover:bg-success-500 text-success-600 hover:text-white flex items-center justify-center gap-1.5 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl transition-all">
                                <x-icon.restore class="w-4 h-4" />
                                <span class="font-semibold text-xs sm:text-sm whitespace-nowrap hidden sm:inline">{{ __('ui.restore') }}</span>
                            </button>
                        </form>
                        @endif

                        @if(auth()->user()->role === \App\Enums\UserRole::SUPERADMIN)
                        <form id="bulk-delete-form" action="{{ route('inventory.bulk-force-delete') }}" method="POST" novalidate class="m-0">
                            @csrf
                            @method('DELETE')
                            <div id="bulk-delete-inputs"></div>
                            <button type="button" onclick="submitInventoryBulkDelete()" class="btn border-0 bg-rose-600 hover:bg-rose-800 focus:ring-rose-500 text-white flex items-center justify-center gap-1.5 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl shadow-[0_4px_12px_rgb(225,29,72,0.3)] hover:shadow-lg transition-all">
                                <x-icon.trash class="w-4 h-4" />
                                <span class="font-semibold text-xs sm:text-sm whitespace-nowrap">{{ __('ui.force_delete') }}</span>
                            </button>
                        </form>
                        @endif
                    @else
                        {{-- Normal Mode Bulk Actions --}}
                        <div class="flex items-center gap-2">
                            <button type="button" onclick="submitInventoryBulkPrint()" class="btn btn-white border border-secondary-200 bg-white hover:bg-secondary-50 text-secondary-700 flex items-center justify-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl transition-all shadow-sm">
                                <svg class="w-4 h-4 sm:w-5 sm:h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path></svg>
                                <span class="font-semibold text-xs sm:text-sm whitespace-nowrap hidden sm:inline">{{ __('ui.print_label') }}</span>
                            </button>

                            @if(auth()->user()->role === \App\Enums\UserRole::SUPERADMIN)
                            <button type="button" onclick="submitInventoryBulkDestroy()" class="btn btn-danger border-0 text-white flex items-center justify-center gap-2 px-3 sm:px-4 py-2 sm:py-2.5 rounded-xl shadow-[0_4px_12px_rgb(239,68,68,0.3)] transition-all">
                                <x-icon.trash class="w-4 h-4 sm:w-5 sm:h-5" />
                                <span class="font-semibold text-xs sm:text-sm whitespace-nowrap">{{ __('ui.bulk_delete') }}</span>
                            </button>
                            @endif
                        </div>
                    @endif
                </div>
            </div>
            @endif

            @if(request('trash'))
                    <!-- Trash Mode Indicator -->
                    <div class="mb-4 relative">
                        <div class="rounded-lg bg-danger-50 p-4 border border-danger-100 flex flex-col sm:flex-row items-center justify-between gap-4">
                             <div class="flex items-center gap-3">
                                <div class="flex-shrink-0">
                                    <x-icon.warning class="h-5 w-5 text-danger-400" />
                                </div>
                                <div>
                                    <h3 class="text-sm font-medium text-danger-800">{{ __('ui.trash_mode') }}</h3>
                                    <div class="text-sm text-danger-700 mt-1">
                                        {{ __('ui.trash_mode_desc') }}
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
            @endif

            <!-- Filters & Search -->
            <div class="mb-4 card p-4 overflow-visible" x-data="{ showFilters: false }">
                    <form id="inventory-filter-form" method="GET" action="{{ route('inventory.index') }}" @submit="isFiltering = true" novalidate>
                    <input type="hidden" name="trash" value="{{ request('trash') }}">
                    <input type="hidden" name="filter" value="{{ request('filter') }}">

                    <!-- Navigation Tabs -->
                    @if(!request('trash'))
                    <div class="mb-5 border-b border-secondary-200">
                        <nav class="-mb-px flex space-x-6 overflow-x-auto" aria-label="Tabs">
                            <a href="{{ route('inventory.index', array_merge(request()->except(['filter', 'page']), ['filter' => null])) }}" 
                               class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200 {{ request('filter') != 'problematic' ? 'border-primary-500 text-primary-600' : 'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300' }}">
                                {{ __('ui.all_inventory') }}
                            </a>
                            @if(in_array(auth()->user()->role, [\App\Enums\UserRole::SUPERADMIN, \App\Enums\UserRole::ADMIN]))
                                <a href="{{ route('inventory.index', array_merge(request()->except(['filter', 'page']), ['filter' => 'problematic'])) }}" 
                                   class="whitespace-nowrap py-3 px-1 border-b-2 font-medium text-sm transition-colors duration-200 flex items-center gap-2 {{ request('filter') == 'problematic' ? 'border-danger-500 text-danger-600' : 'border-transparent text-secondary-500 hover:text-secondary-700 hover:border-secondary-300' }}">
                                    {{ __('ui.problematic_assets') }}
                                    @if(request('filter') == 'problematic')
                                        <span class="bg-danger-100 text-danger-600 py-0.5 px-2 rounded-full text-xs font-bold">{{ $spareparts->total() }}</span>
                                    @endif
                                </a>
                            @endif
                        </nav>
                    </div>
                    @endif

                    <!-- Top: Search Bar & Filter Toggle -->
                    <div class="mb-4 flex gap-2">
                        <div class="relative w-full" 
                             x-data="{ searchQuery: @js(request('search', '')) }"
                             @keydown.window="
                                if ($event.key === '/' && $event.target.tagName !== 'INPUT' && $event.target.tagName !== 'TEXTAREA') {
                                    $event.preventDefault();
                                    $refs.searchInput.focus();
                                }
                             "
                        >
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-icon.search x-show="!isFiltering" class="w-5 h-5 text-secondary-400" />
                                <svg x-show="isFiltering" x-cloak class="animate-spin w-5 h-5 text-primary-500" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            </div>
                            <input type="text" x-ref="searchInput" name="search" x-model="searchQuery" 
                                   @keydown.escape="$refs.searchInput.blur()"
                                   data-testid="search-inventory"
                                   class="input-field pl-10 pr-20 w-full" 
                                   placeholder="{{ __('ui.search_inventory_placeholder') }}" 
                                   onchange="this.form.submit()" maxlength="255">
                            
                            <!-- Search Shortcut Hint (Hidden on mobile or when typing) -->
                            <div x-show="searchQuery.length === 0" class="absolute inset-y-0 right-0 pr-3 hidden sm:flex items-center pointer-events-none">
                                <kbd class="px-2 py-1 text-[10px] font-semibold text-secondary-500 bg-secondary-100 border border-secondary-200 rounded-md shadow-sm">/</kbd>
                            </div>

                            <button type="button" x-show="searchQuery.length > 0" @click="searchQuery = ''; isFiltering = true; $nextTick(() => { document.getElementById('inventory-filter-form').submit(); })" class="absolute inset-y-0 right-0 pr-3 flex items-center text-secondary-400 hover:text-danger-500 transition-colors cursor-pointer" title="{{ __('ui.clear_search') }}" x-cloak>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                        <button type="button" @click="showFilters = !showFilters" class="btn btn-secondary md:hidden flex items-center justify-center w-12 flex-shrink-0" title="{{ __('ui.show_filter') }}">
                            <x-icon.filter class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Active Filter Pills -->
                    <div id="active-filters-container">
                        @include('inventory.partials.active-filters')
                    </div>

                    <!-- Backdrop for Mobile Drawer -->
                    <template x-teleport="body">
                        <div x-show="showFilters" 
                             @click="showFilters = false"
                             x-transition.opacity.duration.300ms
                             class="fixed inset-0 bg-secondary-900/50 backdrop-blur-sm z-[90] md:hidden" 
                             x-cloak></div>
                    </template>

                    <!-- Bottom: Filters & Sort -->
                    <div class="fixed md:relative inset-y-0 right-0 z-[100] md:z-[60] w-[85vw] max-w-sm md:max-w-none md:w-full bg-white md:bg-transparent shadow-2xl md:shadow-none p-6 md:p-0 overflow-y-auto md:overflow-visible transition-transform duration-300 flex flex-col md:flex-row md:flex-wrap gap-4 md:gap-3 h-full md:h-auto"
                         :class="showFilters ? 'translate-x-0' : 'translate-x-full md:translate-x-0'">
                        
                        <!-- Mobile Drawer Header -->
                        <div class="flex items-center justify-between mb-4 md:hidden">
                            <h2 class="text-lg font-bold text-secondary-900">{{ __('ui.filter_data') }}</h2>
                            <button type="button" @click="showFilters = false" class="text-secondary-400 hover:text-secondary-600 bg-secondary-50 p-2 rounded-full focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <!-- Filter Selects -->
                        <div class="flex flex-col md:flex-row md:flex-wrap gap-4 md:gap-3 flex-1">
                            @php
                                $categoryOptions = collect($categoryOptions)->mapWithKeys(fn($item) => [$item => $item])->toArray();
                                $brandOptions = collect($brandOptions)->mapWithKeys(fn($item) => [$item => $item])->toArray();
                                $locationOptions = collect($locationOptions)->mapWithKeys(fn($item) => [$item => $item])->toArray();
                                $colorOptions = collect($colors)->mapWithKeys(fn($item) => [$item => $item])->toArray();
                                $conditionOptions = collect($conditions)->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            @endphp

                            <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                                @php
                                    $typeOptions = [
                                        'sale' => __('ui.type_sale'),
                                        'asset' => __('ui.type_asset'),
                                    ];
                                @endphp
                                <label for="type-filter" class="sr-only">{{ __('ui.all_types') }}</label>
                                <x-select name="type" id="type-filter" :options="$typeOptions" :selected="request('type')" placeholder="{{ __('ui.all_types') }}" :submitOnChange="true" width="w-full" />
                            </div>
                            <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                                <label for="category-filter" class="sr-only">{{ __('ui.all_categories') }}</label>
                                <x-select name="category" id="category-filter" :options="$categoryOptions" :selected="request('category')" placeholder="{{ __('ui.all_categories') }}" :submitOnChange="true" width="w-full" />
                            </div>
                            <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                                <label for="brand-filter" class="sr-only">{{ __('ui.all_brands') }}</label>
                                <x-select name="brand" id="brand-filter" :options="$brandOptions" :selected="request('brand')" placeholder="{{ __('ui.all_brands') }}" :submitOnChange="true" width="w-full" />
                            </div>
                            <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                                <label for="location-filter" class="sr-only">{{ __('ui.all_locations') }}</label>
                                <x-select name="location" id="location-filter" :options="$locationOptions" :selected="request('location')" placeholder="{{ __('ui.all_locations') }}" :submitOnChange="true" width="w-full" />
                            </div>
                            <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                                <label for="color-filter" class="sr-only">{{ __('ui.all_colors') }}</label>
                                <x-select name="color" id="color-filter" :options="$colorOptions" :selected="request('color')" placeholder="{{ __('ui.all_colors') }}" :submitOnChange="true" width="w-full" />
                            </div>
                            <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                                <label for="condition-filter" class="sr-only">{{ __('ui.all_conditions') }}</label>
                                <x-select name="condition" id="condition-filter" :options="$conditionOptions" :selected="request('condition')" placeholder="{{ __('ui.all_conditions') }}" :submitOnChange="true" width="w-full" />
                            </div>
                            <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                                @php
                                    $sortOptions = [
                                        'newest' => __('ui.sort_newest'),
                                        'oldest' => __('ui.sort_oldest'),
                                        'name_asc' => __('ui.sort_name_asc'),
                                        'name_desc' => __('ui.sort_name_desc'),
                                        'stock_asc' => __('ui.sort_stock_asc'),
                                        'stock_desc' => __('ui.sort_stock_desc'),
                                        'price_asc' => __('ui.sort_price_asc'),
                                        'price_desc' => __('ui.sort_price_desc'),
                                    ];
                                @endphp
                                <label for="sort-filter" class="sr-only">{{ __('ui.sort') }}</label>
                                <x-select name="sort" id="sort-filter" :options="$sortOptions" :selected="request('sort', 'newest')" placeholder="{{ __('ui.sort') }}" :submitOnChange="true" width="w-full" />
                            </div>
                        </div>
                        
                        <!-- Reset Button & Mobile Apply -->
                        <div class="mt-auto pt-6 md:pt-0 border-t border-secondary-100 md:border-0 flex flex-col md:flex-row md:items-end gap-3 md:flex-shrink-0">
                            <!-- Mobile Apply Button (Only visible on mobile) -->
                            <button type="button" @click="showFilters = false" class="btn btn-primary w-full justify-center py-3 md:hidden">
                                Terapkan Filter
                            </button>
                            
                            <a href="{{ route('inventory.index', request()->only(['trash', 'filter'])) }}" id="reset-filters" class="btn btn-secondary flex items-center justify-center p-3 md:p-2.5 md:h-[42px] md:w-[42px] w-full" title="{{ __('ui.reset_filter') }}">
                                <x-icon.restore class="h-5 w-5 mr-2 md:mr-0" />
                                <span class="md:hidden font-medium">Reset Filter</span>
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <div :class="{ 'opacity-50 pointer-events-none': isFiltering }" class="transition-opacity duration-200">
                <!-- Mobile Card View -->
                @include('inventory.partials.mobile-list')

                <!-- Desktop Table View -->
                @include('inventory.partials.desktop-table')
            </div>

            <!-- Quick View Drawer Component -->
            @include('inventory.partials.quick-view-drawer')

    @push('scripts')
    @vite('resources/js/pages/superadmin/inventory/index.js')
    @endpush

        </div>
    </div>
</x-app-layout>

