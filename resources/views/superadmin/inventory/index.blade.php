<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Flash Messages -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 bg-success-50 border-l-4 border-success-500 p-4 rounded-md shadow-sm relative">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-success-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            <p class="text-sm font-medium text-success-800">{{ session('success') }}</p>
                        </div>
                        <button @click="show = false" class="text-success-500 hover:text-success-700 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif

            @if(session('error'))
                <div x-data="{ show: true }" x-show="show" x-transition class="mb-4 bg-danger-50 border-l-4 border-danger-500 p-4 rounded-md shadow-sm relative">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center">
                            <svg class="h-6 w-6 text-danger-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <p class="text-sm font-medium text-danger-800">{{ session('error') }}</p>
                        </div>
                        <button @click="show = false" class="text-danger-500 hover:text-danger-700 focus:outline-none">
                            <span class="sr-only">Close</span>
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>
                </div>
            @endif
            <!-- Header -->
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('Master Inventaris') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">Kelola semua data sparepart, stok, dan lokasi.</p>
                </div>
                <div class="flex gap-3" x-data="{ reportModalOpen: false }" @keydown.escape.window="reportModalOpen = false">
                    <!-- Export Button -->
                    <button @click="reportModalOpen = true" class="btn btn-secondary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        Export Data
                    </button>

                    <a href="{{ route('superadmin.inventory.create') }}" class="btn btn-primary flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                        {{ __('Tambah Item') }}
                    </a>

                    <!-- Report Modal -->
                    <div x-show="reportModalOpen" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
                        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                            <div x-show="reportModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100" x-transition:leave-end="opacity-0" class="fixed inset-0 transition-opacity" aria-hidden="true">
                                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
                            </div>
                            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                            <div x-show="reportModalOpen" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100" x-transition:leave="ease-in duration-200" x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100" x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <div class="sm:flex sm:items-start">
                                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-primary-100 sm:mx-0 sm:h-10 sm:w-10">
                                            <svg class="h-6 w-6 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        </div>
                                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                                Download Laporan
                                            </h3>
                                            <div class="mt-2 text-sm text-gray-500">
                                                Pilih jenis laporan dan periode waktu yang diinginkan.
                                            </div>
                                            <form id="reportForm" action="{{ route('superadmin.reports.download') }}" method="GET" class="mt-4 space-y-4" x-data="{ type: 'inventory_list', period: 'all' }">
                                                <!-- Report Type -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Jenis Laporan</label>
                                                    <select name="report_type" x-model="type" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                                        <option value="inventory_list">Data Inventaris (Stok Saat Ini)</option>
                                                        <option value="stock_mutation">Riwayat Stok (Masuk/Keluar)</option>
                                                    </select>
                                                </div>

                                                <!-- Period -->
                                                <div x-show="type === 'stock_mutation'">
                                                    <label class="block text-sm font-medium text-gray-700">Periode</label>
                                                    <select name="period" x-model="period" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                                        <option value="all">Semua Riwayat</option>
                                                        <option value="this_month">Bulan Ini</option>
                                                        <option value="last_month">Bulan Lalu</option>
                                                        <option value="this_year">Tahun Ini</option>
                                                        <option value="last_year">Tahun Lalu</option>
                                                        <option value="custom">Kustom (Pilih Tanggal)</option>
                                                    </select>
                                                </div>

                                                <!-- Location Filter -->
                                                <div>
                                                    <label class="block text-sm font-medium text-gray-700">Lokasi</label>
                                                    <select name="location" class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-primary-500 focus:border-primary-500 sm:text-sm rounded-md">
                                                        <option value="all">Semua Lokasi</option>
                                                        @foreach($locations as $loc)
                                                            <option value="{{ $loc }}">{{ $loc }}</option>
                                                        @endforeach
                                                    </select>
                                                </div>

                                                <!-- Custom Date Range -->
                                                <div x-show="type === 'stock_mutation' && period === 'custom'" class="grid grid-cols-2 gap-4">
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Dari Tanggal</label>
                                                        <input type="date" name="start_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                                    </div>
                                                    <div>
                                                        <label class="block text-sm font-medium text-gray-700">Sampai Tanggal</label>
                                                        <input type="date" name="end_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-primary-500 focus:border-primary-500 sm:text-sm">
                                                    </div>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" form="reportForm" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary-600 text-base font-medium text-white hover:bg-primary-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:ml-3 sm:w-auto sm:text-sm">
                                        Download PDF
                                    </button>
                                    <button @click="reportModalOpen = false" type="button" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Batal
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
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
                         <select name="category" class="input-field w-full sm:w-auto flex-1" onchange="this.form.submit()">
                            <option value="Semua Kategori">Semua Kategori</option>
                            @foreach($categories as $cat)
                                <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ $cat }}</option>
                            @endforeach
                        </select>
                        <select name="brand" class="input-field w-full sm:w-auto flex-1" onchange="this.form.submit()">
                            <option value="Semua Merk">Semua Merk</option>
                            @foreach($brands as $brand)
                                <option value="{{ $brand }}" {{ request('brand') == $brand ? 'selected' : '' }}>{{ $brand }}</option>
                            @endforeach
                        </select>
                         <select name="location" class="input-field w-full sm:w-auto flex-1" onchange="this.form.submit()">
                            <option value="Semua Lokasi">Semua Lokasi</option>
                            @foreach($locations as $loc)
                                <option value="{{ $loc }}" {{ request('location') == $loc ? 'selected' : '' }}>{{ $loc }}</option>
                            @endforeach
                        </select>
                        <select name="color" class="input-field w-full sm:w-auto flex-1" onchange="this.form.submit()">
                            <option value="Semua Warna">Semua Warna</option>
                            @foreach($colors as $col)
                                <option value="{{ $col }}" {{ request('color') == $col ? 'selected' : '' }}>{{ $col }}</option>
                            @endforeach
                        </select>
                        <select name="sort" class="input-field w-full sm:w-auto flex-1" onchange="this.form.submit()">
                            <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>Terbaru</option>
                            <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>Terlama</option>
                            <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>Nama (A-Z)</option>
                            <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>Nama (Z-A)</option>
                            <option value="stock_asc" {{ request('sort') == 'stock_asc' ? 'selected' : '' }}>Stok (Sedikit)</option>
                            <option value="stock_desc" {{ request('sort') == 'stock_desc' ? 'selected' : '' }}>Stok (Banyak)</option>
                            <option value="price_asc" {{ request('sort') == 'price_asc' ? 'selected' : '' }}>Harga (Terendah)</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Harga (Tertinggi)</option>
                        </select>
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
                                            <form action="{{ route('superadmin.inventory.destroy', $sparepart) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-ghost p-2 text-secondary-500 hover:text-danger-600" title="Hapus">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-12 text-center text-secondary-500">
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
                    </table>
                </div>
                <!-- Pagination -->
                <div class="px-4 py-3 border-t border-secondary-100">
                    {{ $spareparts->links() }}
                </div>
            </div>

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
                             <form action="{{ route('superadmin.inventory.destroy', $sparepart) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus data ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger text-xs p-2 h-auto">Hapus</button>
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
