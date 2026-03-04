<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="operatorDashboardData()">
            <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">Halo, {{ Auth::user()->name }}!</h1>
                    <p class="mt-1 text-sm text-secondary-500">Ringkasan aktivitas dan status inventaris Anda saat ini.</p>
                </div>
            </div>

                <!-- QR Scanner Modal Placeholder -->
                <div id="qr-reader-modal" class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-secondary-900/60 backdrop-blur-sm">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                        <div class="p-4 border-b border-secondary-100 flex items-center justify-between">
                            <h3 class="font-bold text-secondary-900">Scan Barcode / QR Barang</h3>
                            <button id="close-scan-btn" class="p-2 hover:bg-secondary-100 rounded-lg text-secondary-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        <div class="p-4 flex flex-col items-center">
                            <!-- Container for QR Reader -->
                            <div id="qr-reader" class="rounded-xl overflow-hidden border-2 border-dashed border-secondary-200 bg-black w-full flex justify-center items-center relative" style="min-height: 250px;"></div>
                            
                            <!-- Custom Controls -->
                            <div class="mt-4 flex flex-wrap justify-center gap-3 w-full">
                                <button id="switch-camera-btn" class="hidden flex-1 sm:flex-none items-center justify-center gap-2 px-4 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <span>Putar Kamera</span>
                                </button>
                                
                                <label for="qr-input-file" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <span>Upload Galeri</span>
                                </label>
                                <input type="file" id="qr-input-file" class="hidden" accept="image/*">
                            </div>

                            <div id="qr-reader-results" class="mt-4 p-3 bg-primary-50 text-primary-700 text-sm rounded-lg hidden w-full text-center">
                                Mencari data barang...
                            </div>
                        </div>
                    </div>
                </div>

            <!-- Dashboard Stats (Colored Gradient Style) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Active Borrowings -->
                <div class="bg-gradient-to-br from-primary-500 to-indigo-600 rounded-xl shadow-md p-6 flex items-center justify-between text-white transform transition-transform hover:-translate-y-1">
                    <div>
                        <p class="text-sm font-medium text-primary-100 mb-1">Total Pinjaman Aktif</p>
                        <h3 class="text-4xl font-extrabold">{{ $activeBorrowingsCount ?? 0 }}</h3>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white">
                        <x-icon.borrow-user class="w-8 h-8" stroke-width="2.5" />
                    </div>
                </div>

                <!-- Pending Requests -->
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl shadow-md p-6 flex items-center justify-between text-white transform transition-transform hover:-translate-y-1">
                    <div>
                        <p class="text-sm font-medium text-amber-100 mb-1">Pengajuan Stok Menunggu</p>
                        <h3 class="text-4xl font-extrabold">{{ $pendingRequestsCount ?? 0 }}</h3>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white">
                        <x-icon.low-stock class="w-8 h-8" stroke-width="2.5" />
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Borrowing Trend Chart -->
                <div class="card flex flex-col lg:col-span-2">
                    <div class="card-header border-b border-secondary-100 p-4 sm:p-5 flex flex-wrap items-center justify-between gap-3">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-indigo-50 flex items-center justify-center text-indigo-600 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z"></path></svg>
                            </div>
                            <h2 class="font-bold text-secondary-900 truncate">Tren Peminjaman</h2>
                        </div>
                        <div x-data="{ open: false }" class="relative z-20">
                            <form method="GET" action="{{ route('dashboard.operator') }}" x-ref="trendForm">
                                <input type="hidden" name="trend_period" id="trend_period_input" value="{{ $trendPeriod }}">
                                
                                <button type="button" @click="open = !open" @click.away="open = false" class="flex-shrink-0 flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-secondary-700 bg-white border border-secondary-200 rounded-lg hover:bg-secondary-50 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5 text-secondary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="whitespace-nowrap">
                                        @switch($trendPeriod)
                                            @case('7_days') 7 Hari Terakhir @break
                                            @case('30_days') 30 Hari Terakhir @break
                                            @case('1_year') 1 Tahun Terakhir @break
                                            @default 6 Bulan Terakhir
                                        @endswitch
                                    </span>
                                    <svg class="w-3 h-3 text-secondary-400 transition-transform duration-200 flex-shrink-0" :class="{'rotate-180': open}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </button>

                                <div x-show="open" 
                                     x-transition:enter="transition ease-out duration-100" 
                                     x-transition:enter-start="transform opacity-0 scale-95" 
                                     x-transition:enter-end="transform opacity-100 scale-100" 
                                     x-transition:leave="transition ease-in duration-75" 
                                     x-transition:leave-start="transform opacity-100 scale-100" 
                                     x-transition:leave-end="transform opacity-0 scale-95" 
                                     class="absolute left-0 sm:left-auto sm:right-0 origin-top-left sm:origin-top-right mt-1 w-48 bg-white rounded-xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.1)] border border-secondary-100 py-1.5 z-[100] overflow-hidden" 
                                     style="display: none;">
                                    
                                    @php
                                        $periods = [
                                            '7_days' => '7 Hari Terakhir',
                                            '30_days' => '30 Hari Terakhir',
                                            '6_months' => '6 Bulan Terakhir',
                                            '1_year' => '1 Tahun Terakhir',
                                        ];
                                    @endphp

                                    @foreach($periods as $val => $label)
                                        <button type="button" 
                                                @click="document.getElementById('trend_period_input').value = '{{ $val }}'; $refs.trendForm.submit();" 
                                                class="w-full text-left px-4 py-2.5 text-xs flex items-center justify-between transition-colors {{ $trendPeriod === $val ? 'bg-primary-50 text-primary-700 font-bold' : 'text-secondary-700 hover:bg-secondary-50 font-medium' }}">
                                            {{ $label }}
                                            @if($trendPeriod === $val)
                                                <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            @endif
                                        </button>
                                    @endforeach
                                </div>
                            </form>
                        </div>
                    </div>
                    <div class="card-body p-6 flex-1 flex flex-col justify-center min-h-[300px]">
                        <div id="operatorBorrowingChart" class="w-full h-full"></div>
                    </div>
                </div>

                <!-- Stock Request Status Chart -->
                <div class="card flex flex-col">
                    <div class="card-header border-b border-secondary-100 p-5 flex items-center gap-2">
                        <div class="w-8 h-8 rounded-lg bg-orange-50 flex items-center justify-center text-orange-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 3.055A9.001 9.001 0 1020.945 13H11V3.055z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.488 9H15V3.512A9.025 9.025 0 0120.488 9z"></path></svg>
                        </div>
                        <h2 class="font-bold text-secondary-900">Status Pengajuan</h2>
                    </div>
                    <div class="card-body p-6 flex-1 flex items-center justify-center min-h-[300px]">
                        <div id="operatorRequestStatusChart" class="w-full"></div>
                    </div>
                </div>
            </div>

            <!-- Data List Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
                <!-- Active Borrowings List -->
                <div class="card flex flex-col h-full">
                    <div class="card-header border-b border-primary-100 p-5 flex items-center justify-between bg-gradient-to-r from-primary-50 to-white">
                        <div class="flex items-center gap-2">
                             <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center text-primary-600 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                            </div>
                            <h2 class="font-bold text-secondary-900">Barang Saya (Aktif)</h2>
                        </div>
                        <a href="{{ route('profile.inventory') }}" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition-colors bg-primary-50 px-3 py-1.5 rounded-full border border-primary-100 shadow-sm hover:bg-primary-100">Lihat Semua</a>
                    </div>
                    <div class="card-body flex-1 p-0">
                        <template x-if="activeBorrowingsList.length === 0">
                            <div class="p-8 text-center text-secondary-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <p>Belum ada barang yang Anda pinjam saat ini.</p>
                            </div>
                        </template>
                        <template x-if="activeBorrowingsList.length > 0">
                            <div class="contents">
                                <!-- Desktop view -->
                                <div class="hidden md:block overflow-x-auto">
                                    <table class="table-modern w-full">
                                        <thead>
                                            <tr>
                                                <th>Nama Barang</th>
                                                <th>Jumlah</th>
                                                <th>Tanggal Peminjaman</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-secondary-100">
                                            <template x-for="borrowing in activeBorrowingsList" :key="borrowing.id">
                                                <tr class="hover:bg-secondary-50/50 transition-colors cursor-pointer" @click="window.location.href='{{ route('inventory.index') }}/' + borrowing.sparepart_id">
                                                    <td>
                                                        <div class="font-medium text-secondary-900 line-clamp-1" :title="borrowing.sparepart_name" x-text="borrowing.sparepart_name"></div>
                                                    </td>
                                                    <td>
                                                        <div class="font-bold text-secondary-900" x-text="borrowing.remaining_quantity"></div>
                                                    </td>
                                                    <td>
                                                        <div class="text-xs text-secondary-500">
                                                            Tgl Pinjam: <span class="text-secondary-700" x-text="borrowing.borrowed_at_formatted"></span><br>
                                                            Tenggat Kembali: <span class="font-medium" :class="borrowing.is_overdue ? 'text-danger-600' : 'text-secondary-700'" x-text="borrowing.expected_return_at_formatted"></span>
                                                        </div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Mobile view -->
                                <div class="md:hidden divide-y divide-secondary-100">
                                    <template x-for="borrowing in activeBorrowingsList" :key="borrowing.id">
                                        <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" @click="window.location.href='{{ route('inventory.index') }}/' + borrowing.sparepart_id">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="font-bold text-secondary-900 leading-tight pr-4" x-text="borrowing.sparepart_name"></div>
                                                <span class="text-sm font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-full whitespace-nowrap"><span x-text="borrowing.remaining_quantity"></span> Unit</span>
                                            </div>
                                            <div class="text-xs text-secondary-500 flex flex-col gap-1 mt-2">
                                                <div class="flex items-center justify-between">
                                                    <span>Pinjam: <span x-text="borrowing.borrowed_at_formatted"></span></span>
                                                </div>
                                                <div class="flex items-center justify-between">
                                                    <span>Tenggat: <span class="font-semibold" :class="borrowing.is_overdue ? 'text-danger-600' : 'text-secondary-700'" x-text="borrowing.expected_return_at_formatted"></span></span>
                                                </div>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Pending Requests List -->
                <div class="card flex flex-col h-full">
                    <div class="card-header border-b border-warning-100 p-5 flex items-center justify-between bg-gradient-to-r from-warning-50 to-white">
                        <div class="flex items-center gap-2">
                             <div class="w-8 h-8 rounded-lg bg-warning-100 flex items-center justify-center text-warning-600 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h2 class="font-bold text-secondary-900">Daftar Pengajuan Stok</h2>
                        </div>
                    </div>
                    <div class="card-body flex-1 p-0 overflow-y-auto max-h-[350px] custom-scrollbar">
                        <template x-if="pendingRequestsList.length === 0">
                            <div class="p-8 text-center text-secondary-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p>Tidak ada pengajuan stok yang menunggu persetujuan.</p>
                            </div>
                        </template>
                        <template x-if="pendingRequestsList.length > 0">
                            <div class="contents">
                                <!-- Desktop view -->
                                <div class="hidden md:block overflow-x-auto">
                                    <table class="table-modern w-full">
                                        <thead>
                                            <tr>
                                                <th>Nama Barang</th>
                                                <th>Tipe Pengajuan</th>
                                                <th>Jumlah Stok</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-secondary-100">
                                            <template x-for="request in pendingRequestsList" :key="request.id">
                                                <tr class="hover:bg-secondary-50/50 transition-colors cursor-pointer" @click="window.location.href='{{ route('inventory.index') }}/' + request.sparepart_id">
                                                    <td>
                                                        <div class="font-medium text-secondary-900 line-clamp-1" :title="request.sparepart_name" x-text="request.sparepart_name"></div>
                                                        <div class="text-[10px] text-secondary-500" x-text="request.created_at_formatted"></div>
                                                    </td>
                                                    <td>
                                                        <template x-if="request.type === 'masuk'">
                                                            <span class="badge bg-success-50 text-success-700 border border-success-200">Stok Masuk</span>
                                                        </template>
                                                        <template x-if="request.type !== 'masuk'">
                                                            <span class="badge bg-orange-50 text-orange-700 border border-orange-200">Stok Keluar</span>
                                                        </template>
                                                    </td>
                                                    <td>
                                                        <div class="font-bold text-secondary-900"><span x-text="request.quantity"></span> <span class="text-xs font-normal text-secondary-500" x-text="request.unit"></span></div>
                                                    </td>
                                                </tr>
                                            </template>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <!-- Mobile view -->
                                <div class="md:hidden divide-y divide-secondary-100">
                                    <template x-for="request in pendingRequestsList" :key="request.id">
                                        <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" @click="window.location.href='{{ route('inventory.index') }}/' + request.sparepart_id">
                                            <div class="flex justify-between items-start mb-2">
                                                <div class="font-bold text-secondary-900 leading-tight pr-4" x-text="request.sparepart_name"></div>
                                                <span class="text-sm font-bold text-secondary-900 whitespace-nowrap"><span x-text="request.quantity"></span> <span class="text-[10px] text-secondary-500 font-normal" x-text="request.unit"></span></span>
                                            </div>
                                            <div class="flex items-center justify-between mt-2">
                                                <template x-if="request.type === 'masuk'">
                                                    <span class="badge bg-success-50 text-success-700 border border-success-200 text-xs py-0.5">Stok Masuk</span>
                                                </template>
                                                <template x-if="request.type !== 'masuk'">
                                                    <span class="badge bg-orange-50 text-orange-700 border border-orange-200 text-xs py-0.5">Stok Keluar</span>
                                                </template>
                                                <span class="text-[10px] text-secondary-400 font-medium" x-text="request.created_at_formatted"></span>
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            <!-- Bottom Section (3 Columns Layout) -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                
                <!-- Top Picks -->
                <div class="card flex flex-col h-full overflow-hidden">
                    <div class="card-header border-b border-primary-100 p-5 flex items-center gap-2 bg-gradient-to-r from-primary-50 to-white">
                        <div class="w-8 h-8 rounded-lg bg-primary-100 flex items-center justify-center text-primary-600 flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path></svg>
                        </div>
                        <h3 class="font-bold text-secondary-900 truncate">Sering Anda Pinjam</h3>
                    </div>
                    <div class="p-4 flex-1 overflow-y-auto" style="max-height: 400px;">
                        <template x-if="topPicks.length === 0">
                            <div class="text-center py-10 flex flex-col items-center justify-center h-full">
                                <p class="text-sm text-secondary-500">Belum ada barang favorit.</p>
                            </div>
                        </template>
                        <template x-if="topPicks.length > 0">
                            <div class="space-y-3">
                                <template x-for="(pick, index) in topPicks" :key="pick.sparepart_id">
                                    <div class="relative group p-3 border border-secondary-200 rounded-xl hover:shadow-sm hover:border-primary-300 transition-all duration-300 bg-white">
                                        <div class="absolute -top-2.5 -right-2.5 w-6 h-6 rounded-full shadow-sm flex items-center justify-center font-bold text-[10px] z-10"
                                             :class="index === 0 ? 'bg-amber-400 text-white shadow-amber-200' : (index === 1 ? 'bg-slate-300 text-slate-700 shadow-slate-200' : 'bg-orange-300 text-orange-800 shadow-orange-200')">
                                            <span x-text="'#' + (index + 1)"></span>
                                        </div>
                                        <div class="flex items-center gap-3">
                                            <template x-if="pick.image_url">
                                                <img :src="pick.image_url" :alt="pick.sparepart_name" class="w-12 h-12 rounded-lg object-cover border border-secondary-100 flex-shrink-0">
                                            </template>
                                            <template x-if="!pick.image_url">
                                                <div class="w-12 h-12 rounded-lg bg-secondary-50 border border-secondary-100 flex items-center justify-center flex-shrink-0 text-secondary-400">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            </template>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-bold text-sm text-secondary-900 line-clamp-1 cursor-pointer hover:text-primary-600 transition-colors" @click="window.location.href='{{ route('inventory.index') }}/' + pick.sparepart_id" x-text="pick.sparepart_name"></h4>
                                                <p class="text-[10px] text-secondary-500 line-clamp-1 mb-1" x-text="pick.category_name"></p>
                                                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] bg-primary-50 text-primary-700 font-bold border border-primary-100">
                                                    <span x-text="pick.total_borrows"></span>x Dipinjam
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

                <!-- Trust Score Section -->
                <div class="card flex flex-col h-full overflow-hidden">
                    <div class="card-header p-5 border-b border-success-100 flex items-center justify-between bg-gradient-to-r from-success-50 to-white">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-success-100 flex items-center justify-center text-success-600 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="font-bold text-secondary-900 truncate">Skor Kedisiplinan</h3>
                        </div>
                    </div>
                    <div class="card-body p-5 flex-1 flex flex-col items-center justify-center" style="min-h: 300px;">
                        <div id="trustScoreChart" class="w-full flex justify-center"></div>
                        <div class="text-center mt-2 px-4">
                            @if($trustScore >= 90)
                                <p class="text-success-600 font-bold text-sm">Sangat Disiplin! 🌟</p>
                                <p class="text-secondary-500 text-[10px] mt-1">Anda andal dalam mengembalikan barang tepat pada waktunya.</p>
                            @elseif($trustScore >= 70)
                                <p class="text-warning-600 font-bold text-sm">Cukup Baik 👍</p>
                                <p class="text-secondary-500 text-[10px] mt-1">Tingkatkan lagi untuk mengembalikan barang selalu tepat waktu.</p>
                            @else
                                <p class="text-danger-600 font-bold text-sm">Kurang Disiplin ⚠️</p>
                                <p class="text-secondary-500 text-[10px] mt-1">Banyak barang terlambat dikembalikan. Harap perhatikan tenggat Anda.</p>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Activity Logs Section -->
                <div class="card flex flex-col h-full overflow-hidden">
                    <div class="card-header p-5 border-b border-secondary-100 flex items-center justify-between bg-white">
                        <div class="flex items-center gap-2">
                            <div class="w-8 h-8 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-600 flex-shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h3 class="font-bold text-secondary-900 truncate">Aktivitas Terakhir</h3>
                        </div>
                        <a href="{{ route('reports.activity-logs.index') }}" class="text-[10px] sm:text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors whitespace-nowrap">Lihat Semua</a>
                    </div>
                    <div class="p-0 flex-1 overflow-y-auto" style="max-height: 400px;">
                        <template x-if="activityLogs.length === 0">
                            <div class="text-center py-10 flex flex-col items-center">
                                <div class="w-16 h-16 bg-secondary-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-sm font-medium text-secondary-900">Belum ada aktivitas</p>
                            </div>
                        </template>
                        <template x-if="activityLogs.length > 0">
                            <div class="divide-y divide-secondary-100">
                                <template x-for="log in activityLogs" :key="log.id || Math.random()">
                                    <div class="p-4 hover:bg-secondary-50/50 transition-colors flex gap-3 items-start">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center"
                                             :class="{
                                                'bg-success-50 text-success-600': log.action_lower.includes('tambah') || log.action_lower.includes('create') || log.action_lower.includes('masuk'),
                                                'bg-danger-50 text-danger-600': log.action_lower.includes('hapus') || log.action_lower.includes('delete') || log.action_lower.includes('tolak'),
                                                'bg-primary-50 text-primary-600': log.action_lower.includes('update') || log.action_lower.includes('edit') || log.action_lower.includes('setuju'),
                                                'bg-purple-50 text-purple-600': log.action_lower.includes('login') || log.action_lower.includes('logout'),
                                                'bg-secondary-100 text-secondary-600': !log.action_lower.includes('tambah') && !log.action_lower.includes('create') && !log.action_lower.includes('masuk') && !log.action_lower.includes('hapus') && !log.action_lower.includes('delete') && !log.action_lower.includes('tolak') && !log.action_lower.includes('update') && !log.action_lower.includes('edit') && !log.action_lower.includes('setuju') && !log.action_lower.includes('login') && !log.action_lower.includes('logout')
                                             }">
                                             <template x-if="log.action_lower.includes('tambah') || log.action_lower.includes('create') || log.action_lower.includes('masuk')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                             </template>
                                             <template x-if="log.action_lower.includes('hapus') || log.action_lower.includes('delete') || log.action_lower.includes('tolak')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                             </template>
                                             <template x-if="log.action_lower.includes('update') || log.action_lower.includes('edit') || log.action_lower.includes('setuju')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                             </template>
                                             <template x-if="log.action_lower.includes('login') || log.action_lower.includes('logout')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                             </template>
                                             <template x-if="!log.action_lower.includes('tambah') && !log.action_lower.includes('create') && !log.action_lower.includes('masuk') && !log.action_lower.includes('hapus') && !log.action_lower.includes('delete') && !log.action_lower.includes('tolak') && !log.action_lower.includes('update') && !log.action_lower.includes('edit') && !log.action_lower.includes('setuju') && !log.action_lower.includes('login') && !log.action_lower.includes('logout')">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                             </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-2 mb-1">
                                                <p class="text-sm font-semibold text-secondary-900 truncate" x-text="log.action"></p>
                                                <span class="text-[10px] text-secondary-500 whitespace-nowrap" x-text="log.created_at_diff"></span>
                                            </div>
                                            <template x-if="log.details">
                                                <p class="text-xs text-secondary-600 line-clamp-2 leading-relaxed" x-text="log.details"></p>
                                            </template>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                    </div>
                </div>

            </div>

            <!-- Floating Action Button (FAB) for Scan QR -->
            <button id="start-scan-btn" class="fixed bottom-6 right-6 sm:bottom-8 sm:right-8 z-50 flex items-center justify-center gap-0 sm:gap-2 p-4 sm:px-6 sm:py-4 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-full shadow-2xl shadow-primary-500/40 transition-all duration-300 hover:scale-105 active:scale-95 group">
                <x-icon.scan-qr class="w-6 h-6 sm:w-7 sm:h-7 group-hover:rotate-12 transition-transform duration-300" />
                <span class="hidden sm:inline text-lg">Scan QR</span>
            </button>
            
        </div>
    </div>

    @push('scripts')
    <script>
        function operatorDashboardData() {
            return {
                activeBorrowingsList: @json($activeBorrowingsList ?? []),
                pendingRequestsList: @json($pendingRequestsList ?? []),
                topPicks: @json($topPicks ?? []),
                activityLogs: @json($activityLogs ?? [])
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Borrowing Trend Chart
            const trendData = @json($borrowingTrend);
            const trendCategories = trendData.map(item => item.period);
            const trendSeries = trendData.map(item => item.count);

            const trendOptions = {
                series: [{
                    name: 'Total Peminjaman',
                    data: trendSeries
                }],
                chart: {
                    type: 'area', // Area chart looks smoother and more modern
                    height: 300,
                    toolbar: { show: false },
                    fontFamily: 'inherit',
                    zoom: { enabled: false }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: { height: 250 }
                    }
                }],
                colors: ['#4f46e5'], // primary-600
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: { enabled: false },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: trendCategories,
                    axisBorder: { show: false },
                    axisTicks: { show: false },
                    @if($trendPeriod === '30_days')
                    tickAmount: 6,
                    @endif
                    labels: {
                        style: { colors: '#64748b', fontSize: '11px' },
                        @if($trendPeriod === '30_days')
                        rotate: -30,
                        rotateAlways: false,
                        @endif
                        offsetY: 2,
                    },
                },
                yaxis: {
                    labels: { style: { colors: '#64748b' }, formatter: (value) => { return Math.round(value) } }
                },
                grid: {
                    borderColor: '#f1f5f9', // secondary-100
                    strokeDashArray: 4,
                },
                tooltip: {
                    theme: 'light'
                },
                noData: {
                    text: 'Belum ada data peminjaman',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: {
                        color: '#94a3b8',
                        fontSize: '14px',
                        fontFamily: 'inherit'
                    }
                }
            };
            
            let borrowingChart, requestStatusChart, trustChart;

            if(document.querySelector("#operatorBorrowingChart")) {
                borrowingChart = new ApexCharts(document.querySelector("#operatorBorrowingChart"), trendOptions);
                borrowingChart.render();
            }

            // 2. Stock Request Status Distribution Chart
            const statusData = @json($stockChartData);
            const statusSeries = [statusData.pending || 0, statusData.approved || 0, statusData.rejected || 0];
            
            const statusOptions = {
                series: statusSeries,
                chart: {
                    type: 'donut',
                    height: 300,
                    fontFamily: 'inherit',
                },
                labels: ['Menunggu', 'Disetujui', 'Ditolak'],
                colors: ['#f59e0b', '#10b981', '#ef4444'], // warning, success, danger
                plotOptions: {
                    pie: {
                        donut: {
                            size: '75%',
                            labels: {
                                show: true,
                                name: {
                                    show: true,
                                    fontSize: '13px',
                                    fontWeight: 600,
                                    color: '#64748b'
                                },
                                value: {
                                    show: true,
                                    fontSize: '28px',
                                    fontWeight: 800,
                                    color: '#1e293b',
                                    formatter: function (val) { return val; }
                                },
                                total: {
                                    show: true,
                                    showAlways: true,
                                    label: 'TOTAL',
                                    fontSize: '11px',
                                    fontWeight: 700,
                                    color: '#94a3b8',
                                    formatter: function (w) {
                                        return w.globals.seriesTotals.reduce((a, b) => { return a + b }, 0);
                                    }
                                }
                            }
                        }
                    }
                },
                dataLabels: { enabled: false },
                stroke: { width: 3, colors: ['#ffffff'] },
                legend: {
                    position: 'bottom',
                    horizontalAlign: 'center',
                    markers: { radius: 12 },
                    itemMargin: { horizontal: 10, vertical: 8 },
                    fontSize: '13px',
                    fontWeight: 500,
                    labels: { colors: '#475569' }
                },
                responsive: [{
                    breakpoint: 480,
                    options: {
                        chart: { height: 350 },
                        legend: { position: 'bottom' }
                    }
                }],
                tooltip: {
                    theme: 'light',
                    y: { formatter: function(val) { return val + " Pengajuan" } }
                },
                noData: {
                    text: 'Belum ada data pengajuan',
                    align: 'center',
                    verticalAlign: 'middle',
                    style: { color: '#94a3b8', fontSize: '14px' }
                }
            };
            
            if(document.querySelector("#operatorRequestStatusChart")) {
                requestStatusChart = new ApexCharts(document.querySelector("#operatorRequestStatusChart"), statusOptions);
                requestStatusChart.render();
            }

            // 3. Trust Score Chart (Radial Gauge)
            const trustScore = {{ $trustScore }};
            const trustScoreColor = trustScore >= 90 ? '#10b981' : (trustScore >= 70 ? '#f59e0b' : '#ef4444');
            
            const trustScoreOptions = {
                series: [trustScore],
                chart: {
                    type: 'radialBar',
                    height: 280,
                    fontFamily: 'inherit',
                    offsetY: -10
                },
                plotOptions: {
                    radialBar: {
                        startAngle: -135,
                        endAngle: 135,
                        hollow: {
                            margin: 15,
                            size: '60%',
                            background: 'transparent',
                        },
                        track: {
                            background: '#f1f5f9',
                            strokeWidth: '100%',
                            margin: 0, 
                            dropShadow: {
                                enabled: true,
                                top: 0,
                                left: 0,
                                blur: 3,
                                opacity: 0.1
                            }
                        },
                        dataLabels: {
                            show: true,
                            name: {
                                offsetY: 20,
                                show: true,
                                color: '#64748b',
                                fontSize: '10px',
                                fontWeight: 700
                            },
                            value: {
                                offsetY: -10,
                                color: trustScoreColor,
                                fontSize: '32px',
                                fontWeight: 800,
                                show: true,
                                formatter: function (val) {
                                    return val + "%";
                                }
                            }
                        }
                    }
                },
                fill: {
                    type: 'solid',
                    colors: [trustScoreColor]
                },
                stroke: {
                    lineCap: 'round'
                },
                labels: ['SKOR'],
            };

            if(document.querySelector("#trustScoreChart")) {
                trustChart = new ApexCharts(document.querySelector("#trustScoreChart"), trustScoreOptions);
                trustChart.render();
            }

            // Global Dashboard Update Function
            window.updateDashboardCharts = function(unused1, unused2, unused3, fullData) {
                if (!fullData) return;

                // Update Borrowing Trend
                if (borrowingChart && fullData.borrowingTrend) {
                    const newSeries = fullData.borrowingTrend.map(item => item.count);
                    const newCategories = fullData.borrowingTrend.map(item => item.period);
                    borrowingChart.updateOptions({ xaxis: { categories: newCategories } });
                    borrowingChart.updateSeries([{ data: newSeries }]);
                }

                // Update Stock Status Distribution
                if (requestStatusChart && fullData.stockChartData) {
                    const newData = [
                        fullData.stockChartData.pending || 0,
                        fullData.stockChartData.approved || 0,
                        fullData.stockChartData.rejected || 0
                    ];
                    requestStatusChart.updateSeries(newData);
                }

                // Update Trust Score
                if (trustChart && fullData.trustScore !== undefined) {
                    const ns = fullData.trustScore;
                    const nc = ns >= 90 ? '#10b981' : (ns >= 70 ? '#f59e0b' : '#ef4444');
                    trustChart.updateOptions({
                        fill: { colors: [nc] },
                        plotOptions: { radialBar: { dataLabels: { value: { color: nc } } } }
                    });
                    trustChart.updateSeries([ns]);
                }
            };
        });
    </script>
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const scanBtn = document.getElementById('start-scan-btn');
            const closeBtn = document.getElementById('close-scan-btn');
            const modal = document.getElementById('qr-reader-modal');
            const resultDiv = document.getElementById('qr-reader-results');
            let html5QrCode = null;

            const onScanSuccess = (decodedText, decodedResult) => {
                console.log(`Scan result: ${decodedText}`, decodedResult);
                
                const processResult = () => {
                    resultDiv.classList.remove('hidden');
                    resultDiv.innerText = `Mengalihkan ke barang: ${decodedText}...`;
                    
                    if (decodedText.startsWith('http')) {
                        window.location.href = decodedText;
                    } else {
                        window.location.href = `{{ route('inventory.index') }}?search=${encodeURIComponent(decodedText)}`;
                    }
                };

                // Hentikan scan secara aman jika sedang jalan
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        processResult();
                    }).catch(err => {
                        console.warn("Failed to stop scanning.", err);
                        processResult(); // Lanjut redirect meski gagal stop
                    });
                } else {
                    processResult();
                }
            };

            const switchCamBtn = document.getElementById('switch-camera-btn');
            let cameras = [];
            let currentCameraIndex = 0;

            const startScan = () => {
                modal.classList.remove('hidden');
                modal.classList.add('flex');
                document.body.classList.add('overflow-hidden');
                
                Html5Qrcode.getCameras().then(devices => {
                    if (devices && devices.length > 0) {
                        cameras = devices;
                        currentCameraIndex = devices.length > 1 ? 1 : 0; // Prefer back camera if available

                        if (cameras.length > 1) {
                            switchCamBtn.classList.remove('hidden');
                            switchCamBtn.classList.add('flex');
                        } else {
                            switchCamBtn.classList.add('hidden');
                            switchCamBtn.classList.remove('flex');
                        }

                        startCamera(cameras[currentCameraIndex].id);
                    } else {
                        throw new Error("No cameras found.");
                    }
                }).catch(err => {
                    console.error("Camera access failed.", err);
                    Swal.fire('Error', 'Gagal mengakses kamera: ' + err, 'error');
                });
            };

            const startCamera = (cameraId) => {
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        initScanning(cameraId);
                    });
                } else {
                    initScanning(cameraId);
                }
            };

            const initScanning = (cameraId) => {
                html5QrCode = new Html5Qrcode("qr-reader");
                const config = { fps: 10, qrbox: { width: 250, height: 250 } };

                html5QrCode.start(cameraId, config, onScanSuccess)
                .then(() => {
                    // Center the video element created by html5-qrcode
                    setTimeout(() => {
                        const video = document.querySelector('#qr-reader video');
                        if(video) {
                            video.style.objectFit = 'cover';
                            video.style.margin = 'auto'; // Center block
                        }
                    }, 100);
                })
                .catch(err => {
                    console.error("Scanning failed.", err);
                    modal.classList.add('hidden');
                    document.body.classList.remove('overflow-hidden');
                    Swal.fire('Error', 'Gagal memulai scan: ' + err, 'error');
                });
            };

            const stopScan = () => {
                if (html5QrCode && html5QrCode.isScanning) {
                    html5QrCode.stop().then(() => {
                        html5QrCode.clear();
                        modal.classList.add('hidden');
                        modal.classList.remove('flex');
                        document.body.classList.remove('overflow-hidden');
                    });
                } else {
                    modal.classList.add('hidden');
                    modal.classList.remove('flex');
                    document.body.classList.remove('overflow-hidden');
                }
            };

            if (switchCamBtn) {
                switchCamBtn.addEventListener('click', () => {
                    if (cameras.length > 1) {
                        currentCameraIndex = (currentCameraIndex + 1) % cameras.length;
                        startCamera(cameras[currentCameraIndex].id);
                    }
                });
            }

            if (scanBtn) scanBtn.addEventListener('click', startScan);
            if (closeBtn) closeBtn.addEventListener('click', stopScan);
            
            // Tambahkan listener untuk input file gambar QR
            const fileInput = document.getElementById('qr-input-file');
            if (fileInput) {
                fileInput.addEventListener('change', e => {
                    if (e.target.files.length === 0) return;
                    const imageFile = e.target.files[0];
                    
                    const convertSvgToPng = (file) => {
                        return new Promise((resolve, reject) => {
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                const img = new Image();
                                img.onload = () => {
                                    let width = img.naturalWidth || img.width || 1000;
                                    let height = img.naturalHeight || img.height || 500;
                                    if (width < 800) {
                                        const scale = 800 / width;
                                        width *= scale;
                                        height *= scale;
                                    }
                                    const canvas = document.createElement('canvas');
                                    canvas.width = width;
                                    canvas.height = height;
                                    const ctx = canvas.getContext('2d');
                                    ctx.fillStyle = "white";
                                    ctx.fillRect(0, 0, canvas.width, canvas.height);
                                    ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                                    canvas.toBlob((blob) => {
                                        if (blob) resolve(new File([blob], "qr.png", { type: "image/png" }));
                                        else reject("Gagal konversi canvas.");
                                    }, 'image/png');
                                };
                                img.onerror = () => reject("Gagal muat SVG.");
                                img.src = e.target.result;
                            };
                            reader.onerror = () => reject("Gagal baca file.");
                            reader.readAsDataURL(file);
                        });
                    };

                    const executeScan = (fileToScan) => {
                        html5QrCode.scanFile(fileToScan, true)
                            .then(decodedText => {
                                Swal.close();
                                onScanSuccess(decodedText, null);
                            })
                            .catch(err => {
                                Swal.close();
                                console.warn("Gagal membaca QR dari file gambar.", err);
                                Swal.fire('Error', 'QR / Barcode tidak ditemukan pada gambar. Pastikan gambar jelas dan tidak buram.', 'error');
                            })
                            .finally(() => {
                                e.target.value = ''; // Reset file input
                            });
                    };

                    const scanImageFile = () => {
                        if (!html5QrCode) {
                            html5QrCode = new Html5Qrcode("qr-reader");
                        }
                        
                        Swal.fire({
                            title: 'Memproses Gambar...',
                            text: 'Sistem sedang membaca Barcode/QR...',
                            allowOutsideClick: false,
                            didOpen: () => Swal.showLoading()
                        });
                        
                        if (imageFile.type === 'image/svg+xml') {
                            convertSvgToPng(imageFile)
                                .then(pngFile => executeScan(pngFile))
                                .catch(err => {
                                    Swal.close();
                                    Swal.fire('Error', 'Gagal memproses file SVG.', 'error');
                                    e.target.value = '';
                                });
                        } else {
                            executeScan(imageFile);
                        }
                    };

                    // Matikan kamera terlebih dahulu jika sedang menyala agar tidak bentrok
                    if (html5QrCode && html5QrCode.isScanning) {
                        html5QrCode.stop().then(() => {
                            html5QrCode.clear();
                            scanImageFile();
                        }).catch(err => {
                            console.warn("Mengabaikan error stop kamera", err);
                            scanImageFile();
                        });
                    } else {
                        scanImageFile();
                    }
                });
            }
        });
    </script>
    @endpush
</x-app-layout>
