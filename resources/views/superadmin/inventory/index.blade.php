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
                    <a href="{{ route('superadmin.inventory.create') }}" class="btn btn-primary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        {{ __('Tambah Inventaris') }}
                    </a>
                </div>

                </div>

            <!-- Filters & Search -->
            <div class="mb-4 card p-4">
                <form method="GET" action="{{ route('superadmin.inventory.index') }}">
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
                        
                        <a href="{{ route('superadmin.inventory.index') }}" class="btn btn-secondary flex items-center justify-center gap-2" title="Reset Filter">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                            </svg>
                        </a>
                    </div>
                </form>
            </div>

            <!-- Desktop Table View (Hidden on Mobile) -->
            <div class="hidden md:block card overflow-hidden">
                <div> <!-- Removed overflow-x-auto -->
                    <table class="table-modern w-full table-fixed">
                        <thead>
                            <tr>
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
                                    <td class="px-4 py-3">
                                        <div class="flex items-center gap-3">
                                            <!-- Status Indicator -->
                                            <div class="w-1.5 h-1.5 rounded-full flex-shrink-0 {{ $sparepart->status === 'aktif' ? 'bg-success-500' : 'bg-danger-500' }}" title="{{ ucfirst($sparepart->status) }}"></div>
                                            
                                            <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                                @if($sparepart->image)
                                                    <img src="{{ asset('storage/' . $sparepart->image) }}" alt="" class="h-full w-full object-cover rounded-lg">
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
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center text-secondary-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="h-16 w-16 bg-secondary-100 text-secondary-400 rounded-full flex items-center justify-center mb-4">
                                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                            </div>
                                            <p class="text-lg font-medium text-secondary-900">Belum ada inventaris</p>
                                            <p class="text-sm mt-1">Mulai dengan menambahkan sparepart baru.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                        
                        <!-- Skeleton Body (Hidden by default) -->
                        <tbody id="skeleton-body" class="hidden divide-y divide-secondary-100">
                            @for ($i = 0; $i < 5; $i++)
                                <tr>
                                    <td class="px-4 py-4">
                                        <div class="flex items-center">
                                            <div class="h-10 w-10 bg-secondary-200 rounded-md animate-pulse"></div>
                                            <div class="ml-4 space-y-1">
                                                <div class="h-4 w-32 bg-secondary-200 rounded animate-pulse"></div>
                                                <div class="h-3 w-20 bg-secondary-200 rounded animate-pulse"></div>
                                            </div>
                                        </div>
                                    </td>
                                    @for ($j = 0; $j < 6; $j++)
                                        <td class="px-4 py-4 text-center">
                                            <div class="h-4 w-16 bg-secondary-200 rounded animate-pulse mx-auto"></div>
                                        </td>
                                    @endfor
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
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form[action="{{ route('superadmin.inventory.index') }}"]');
            const realBody = document.querySelector('tbody:not(#skeleton-body)');
            const skeletonBody = document.getElementById('skeleton-body');

            if (form) {
                // Handle Filter/Search Changes
                const inputs = form.querySelectorAll('input, select');
                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        showSkeleton();
                    });
                });

                form.addEventListener('submit', function() {
                    showSkeleton();
                });
            }

            // Handle Pagination Clicks
            const paginationLinks = document.querySelectorAll('.pagination a, .page-link'); // Adjust selector based on Laravel pagination output
            paginationLinks.forEach(link => {
                link.addEventListener('click', function(e) {
                   showSkeleton();
                });
            });

            function showSkeleton() {
                if (realBody && skeletonBody) {
                    realBody.classList.add('hidden');
                    skeletonBody.classList.remove('hidden');
                }
            }
        });
    </script>
    @endpush
            <!-- Mobile Card View (Visible on Mobile) -->
            <div class="md:hidden space-y-4">
                @forelse ($spareparts as $sparepart)
                    <div class="card p-4 flex flex-col gap-3">
                        <div class="flex items-start justify-between gap-3">
                            <div class="flex items-center gap-3 min-w-0">
                                <!-- Status Indicator (Dot) -->
                                <div class="w-2 h-2 rounded-full flex-shrink-0 {{ $sparepart->status === 'aktif' ? 'bg-success-500' : 'bg-danger-500' }}" title="{{ ucfirst($sparepart->status) }}"></div>
                                
                                <div class="h-12 w-12 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                    @if($sparepart->image)
                                        <img src="{{ asset('storage/' . $sparepart->image) }}" alt="" class="h-full w-full object-cover rounded-lg">
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
