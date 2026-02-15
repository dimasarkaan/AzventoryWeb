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
                            <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
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
                     <!-- Trash Toggle Button -->
                     <a href="{{ request('trash') ? route('superadmin.inventory.index') : route('superadmin.inventory.index', ['trash' => 'true']) }}" 
                        class="btn flex items-center justify-center p-2.5 {{ request('trash') ? 'btn-danger' : 'btn-secondary' }}" 
                        title="{{ request('trash') ? __('ui.exit_trash') : __('ui.view_trash') }}">
                        @if(request('trash'))
                            <!-- Icon: Arrow Left / Back -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        @else
                            <!-- Icon: Trash -->
                            <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        @endif
                    </a>
                    
                    @if(!request('trash'))
                    @can('create', App\Models\Sparepart::class)
                    <a href="{{ route('superadmin.inventory.create') }}" class="btn btn-primary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
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
                                    <svg class="h-5 w-5 text-danger-400" viewBox="0 0 20 20" fill="currentColor">
                                        <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd" />
                                    </svg>
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
                            <div id="bulk-action-bar" style="display: none;" class="fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-xl shadow-2xl border border-secondary-200 p-3 z-50 flex items-center gap-4 animate-in slide-in-from-bottom-5 fade-in duration-300 ring-1 ring-black/5">
                            <span class="text-sm font-medium text-secondary-700 whitespace-nowrap pl-2">
                                <span id="selected-count" class="font-bold text-primary-600">0</span> {{ __('ui.selected') }}
                            </span>
                            <div class="h-6 w-px bg-secondary-200"></div>
                            <div class="flex gap-2">
                                <form id="bulk-restore-form" action="{{ route('superadmin.inventory.bulk-restore') }}" method="POST">
                                    @csrf
                                    <div id="bulk-restore-inputs"></div>
                                    <button type="button" onclick="submitInventoryBulkRestore()" class="btn btn-sm btn-success flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        {{ __('ui.restore') }}
                                    </button>
                                </form>
                                <form id="bulk-delete-form" action="{{ route('superadmin.inventory.bulk-force-delete') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div id="bulk-delete-inputs"></div>
                                    <button type="button" onclick="submitInventoryBulkDelete()" class="btn btn-sm btn-danger flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        {{ __('ui.force_delete') }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
            @endif

            <!-- Filters & Search -->
            <div class="mb-4 card p-4 overflow-visible" x-data="{ showFilters: false }">
                    <form id="inventory-filter-form" method="GET" action="{{ route('superadmin.inventory.index') }}">
                    <input type="hidden" name="trash" value="{{ request('trash') }}">
                    <!-- Top: Search Bar & Filter Toggle -->
                    <div class="mb-4 flex gap-2">
                        <div class="relative w-full">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" class="input-field pl-10 w-full" placeholder="{{ __('ui.search_inventory_placeholder') }}" onchange="this.form.submit()">
                        </div>
                        <button type="button" @click="showFilters = !showFilters" class="btn btn-secondary md:hidden flex items-center justify-center w-12 flex-shrink-0" title="{{ __('ui.show_filter') }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        </button>
                    </div>

                    <!-- Bottom: Filters & Sort -->
                    <div class="flex-col md:flex-row flex-wrap gap-3" :class="showFilters ? 'flex' : 'hidden md:flex'">
                        @php
                            $categoryOptions = $categories->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            $brandOptions = $brands->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            $locationOptions = $locations->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            $colorOptions = $colors->mapWithKeys(fn($item) => [$item => $item])->toArray();
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
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <x-select name="sort" :options="$sortOptions" :selected="request('sort', 'newest')" placeholder="{{ __('ui.sort') }}" :submitOnChange="true" width="w-full" />
                        </div>
                        
                        <a href="{{ route('superadmin.inventory.index') }}" id="reset-filters" class="btn btn-secondary flex items-center justify-center gap-2" title="{{ __('ui.reset_filter') }}">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-4">
                @forelse ($spareparts as $sparepart)
                    <div class="card p-4">
                        <!-- Header: Image, Name, Status -->
                        <div class="flex items-start gap-4 mb-4">
                            <!-- Image -->
                            <div class="h-16 w-16 rounded-lg bg-secondary-100 flex items-center justify-center flex-shrink-0 text-secondary-400 overflow-hidden border border-secondary-200">
                                @if($sparepart->image)
                                    <img src="{{ asset('storage/' . $sparepart->image) }}" alt="{{ $sparepart->name }}" loading="lazy" class="h-full w-full object-cover">
                                @else
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            
                            <!-- Title & Badge -->
                            <div class="flex-1 min-w-0">
                                <div class="flex items-start justify-between gap-2">
                                    <div>
                                        <div class="flex items-center gap-2">
                                             <h3 class="text-base font-bold text-secondary-900 line-clamp-1">
                                                <a href="{{ route('superadmin.inventory.show', $sparepart) }}">
                                                    {{ $sparepart->name }}
                                                </a>
                                            </h3>
                                            
                                        </div>
                                        <p class="text-xs text-secondary-500 font-mono mt-0.5">{{ $sparepart->part_number }}</p>
                                    </div>
                                    <x-status-badge :status="$sparepart->status" class="flex-shrink-0" />
                                </div>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm mb-4 border-t border-b border-secondary-100 py-3">
                            <!-- Brand & Category -->
                            <div class="col-span-2 flex items-center justify-between">
                                <span class="text-secondary-500">{{ __('ui.brand') }} / {{ __('ui.category') }}</span>
                                <span class="font-medium text-secondary-900 text-right truncate pl-2">
                                    {{ $sparepart->brand ?? '-' }} <span class="text-secondary-300 mx-1">|</span> {{ $sparepart->category }}
                                </span>
                            </div>

                            <!-- Condition -->
                            <div class="flex flex-col">
                                <span class="text-xs text-secondary-500 mb-1">{{ __('ui.condition') }}</span>
                                @php
                                    $condition = $sparepart->condition ?? '-';
                                    $conditionColor = match(strtolower($condition)) {
                                        'baik' => 'text-success-700 bg-success-50 border-success-200',
                                        'rusak' => 'text-danger-700 bg-danger-50 border-danger-200',
                                        'hilang' => 'text-secondary-700 bg-secondary-100 border-secondary-200',
                                        default => 'text-secondary-700 bg-secondary-50 border-secondary-200'
                                    };
                                @endphp
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border w-fit {{ $conditionColor }}">
                                    {{ ucfirst($condition) }}
                                </span>
                            </div>

                            <!-- Stock & Location -->
                            <div class="flex flex-col items-end text-right">
                                <span class="text-xs text-secondary-500 mb-1">{{ __('ui.stock') }} @ {{ $sparepart->location }}</span>
                                <div class="flex items-center gap-1.5">
                                    @php
                                        $isLowStock = $sparepart->stock <= $sparepart->minimum_stock && !in_array(strtolower($sparepart->condition), ['rusak', 'hilang']);
                                    @endphp
                                    <span class="text-lg font-bold {{ $isLowStock ? 'text-danger-600' : 'text-secondary-900' }}">
                                        {{ $sparepart->stock }}
                                    </span>
                                    <span class="text-xs text-secondary-500">{{ $sparepart->unit ?? 'Pcs' }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center justify-end gap-2">
                            @if(request('trash'))
                                @can('restore', $sparepart)
                                <form action="{{ route('superadmin.inventory.restore', $sparepart->id) }}" method="POST" class="inline-block w-full sm:w-auto">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-sm btn-success w-full justify-center flex items-center gap-1" onclick="confirmInventoryRestore(event)">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        {{ __('ui.restore') }}
                                    </button>
                                </form>
                                @endcan
                            @else
                                <a href="{{ route('superadmin.inventory.show', $sparepart) }}" class="btn btn-sm btn-secondary flex-1 justify-center">
                                    {{ __('ui.detail') }}
                                </a>
                                @can('update', $sparepart)
                                <a href="{{ route('superadmin.inventory.edit', $sparepart) }}" class="btn btn-sm btn-secondary flex-1 justify-center border-secondary-300 shadow-sm">
                                    {{ __('ui.edit') }}
                                </a>
                                @endcan
                                @can('delete', $sparepart)
                                <form action="{{ route('superadmin.inventory.destroy', $sparepart) }}" method="POST" class="inline-block flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-danger w-full justify-center" onclick="confirmDelete(event)">
                                        {{ __('ui.delete') }}
                                    </button>
                                </form>
                                @endcan
                            @endif
                        </div>
                    </div>
                @empty
                    <!-- Mobile Empty State -->
                    <div class="card p-8 flex flex-col items-center justify-center text-center">
                        @php
                            $isFiltered = request('search') || request('category') || request('brand') || request('location') || request('color');
                        @endphp

                        <div class="h-16 w-16 bg-secondary-100 text-secondary-400 rounded-full flex items-center justify-center mb-4 shadow-sm border border-secondary-200">
                             @if(request('trash'))
                                {{-- Trash Icon --}}
                                <svg class="w-8 h-8 text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            @elseif($isFiltered)
                                {{-- Search/Filter Icon --}}
                                <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            @else
                                {{-- Default Box Icon --}}
                                <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            @endif
                        </div>
                        
                        <h3 class="text-lg font-medium text-secondary-900">
                            @if(request('trash'))
                                {{ __('ui.trash_empty') }}
                            @elseif($isFiltered)
                                {{ __('ui.no_results') }}
                            @else
                                {{ __('ui.inventory_empty') }}
                            @endif
                        </h3>
                        
                        <p class="text-secondary-500 text-sm mt-1 max-w-xs mx-auto">
                            @if(request('trash'))
                                {{ __('ui.trash_empty_desc') }}
                            @elseif($isFiltered)
                                {{ __('ui.no_results_desc') }}
                            @else
                                {{ __('ui.inventory_empty_desc') }}
                            @endif
                        </p>
                    </div>
                @endforelse
                
                <!-- Mobile Pagination -->
                <div class="mt-4">
                    {{ $spareparts->links() }}
                </div>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-modern w-full table-fixed">
                        <thead>
                            <tr>
                                @if(request('trash'))
                                    <th class="w-[5%] px-4 py-3 text-center">
                                        <input type="checkbox" id="select-all" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                    </th>
                                @endif
                                <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[20%]">{{ __('ui.name') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.brand') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.category') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.condition') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[10%]">{{ __('ui.color') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">{{ __('ui.location') }}</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[10%]">{{ __('ui.stock') }}</th>
                                <!-- Removed Status Header -->
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[14%]">{{ __('ui.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($spareparts as $sparepart)
                                <x-inventory.table-row :sparepart="$sparepart" :trash="request('trash')" />
                            @empty
                                <tr>
                                    <td colspan="{{ request('trash') ? '9' : '8' }}" class="px-6 py-12 text-center text-secondary-500">
                                        <div class="flex flex-col items-center justify-center w-full">
                                            @php
                                                $isFiltered = request('search') || request('category') || request('brand') || request('location') || request('color');
                                            @endphp

                                            <div class="h-16 w-16 bg-secondary-100 text-secondary-400 rounded-full flex items-center justify-center mb-4 shadow-sm border border-secondary-200">
                                                @if(request('trash'))
                                                    {{-- Trash Icon --}}
                                                    <svg class="w-8 h-8 text-danger-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                @elseif($isFiltered)
                                                    {{-- Search/Filter Icon --}}
                                                    <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                                @else
                                                    {{-- Default Box Icon --}}
                                                    <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                                @endif
                                            </div>

                                            <p class="text-lg font-bold text-secondary-900 tracking-tight">
                                                @if(request('trash'))
                                                    {{ __('ui.trash_empty') }}
                                                @elseif($isFiltered)
                                                    {{ __('ui.no_results') }}
                                                @else
                                                    {{ __('ui.inventory_empty') }}
                                                @endif
                                            </p>

                                            <p class="text-sm mt-1 max-w-sm mx-auto leading-relaxed text-center text-secondary-500">
                                                @if(request('trash'))
                                                    {{ __('ui.trash_empty_desc') }}
                                                @elseif($isFiltered)
                                                    {{ __('ui.no_results_desc') }}
                                                @else
                                                    {{ __('ui.inventory_empty_desc') }}
                                                @endif
                                            </p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        
                        <!-- Skeleton Body (Hidden by default) -->
                        <!-- High-Quality Skeleton Body -->
                        <tbody id="skeleton-body" class="hidden divide-y divide-secondary-100 bg-white">
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    @if(request('trash'))
                                        <td class="px-4 py-4 text-center">
                                            <div class="h-4 w-4 bg-secondary-100 rounded animate-pulse mx-auto"></div>
                                        </td>
                                    @endif
                                    <!-- Name & Image Column -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-1.5 h-1.5 rounded-full bg-secondary-200 animate-pulse flex-shrink-0"></div> <!-- Status Dot -->
                                            <div class="h-10 w-10 bg-secondary-100 rounded-lg animate-pulse flex-shrink-0"></div> <!-- Image -->
                                            <div class="space-y-2 flex-1 min-w-0">
                                                <div class="h-4 w-32 bg-secondary-100 rounded animate-pulse"></div> <!-- Name -->
                                                <div class="h-3 w-20 bg-secondary-50 rounded animate-pulse"></div>  <!-- Part Number -->
                                            </div>
                                        </div>
                                    </td>
                                    <!-- Brand -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="h-4 w-20 bg-secondary-50 rounded animate-pulse mx-auto"></div>
                                    </td>
                                    <!-- Category -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="h-5 w-24 bg-secondary-100 rounded-full animate-pulse mx-auto"></div>
                                    </td>
                                    <!-- Color -->
                                    <td class="px-4 py-4 text-center">
                                         <div class="h-4 w-16 bg-secondary-50 rounded animate-pulse mx-auto"></div>
                                    </td>
                                    <!-- Location -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-center justify-center gap-1.5">
                                             <div class="h-4 w-4 bg-secondary-100 rounded-full animate-pulse"></div>
                                             <div class="h-4 w-16 bg-secondary-50 rounded animate-pulse"></div>
                                        </div>
                                    </td>
                                    <!-- Stock -->
                                    <td class="px-4 py-4 text-center">
                                        <div class="flex items-baseline justify-center gap-1">
                                            <div class="h-5 w-8 bg-secondary-100 rounded animate-pulse"></div>
                                            <div class="h-3 w-6 bg-secondary-50 rounded animate-pulse"></div>
                                        </div>
                                    </td>
                                    <!-- Actions -->
                                    <td class="px-4 py-4">
                                        <div class="flex items-center justify-center gap-2">
                                            <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                                            <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                                            <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                                        </div>
                                    </td>
                                </tr>
                            @endfor
                        </tbody>
                    </table>
                </div>

                <!-- Pagination -->
                @if($spareparts->hasPages())
                    <div class="bg-secondary-50 px-4 py-3 border-t border-secondary-200 sm:px-6">
                        {{ $spareparts->appends(request()->query())->links() }}
                    </div>
                @endif
            </div>

    @push('scripts')
    @vite('resources/js/pages/superadmin/inventory/index.js')
    @endpush

        </div>
    </div>
</x-app-layout>
