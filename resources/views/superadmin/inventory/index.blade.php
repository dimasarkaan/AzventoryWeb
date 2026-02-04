<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->

            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('Manajemen Inventaris') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">Kelola semua data sparepart, stok, dan lokasi.</p>
                </div>
                <div class="flex gap-2">
                     <!-- Trash Toggle Button -->
                     <a href="{{ request('trash') ? route('superadmin.inventory.index') : route('superadmin.inventory.index', ['trash' => 'true']) }}" 
                        class="btn flex items-center justify-center p-2.5 {{ request('trash') ? 'btn-danger' : 'btn-secondary' }}" 
                        title="{{ request('trash') ? 'Keluar dari Tong Sampah' : 'Lihat Tong Sampah' }}">
                        @if(request('trash'))
                            <!-- Icon: Arrow Left / Back -->
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                        @else
                            <!-- Icon: Trash -->
                            <svg class="w-5 h-5 text-secondary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        @endif
                    </a>
                    
                    @if(!request('trash'))
                    <a href="{{ route('superadmin.inventory.create') }}" class="btn btn-primary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        {{ __('Tambah Inventaris') }}
                    </a>
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
                                    <h3 class="text-sm font-medium text-danger-800">Mode Tong Sampah</h3>
                                    <div class="text-sm text-danger-700 mt-1">
                                        Pilih item untuk dipulihkan atau dihapus selamanya.
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Floating Bulk Action Bar -->
                        <div id="bulk-action-bar" class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 bg-white rounded-xl shadow-2xl border border-secondary-200 p-3 z-50 flex items-center gap-4 animate-in slide-in-from-bottom-5 fade-in duration-300 ring-1 ring-black/5">
                            <span class="text-sm font-medium text-secondary-700 whitespace-nowrap pl-2">
                                <span id="selected-count" class="font-bold text-primary-600">0</span> Dipilih
                            </span>
                            <div class="h-6 w-px bg-secondary-200"></div>
                            <div class="flex gap-2">
                                <form id="bulk-restore-form" action="{{ route('superadmin.inventory.bulk-restore') }}" method="POST">
                                    @csrf
                                    <div id="bulk-restore-inputs"></div>
                                    <button type="button" onclick="submitInventoryBulkRestore()" class="btn btn-sm btn-success flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                        Pulihkan
                                    </button>
                                </form>
                                <form id="bulk-delete-form" action="{{ route('superadmin.inventory.bulk-force-delete') }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <div id="bulk-delete-inputs"></div>
                                    <button type="button" onclick="submitInventoryBulkDelete()" class="btn btn-sm btn-danger flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        Hapus Permanen
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
                            <input type="text" name="search" value="{{ request('search') }}" class="input-field pl-10 w-full" placeholder="Cari sparepart, kode part, merk..." onchange="this.form.submit()">
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
                            <x-select name="category" :options="$categoryOptions" :selected="request('category')" placeholder="Semua Kategori" :submitOnChange="true" width="w-full" />
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <x-select name="brand" :options="$brandOptions" :selected="request('brand')" placeholder="Semua Merk" :submitOnChange="true" width="w-full" />
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <x-select name="location" :options="$locationOptions" :selected="request('location')" placeholder="Semua Lokasi" :submitOnChange="true" width="w-full" />
                        </div>
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <x-select name="color" :options="$colorOptions" :selected="request('color')" placeholder="Semua Warna" :submitOnChange="true" width="w-full" />
                        </div>
                        @php
                            $sortOptions = [
                                'newest' => 'Terbaru',
                                'oldest' => 'Terlama',
                                'name_asc' => 'Nama (A-Z)',
                                'name_desc' => 'Nama (Z-A)',
                                'stock_asc' => 'Stok (Sedikit)',
                                'stock_desc' => 'Stok (Banyak)',
                                'price_asc' => 'Harga (Terendah)',
                                'price_desc' => 'Harga (Tertinggi)',
                            ];
                        @endphp
                        <div class="flex-1 w-full sm:w-auto min-w-[150px]">
                            <x-select name="sort" :options="$sortOptions" :selected="request('sort', 'newest')" placeholder="Urutkan" :submitOnChange="true" width="w-full" />
                        </div>
                        
                        <a href="{{ route('superadmin.inventory.index') }}" id="reset-filters" class="btn btn-secondary flex items-center justify-center gap-2" title="Reset Filter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </a>
                    </div>
                </form>
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
                                <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[20%]">Nama Sparepart</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[14%]">Merk</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[14%]">Kategori</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">Warna</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[14%]">Lokasi</th>
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]">Stok</th>
                                <!-- Removed Status Header -->
                                <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[14%]">Aksi</th>
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
                                            <!-- Status Indicator -->
                                            <x-status-badge :status="$sparepart->status" class="w-1.5 h-1.5" />
                                            
                                            <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                                @if($sparepart->image)
                                                    <img src="{{ asset('storage/' . $sparepart->image) }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover rounded-lg">
                                                @else
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                @endif
                                            </div>
                                            <div class="min-w-0">
                                                <a href="{{ route('superadmin.inventory.show', $sparepart) }}" class="font-medium text-secondary-900 group-hover:text-primary-600 transition-colors block truncate" title="{{ $sparepart->name }}">
                                                    {{ $sparepart->name }}
                                                </a>
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
                                            <span class="text-base font-bold {{ $sparepart->stock <= $sparepart->minimum_stock ? 'text-danger-600' : 'text-secondary-900' }}">
                                                {{ $sparepart->stock }}
                                            </span>
                                            <span class="text-xs text-secondary-500">{{ $sparepart->unit ?? 'Pcs' }}</span>
                                            
                                            @if($sparepart->stock <= $sparepart->minimum_stock)
                                                <div class="relative group self-center" title="Stok Menipis">
                                                    <svg class="w-4 h-4 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                                </div>
                                            @endif
                                        </div>
                                    </td>
                                    <!-- Removed Status Column Data -->
                                    <td class="px-4 py-3">
                                        <div class="flex items-center justify-center gap-2">
                                            @if(request('trash'))
                                                <form action="{{ route('superadmin.inventory.restore', $sparepart->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('PATCH')
                                                    <button type="submit" class="btn btn-ghost p-2 text-success-600 hover:text-success-700 bg-success-50 hover:bg-success-100 rounded-lg" title="Pulihkan" onclick="confirmInventoryRestore(event)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                                    </button>
                                                </form>
                                                <form action="{{ route('superadmin.inventory.force-delete', $sparepart->id) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-ghost p-2 text-danger-600 hover:text-danger-700 bg-danger-50 hover:bg-danger-100 rounded-lg" title="Hapus Permanen" onclick="confirmInventoryForceDelete(event)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @else
                                                <a href="{{ route('superadmin.inventory.show', $sparepart) }}" class="btn btn-ghost p-2 text-secondary-500 hover:text-primary-600" title="Detail">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                                </a>
                                                <a href="{{ route('superadmin.inventory.edit', $sparepart) }}" class="btn btn-ghost p-2 text-secondary-500 hover:text-warning-600" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </a>
                                                <form action="{{ route('superadmin.inventory.destroy', $sparepart) }}" method="POST" class="inline-block">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="btn btn-ghost p-2 text-secondary-500 hover:text-danger-600" title="Hapus" onclick="confirmDelete(event)">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="{{ request('trash') ? '8' : '7' }}" class="px-6 py-12 text-center text-secondary-500">
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
                                                    Tong sampah kosong
                                                @elseif($isFiltered)
                                                    Tidak ditemukan hasil
                                                @else
                                                    Belum ada inventaris
                                                @endif
                                            </p>

                                            <p class="text-sm mt-1 max-w-3xl mx-auto leading-relaxed text-center">
                                                @if(request('trash'))
                                                    Tidak ada item yang dihapus sementara.
                                                @elseif($isFiltered)
                                                    Pencarian Anda tidak cocok dengan data manapun. Coba gunakan kata kunci lain atau reset filter.
                                                @else
                                                    Data inventaris masih kosong. Mulai dengan menambahkan sparepart baru.
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
                                <div class="min-w-0">
                                    <a href="{{ route('superadmin.inventory.show', $sparepart) }}" class="font-bold text-secondary-900 line-clamp-1 block leading-tight">
                                        {{ $sparepart->name }}
                                    </a>
                                    <span class="text-xs text-secondary-500 font-mono block mt-0.5">{{ $sparepart->part_number }}</span>
                                </div>
                            </div>
                            <!-- Actions Dropdown or Menu could go here, but for now we have buttons below -->
                        </div>
                        
                        <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-xs border-t border-b border-secondary-50 py-2.5">
                            <!-- Row 1 -->
                            <div>
                                <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">Kategori</span>
                                <span class="font-medium text-secondary-700 block truncate">{{ $sparepart->category }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">Merk</span>
                                <span class="font-medium text-secondary-700 block truncate">{{ $sparepart->brand ?? '-' }}</span>
                            </div>

                            <!-- Row 2: Lokasi Full Width -->
                            <div class="col-span-2">
                                <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">Lokasi</span>
                                <div class="font-medium text-secondary-700 flex items-center gap-1">
                                    <svg class="w-3 h-3 text-secondary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                                    <span class="truncate">{{ $sparepart->location }}</span>
                                </div>
                            </div>

                            <!-- Row 3 -->
                            <div>
                                <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">Warna</span>
                                <span class="font-medium text-secondary-700 block truncate">{{ $sparepart->color ?? '-' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">Stok</span>
                                <div class="flex items-center justify-end gap-1.5">
                                    <span class="font-bold {{ $sparepart->stock <= $sparepart->minimum_stock ? 'text-danger-600' : 'text-secondary-900' }}">{{ $sparepart->stock }}</span>
                                    <span class="text-[10px] text-secondary-500">{{ $sparepart->unit ?? 'Pcs' }}</span>
                                    @if($sparepart->stock <= $sparepart->minimum_stock)
                                         <svg class="w-3 h-3 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="flex items-center justify-end gap-2 pt-1">
                             <a href="{{ route('superadmin.inventory.show', $sparepart) }}" class="btn btn-ghost text-xs p-2 h-auto text-secondary-600 font-medium">Detail</a>
                             <a href="{{ route('superadmin.inventory.edit', $sparepart) }}" class="btn btn-secondary text-xs p-2 h-auto">Edit</a>
                             <form action="{{ route('superadmin.inventory.destroy', $sparepart) }}" method="POST" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger text-xs p-2 h-auto" onclick="confirmDelete(event)">Hapus</button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="card p-8 text-center text-secondary-500">
                        <div class="w-16 h-16 bg-secondary-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                        </div>
                        <p class="text-lg font-medium text-secondary-900">Belum ada inventaris</p>
                        <p class="text-sm mt-1">Mulai dengan menambahkan sparepart baru.</p>
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
