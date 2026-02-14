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
            <div class="mb-4 card p-4 overflow-visible">
                    <form id="inventory-filter-form" method="GET" action="{{ route('superadmin.inventory.index') }}">
                    <input type="hidden" name="trash" value="{{ request('trash') }}">
                    <!-- Top: Search Bar -->
                    <div class="mb-4">
                        <div class="relative w-full">
                             <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                            </div>
                            <input type="text" name="search" value="{{ request('search') }}" class="input-field pl-10 w-full" placeholder="{{ __('ui.search_inventory_placeholder') }}" onchange="this.form.submit()">
                        </div>
                    </div>

                    <!-- Bottom: Filters & Sort -->
                    <div class="flex flex-col md:flex-row flex-wrap gap-3">
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
                                <a href="{{ route('superadmin.inventory.edit', $sparepart) }}" class="btn btn-sm btn-warning flex-1 justify-center">
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
                        <div class="h-16 w-16 bg-secondary-100 text-secondary-400 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <h3 class="text-lg font-medium text-secondary-900">{{ __('ui.inventory_empty') }}</h3>
                        <p class="text-secondary-500 text-sm mt-1">{{ __('ui.inventory_empty_desc') }}</p>
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
                                <tr class="group hover:bg-secondary-50 transition-colors">
                                    @if(request('trash'))
                                        <td class="px-4 py-3 text-center">
                                            <input type="checkbox" name="ids[]" value="{{ $sparepart->id }}" class="bulk-checkbox rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                        </td>
                                    @endif
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <!-- Status Indicator logic (active/inactive) can remain or be removed if redundant. Keeping for now as dot. -->
                                            <x-status-badge :status="$sparepart->status" class="w-1.5 h-1.5" />
                                            
                                            <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                                @if($sparepart->image)
                                                    <img src="{{ asset('storage/' . $sparepart->image) }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover rounded-lg">
                                                @else
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                @endif
                                            </div>
                                            
                                            <!-- Vertical Line Indicator -->
                                            <div class="hidden sm:block w-1 self-stretch rounded-full mr-3 shrink-0 {{ $sparepart->type === 'sale' ? 'bg-green-600' : 'bg-blue-600' }}" title="{{ $sparepart->type === 'sale' ? 'Barang Dijual' : 'Aset Kantor' }}"></div>

                                            <div class="min-w-0">
                                                <div class="flex items-center gap-1.5 mb-0.5">
                                                    <a href="{{ route('superadmin.inventory.show', $sparepart) }}" class="font-medium text-secondary-900 group-hover:text-primary-600 transition-colors block truncate" title="{{ $sparepart->name }}">
                                                        {{ $sparepart->name }}
                                                    </a>
                                                </div>
                                                <span class="text-xs text-secondary-500 font-mono truncate block">{{ $sparepart->part_number }}</span>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm text-secondary-700 font-medium truncate block">
                                            {{ $sparepart->brand ?? '-' }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary-100 text-secondary-800 truncate">
                                            {{ $sparepart->category }}
                                        </span>
                                    </td>
                                    <!-- Condition Column -->
                                    <td class="px-4 py-3 text-center">
                                        <div class="flex flex-col items-center justify-center gap-0.5">
                                            @php
                                                $condition = $sparepart->condition ?? '-';
                                                $age = $sparepart->age === 'Pernah Dipakai (Bekas)' ? 'Bekas' : ($sparepart->age ?? '-');
                                                
                                                $conditionColor = match(strtolower($condition)) {
                                                    'baik' => 'text-success-600 bg-success-50 border-success-100',
                                                    'rusak' => 'text-danger-600 bg-danger-50 border-danger-100',
                                                    'hilang' => 'text-secondary-600 bg-secondary-100 border-secondary-200',
                                                    default => 'text-secondary-600 bg-secondary-50 border-secondary-100'
                                                };
                                            @endphp
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border {{ $conditionColor }}">
                                                {{ ucfirst($condition) }}
                                            </span>
                                            <span class="text-[10px] text-secondary-400 font-medium">
                                                {{ $age }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3 text-center">
                                        <span class="text-sm text-secondary-700 truncate block">{{ $sparepart->color ?? '-' }}</span>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-1.5 text-secondary-600 text-sm truncate">
                                            <svg class="w-4 h-4 text-secondary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                            <span class="truncate">{{ $sparepart->location }}</span>
                                        </div>
                                    </td>
                                    <td class="px-4 py-3">
                                        <div class="flex items-baseline justify-center gap-1">
                                            @php
                                                $isLowStock = $sparepart->stock <= $sparepart->minimum_stock && !in_array(strtolower($sparepart->condition), ['rusak', 'hilang']);
                                            @endphp
                                            
                                            @if($isLowStock)
                                                <div class="relative group self-center" title="{{ __('ui.low_stock') }}">
                                                    <svg class="w-4 h-4 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                </div>
                                            @endif
                                            <span class="text-base font-bold {{ $isLowStock ? 'text-danger-600' : 'text-secondary-900' }}">
                                                {{ $sparepart->stock }}
                                            </span>
                                            <span class="text-xs text-secondary-500">{{ $sparepart->unit ?? 'Pcs' }}</span>
                                        </div>
                                    </td>
                                    <!-- Removed Status Column Data -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            @if(request('trash'))
                                                @can('restore', $sparepart)
                                                <form action="{{ route('superadmin.inventory.restore', $sparepart->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-ghost p-2 text-success-600 hover:text-success-700 bg-success-50 hover:bg-success-100 rounded-lg" title="{{ __('ui.restore') }}" onclick="confirmInventoryRestore(event)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                    </button>
                                                </form>
                                                @endcan
                                                @can('forceDelete', $sparepart)
                                                <form action="{{ route('superadmin.inventory.force-delete', $sparepart->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-ghost p-2 text-danger-600 hover:text-danger-700 bg-danger-50 hover:bg-danger-100 rounded-lg" title="{{ __('ui.force_delete') }}" onclick="confirmInventoryForceDelete(event)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                                @endcan
                                            @else
                                                <a href="{{ route('superadmin.inventory.show', $sparepart) }}" class="btn btn-ghost p-2 text-secondary-600 hover:text-primary-600 hover:bg-secondary-100 rounded-lg" title="{{ __('ui.detail') }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>
                                                @can('update', $sparepart)
                                                <a href="{{ route('superadmin.inventory.edit', $sparepart) }}" class="btn btn-ghost p-2 text-warning-600 hover:text-warning-700 hover:bg-warning-50 rounded-lg" title="{{ __('ui.edit') }}">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                @endcan
                                                @can('delete', $sparepart)
                                                <form action="{{ route('superadmin.inventory.destroy', $sparepart) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-ghost p-2 text-danger-600 hover:text-danger-700 hover:bg-danger-50 rounded-lg" title="{{ __('ui.delete') }}" onclick="confirmDelete(event)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                                @endcan
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request('trash') ? '9' : '8' }}" class="px-6 py-12 text-center text-secondary-500">
                                        <div class="flex flex-col items-center justify-center w-full">
                                            @php
                                                $isFiltered = request('search') || request('category') || request('brand') || request('location') || request('color');
                                            @endphp

                                            <div class="h-16 w-16 bg-secondary-100 text-secondary-400 rounded-full flex items-center justify-center mb-4">
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

                                            <p class="text-lg font-medium text-secondary-900">
                                                @if(request('trash'))
                                                    {{ __('ui.trash_empty') }}
                                                @elseif($isFiltered)
                                                    {{ __('ui.no_results') }}
                                                @else
                                                    {{ __('ui.inventory_empty') }}
                                                @endif
                                            </p>

                                            <p class="text-sm mt-1 max-w-3xl mx-auto leading-relaxed text-center">
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
            <!-- Mobile Card View (Visible on Mobile) -->
            <div class="md:hidden space-y-4">
                @forelse ($spareparts as $sparepart)
                    <div class="card p-4 flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- Status Indicator (Dot) -->
                                <x-status-badge :status="$sparepart->status" class="w-2 h-2" />
                                
                                <div class="h-12 w-12 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                    @if($sparepart->image)
                                        <img src="{{ asset('storage/' . $sparepart->image) }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover rounded-lg">
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>

                                <!-- Vertical Line Indicator -->
                                <div class="w-1 self-stretch rounded-full mr-3 shrink-0 {{ $sparepart->type === 'sale' ? 'bg-green-600' : 'bg-blue-600' }}"></div>

                                <div class="min-w-0">
                                    <div class="flex items-center gap-1.5 mb-0.5">
                                        <a href="{{ route('superadmin.inventory.show', $sparepart) }}" class="font-bold text-secondary-900 line-clamp-1 block leading-tight">
                                            {{ $sparepart->name }}
                                        </a>
                                    </div>
                                    <span class="text-xs text-secondary-500 font-mono block mt-0.5">{{ $sparepart->part_number }}</span>
                                </div>
                            </div>
                            <!-- Actions Dropdown or Menu could go here, but for now we have buttons below -->
                        </div>
                        
                        <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-xs border-t border-b border-secondary-50 py-2.5">
                            <!-- Row 1 -->
                            <div>
                                <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">{{ __('ui.category') }}</span>
                                <span class="font-medium text-secondary-700 block truncate">{{ $sparepart->category }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">{{ __('ui.condition') }}</span>
                                @php
                                    $condition = $sparepart->condition ?? '-';
                                    $age = $sparepart->age === 'Pernah Dipakai (Bekas)' ? 'Bekas' : ($sparepart->age ?? '-');
                                    
                                    $conditionColor = match(strtolower($condition)) {
                                        'baik' => 'text-success-800 bg-success-50',
                                        'rusak' => 'text-danger-800 bg-danger-50',
                                        'hilang' => 'text-secondary-800 bg-secondary-100',
                                        default => 'text-secondary-800 bg-secondary-50'
                                    };
                                @endphp
                                <div class="flex flex-col items-end gap-0.5">
                                    <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $conditionColor }}">
                                        {{ ucfirst($condition) }}
                                    </span>
                                    <span class="text-[10px] text-secondary-500">{{ $age }}</span>
                                </div>
                            </div>
                            <div>
                                <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">{{ __('ui.brand') }}</span>
                                <span class="font-medium text-secondary-700 block truncate">{{ $sparepart->brand ?? '-' }}</span>
                            </div>

                            <!-- Row 2: Lokasi Full Width -->
                            <div class="col-span-2">
                                <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">{{ __('ui.location') }}</span>
                                <div class="font-medium text-secondary-700 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-secondary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="truncate">{{ $sparepart->location }}</span>
                                </div>
                            </div>

                            <!-- Row 3 -->
                            <div>
                                <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">{{ __('ui.color') }}</span>
                                <span class="font-medium text-secondary-700 block truncate">{{ $sparepart->color ?? '-' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">{{ __('ui.stock') }}</span>
                                <div class="flex items-center justify-end gap-1.5">
                                    @php
                                        $isLowStockMobile = $sparepart->stock <= $sparepart->minimum_stock && !in_array(strtolower($sparepart->condition), ['rusak', 'hilang']);
                                    @endphp
                                    
                                    @if($isLowStockMobile)
                                         <svg class="w-3 h-3 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    @endif
                                    <span class="font-bold {{ $isLowStockMobile ? 'text-danger-600' : 'text-secondary-900' }}">{{ $sparepart->stock }}</span>
                                    <span class="text-[10px] text-secondary-500">{{ $sparepart->unit ?? 'Pcs' }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1">
                             <a href="{{ route('superadmin.inventory.show', $sparepart) }}" class="btn btn-ghost text-xs p-2 h-auto text-secondary-600 font-medium">{{ __('ui.detail') }}</a>
                             <a href="{{ route('superadmin.inventory.edit', $sparepart) }}" class="btn btn-secondary text-xs p-2 h-auto">{{ __('ui.edit') }}</a>
                             <form action="{{ route('superadmin.inventory.destroy', $sparepart) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger text-xs p-2 h-auto" onclick="confirmDelete(event)">{{ __('ui.delete') }}</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="card p-8 text-center text-secondary-500">
                        <div class="w-16 h-16 bg-secondary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <p class="text-lg font-medium text-secondary-900">{{ __('ui.inventory_empty') }}</p>
                        <p class="text-sm mt-1">{{ __('ui.inventory_empty_desc') }}</p>
                    </div>
                @endforelse

                <!-- Pagination -->
                <div class="mt-4 md:hidden">
                    {{ $spareparts->links() }}
                </div>
            </div>


        </div>
    </div>
</x-app-layout>
