<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->

            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.inventory_management') }}
                    </h2>
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
                             style="display: none;">
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

            @if(request('trash'))
                    <!-- Trash Mode Indicator & Bulk Actions -->
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

                        <!-- Floating Bulk Action Bar -->
                        <!-- Floating Bulk Action Bar (Styled like Users) -->
                        <div id="bulk-action-bar" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-xl shadow-xl border border-secondary-200 px-6 py-3 flex items-center gap-6 z-50 transition-all duration-300 translate-y-24 opacity-0">
                            <div class="flex items-center gap-2 border-r border-secondary-200 pr-6">
                                <span class="font-bold text-lg text-primary-600" id="selected-count">0</span>
                                <span class="text-sm text-secondary-500 font-medium">{{ __('ui.selected') }}</span>
                            </div>
                            
                            <div class="flex items-center gap-3">
                                <form id="bulk-restore-form" action="{{ route('inventory.bulk-restore') }}" method="POST">
                                    @csrf
                                    <div id="bulk-restore-inputs"></div>
                                    <button type="button" onclick="submitInventoryBulkRestore()" class="btn btn-white text-secondary-700 hover:text-primary-600 flex items-center gap-2 border-0 bg-transparent hover:bg-secondary-50">
                                        <x-icon.restore class="w-5 h-5" />
                                        <span class="font-medium">{{ __('ui.restore') }}</span>
                                    </button>
                                </form>

                                <form id="bulk-delete-form" action="{{ route('inventory.bulk-force-delete') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div id="bulk-delete-inputs"></div>
                                    <button type="button" onclick="submitInventoryBulkDelete()" class="btn btn-danger flex items-center gap-2 px-4 py-2 rounded-lg shadow-sm hover:shadow-md transition-all">
                                        <x-icon.trash class="w-4 h-4" />
                                        <span>{{ __('ui.force_delete') }}</span>
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
            @endif

            <!-- Filters & Search -->
            <div class="mb-4 card p-4 overflow-visible" x-data="{ showFilters: false }">
                    <form id="inventory-filter-form" method="GET" action="{{ route('inventory.index') }}">
                    <input type="hidden" name="trash" value="{{ request('trash') }}">
                    <!-- Top: Search Bar & Filter Toggle -->
                    <div class="mb-4 flex gap-2">
                        <div class="relative w-full">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-icon.search class="w-5 h-5 text-secondary-400" />
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" class="input-field pl-10 w-full" placeholder="{{ __('ui.search_inventory_placeholder') }}" onchange="this.form.submit()">
                        </div>
                        <button type="button" @click="showFilters = !showFilters" class="btn btn-secondary md:hidden flex items-center justify-center w-12 flex-shrink-0" title="{{ __('ui.show_filter') }}">
                            <x-icon.filter class="w-5 h-5" />
                        </button>
                    </div>

                    <!-- Bottom: Filters & Sort -->
                    <div class="flex-col md:flex-row flex-wrap gap-3" :class="showFilters ? 'flex' : 'hidden md:flex'">
                        @php
                            $categoryOptions = $categories->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            $brandOptions = $brands->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            $locationOptions = $locations->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            $colorOptions = $colors->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            $conditionOptions = $conditions->mapWithKeys(fn($item) => [$item => $item])->toArray();
                        @endphp

                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            @php
                                $typeOptions = [
                                    'sale' => 'Barang Dijual (Sale)',
                                    'asset' => 'Inventaris (Asset)',
                                ];
                            @endphp
                            <x-select name="type" :options="$typeOptions" :selected="request('type')" placeholder="{{ __('ui.all_types') }}" :submitOnChange="true" width="w-full" />
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <x-select name="category" :options="$categoryOptions" :selected="request('category')" placeholder="{{ __('ui.all_categories') }}" :submitOnChange="true" width="w-full" />
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <x-select name="brand" :options="$brandOptions" :selected="request('brand')" placeholder="{{ __('ui.all_brands') }}" :submitOnChange="true" width="w-full" />
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <x-select name="location" :options="$locationOptions" :selected="request('location')" placeholder="{{ __('ui.all_locations') }}" :submitOnChange="true" width="w-full" />
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <x-select name="color" :options="$colorOptions" :selected="request('color')" placeholder="{{ __('ui.all_colors') }}" :submitOnChange="true" width="w-full" />
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <x-select name="condition" :options="$conditionOptions" :selected="request('condition')" placeholder="{{ __('ui.all_conditions') }}" :submitOnChange="true" width="w-full" />
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
                            <x-select name="sort" :options="$sortOptions" :selected="request('sort', 'newest')" placeholder="{{ __('ui.sort') }}" :submitOnChange="true" width="w-full" />
                        </div>
                        
                        <div class="flex items-end flex-shrink-0">
                            <a href="{{ route('inventory.index') }}" id="reset-filters" class="btn btn-secondary flex items-center justify-center p-2.5 h-[42px] w-[42px]" title="{{ __('ui.reset_filter') }}">
                                <x-icon.restore class="h-5 w-5" />
                            </a>
                        </div>
                    </div>
                </form>
            </div>

            <!-- Mobile Card View -->
            @include('inventory.partials.mobile-list')

            <!-- Desktop Table View -->
            @include('inventory.partials.desktop-table')

    @push('scripts')
    @vite('resources/js/pages/superadmin/inventory/index.js')
    @endpush

        </div>
    </div>
</x-app-layout>
