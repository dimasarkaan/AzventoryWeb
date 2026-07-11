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
                <div id="qr-reader-modal" 
                     role="dialog" 
                     aria-modal="true" 
                     aria-labelledby="qr-modal-title"
                     class="fixed inset-0 z-[100] hidden items-center justify-center p-4 bg-secondary-900/60 backdrop-blur-sm">
                    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-lg overflow-hidden">
                        <div class="p-4 border-b border-secondary-100 flex items-center justify-between">
                            <h3 id="qr-modal-title" class="font-bold text-secondary-900">Scan Barcode / QR Barang</h3>
                            <button id="close-scan-btn" 
                                    aria-label="Tutup Scan"
                                    class="p-2 hover:bg-secondary-100 rounded-lg text-secondary-500 transition-colors">
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
                                <button id="switch-camera-btn" 
                                        aria-label="Putar Kamera"
                                        class="hidden flex-1 sm:flex-none items-center justify-center gap-2 px-4 py-2 bg-secondary-100 hover:bg-secondary-200 text-secondary-700 font-semibold rounded-lg transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                                    <span>Putar Kamera</span>
                                </button>
                                
                                <label for="qr-input-file" class="flex-1 sm:flex-none flex items-center justify-center gap-2 px-4 py-2 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-semibold rounded-lg transition-colors cursor-pointer">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <span>Upload Galeri</span>
                                </label>
                                <input type="file" id="qr-input-file" name="qr_file" class="hidden" accept="image/*">
                            </div>

                            <div id="qr-reader-results" class="mt-4 p-3 bg-primary-50 text-primary-700 text-sm rounded-lg hidden w-full text-center">
                                Mencari data barang...
                            </div>
                        </div>
                    </div>
                </div>

            {{-- ================================================================
                 STAT CARDS — Bento Workspace Style
                 Referensi: ui-ux-pro-max / Bento Grids + Executive Dashboard
                 ================================================================ --}}
            @php
                $overdueCount = collect($activeBorrowingsList ?? [])->where('is_overdue', true)->count();
                $maxBorrowings = 20; // kapasitas default
                $borrowProgress = $maxBorrowings > 0 ? min(100, round((($activeBorrowingsCount ?? 0) / $maxBorrowings) * 100)) : 0;
                $borrowProgressColor = $borrowProgress >= 80 ? 'bg-danger-500' : ($borrowProgress >= 50 ? 'bg-warning-500' : 'bg-primary-500');
                $pendingProgress = min(100, (($pendingRequestsCount ?? 0) / max(1, ($pendingRequestsCount ?? 0) + 5)) * 100);
            @endphp
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5 mb-6">

                {{-- Card 1: Pinjaman Aktif --}}
                <div onclick="document.getElementById('active-borrowings-section')?.scrollIntoView({behavior:'smooth',block:'start'})"
                     role="button" tabindex="0"
                     onkeydown="if(event.key==='Enter')this.click()"
                     class="group bg-white rounded-[20px] border border-secondary-100 shadow-card p-6 flex flex-col gap-4 hover:shadow-lg hover:border-primary-200 transition-all duration-300 cursor-pointer focus:outline-none focus:ring-2 focus:ring-primary-500 focus:ring-offset-2"
                     aria-label="Scroll ke daftar pinjaman aktif">

                    {{-- Icon + Label --}}
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-primary-100 flex items-center justify-center text-primary-600 shadow-sm group-hover:scale-110 group-hover:bg-primary-200 transition-all duration-300 flex-shrink-0">
                            <x-icon.borrow-user class="w-6 h-6" stroke-width="2" />
                        </div>
                        <span class="text-xs font-bold text-secondary-400 uppercase tracking-widest pt-1">Pinjaman Aktif</span>
                    </div>

                    {{-- Angka --}}
                    <div class="flex-1">
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl font-black tabular-nums text-secondary-900 tracking-tight leading-none">{{ $activeBorrowingsCount ?? 0 }}</span>
                            <span class="text-sm font-medium text-secondary-400">unit dipinjam</span>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-secondary-400">Kapasitas pinjaman</span>
                            <span class="text-xs font-bold text-secondary-500">{{ $activeBorrowingsCount ?? 0 }} / {{ $maxBorrowings }}</span>
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-secondary-100 overflow-hidden">
                            <div class="h-full rounded-full {{ $borrowProgressColor }} transition-all duration-500"
                                 style="width: {{ $borrowProgress }}%"></div>
                        </div>
                    </div>

                    {{-- Status Hint --}}
                    @if ($overdueCount > 0)
                        <div class="flex items-center gap-2 pt-1 border-t border-secondary-100">
                            <span class="w-2 h-2 rounded-full bg-danger-500 animate-pulse flex-shrink-0"></span>
                            <span class="text-xs text-danger-600 font-medium">{{ $overdueCount }} item melewati batas waktu</span>
                        </div>
                    @else
                        <div class="flex items-center gap-2 pt-1 border-t border-secondary-100">
                            <span class="w-2 h-2 rounded-full bg-success-500 flex-shrink-0"></span>
                            <span class="text-xs text-success-600 font-medium">Semua peminjaman tepat waktu</span>
                        </div>
                    @endif
                </div>

                {{-- Card 2: Pengajuan Menunggu --}}
                <div class="group bg-white rounded-[20px] border border-secondary-100 shadow-card p-6 flex flex-col gap-4 hover:shadow-lg hover:border-warning-200 transition-all duration-300 cursor-default">

                    {{-- Icon + Label --}}
                    <div class="flex items-start justify-between">
                        <div class="w-12 h-12 rounded-2xl bg-warning-100 flex items-center justify-center text-warning-600 shadow-sm group-hover:scale-110 group-hover:bg-warning-200 transition-all duration-300 flex-shrink-0">
                            <x-icon.low-stock class="w-6 h-6" stroke-width="2" />
                        </div>
                        <span class="text-xs font-bold text-secondary-400 uppercase tracking-widest pt-1">Pengajuan Menunggu</span>
                    </div>

                    {{-- Angka --}}
                    <div class="flex-1">
                        <div class="flex items-baseline gap-2">
                            <span class="text-5xl font-black tabular-nums text-secondary-900 tracking-tight leading-none">{{ $pendingRequestsCount ?? 0 }}</span>
                            <span class="text-sm font-medium text-secondary-400">pengajuan stok</span>
                        </div>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="space-y-1.5">
                        <div class="flex justify-between items-center">
                            <span class="text-xs text-secondary-400">Butuh persetujuan admin</span>
                            @if (($pendingRequestsCount ?? 0) > 0)
                                <span class="text-xs font-bold text-warning-600">Menunggu review</span>
                            @else
                                <span class="text-xs font-bold text-success-600">Semua diproses</span>
                            @endif
                        </div>
                        <div class="h-1.5 w-full rounded-full bg-secondary-100 overflow-hidden">
                            @if (($pendingRequestsCount ?? 0) > 0)
                                <div class="h-full rounded-full bg-warning-400 transition-all duration-500 animate-pulse"
                                     style="width: {{ min(100, ($pendingRequestsCount ?? 0) * 10) }}%"></div>
                            @else
                                <div class="h-full rounded-full bg-success-400 w-full transition-all duration-500"></div>
                            @endif
                        </div>
                    </div>

                    {{-- Status Hint --}}
                    <div class="flex items-center gap-2 pt-1 border-t border-secondary-100">
                        @if (($pendingRequestsCount ?? 0) > 0)
                            <span class="w-2 h-2 rounded-full bg-warning-400 animate-pulse flex-shrink-0"></span>
                            <span class="text-xs text-warning-600 font-medium">Admin belum memproses pengajuanmu</span>
                        @else
                            <span class="w-2 h-2 rounded-full bg-success-500 flex-shrink-0"></span>
                            <span class="text-xs text-success-600 font-medium">Tidak ada pengajuan yang menunggu</span>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">
                <!-- Borrowing Trend Chart -->
                <div class="card flex flex-col lg:col-span-2 shadow-soft border-none">
                    <div class="card-header border-b border-secondary-100 p-4 sm:p-5 flex flex-wrap items-center justify-between gap-3 bg-white">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-secondary-50 flex items-center justify-center text-secondary-600 flex-shrink-0 border border-secondary-100">
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
                <div class="card flex flex-col shadow-soft border-none">
                    <div class="card-header border-b border-secondary-100 p-5 flex items-center gap-3 bg-white">
                        <div class="w-8 h-8 rounded-lg bg-secondary-50 flex items-center justify-center text-secondary-600 flex-shrink-0 border border-secondary-100">
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
                <div id="active-borrowings-section" class="card flex flex-col h-full shadow-soft border-none">
                    <div class="card-header border-b border-secondary-100 p-5 flex items-center justify-between bg-white">
                        <div class="flex items-center gap-3">
                             <div class="w-8 h-8 rounded-lg bg-secondary-50 flex items-center justify-center text-secondary-600 flex-shrink-0 border border-secondary-100">
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
                                                <tr class="hover:bg-secondary-50/50 transition-colors cursor-pointer" @click="window.location.href='{{ route('inventory.index') }}/' + (borrowing.sparepart_uuid || borrowing.sparepart_id)">
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
                                        <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" @click="window.location.href='{{ route('inventory.index') }}/' + (borrowing.sparepart_uuid || borrowing.sparepart_id)">
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
                <div class="card flex flex-col h-full shadow-soft border-none">
                    <div class="card-header border-b border-secondary-100 p-5 flex items-center justify-between bg-white">
                        <div class="flex items-center gap-3">
                             <div class="w-8 h-8 rounded-lg bg-secondary-50 flex items-center justify-center text-secondary-600 flex-shrink-0 border border-secondary-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <h2 class="font-bold text-secondary-900">Daftar Pengajuan Stok</h2>
                        </div>
                    </div>
                    <div class="card-body flex-1 p-0">
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
                                                <tr class="hover:bg-secondary-50/50 transition-colors cursor-pointer" @click="window.location.href='{{ route('inventory.index') }}/' + (request.sparepart_uuid || request.sparepart_id)">
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
                                        <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" @click="window.location.href='{{ route('inventory.index') }}/' + (request.sparepart_uuid || request.sparepart_id)">
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

            {{-- ================================================================
                 BOTTOM SECTION (3 Columns)
                 Skill: Bento Grids — rounded-[20px], soft bg, subtle border
                 UX Rules: no emoji, no layout-shift hover, 8px+ gap between items
                 ================================================================ --}}
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

                {{-- Card 1: Sering Anda Pinjam — Leaderboard Style --}}
                {{-- Ref: Sales Intelligence Dashboard — rank-1: gold, rank-2: silver, rank-3: bronze --}}
                <div class="card flex flex-col overflow-hidden shadow-soft border-none">
                    {{-- Header: konsisten dengan card lain (plain white + icon + title + subtitle) --}}
                    <div class="border-b border-secondary-100 px-5 py-3 flex items-center gap-3 bg-white flex-shrink-0">
                        <div class="size-8 rounded-lg bg-amber-50 flex items-center justify-center text-amber-500 flex-shrink-0 border border-amber-100">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900 text-sm leading-tight">Sering Anda Pinjam</h3>
                            <p class="text-[11px] text-secondary-400">Top 3 barang favorit Anda</p>
                        </div>
                    </div>
                    {{-- Body: row-style items, consistent dengan Card 3 --}}
                    <div class="flex-1 flex flex-col">
                        <template x-if="topPicks.length === 0">
                            <div class="flex-1 flex flex-col items-center justify-center gap-2 py-6">
                                <div class="size-10 rounded-full bg-amber-50 flex items-center justify-center">
                                    <svg class="w-5 h-5 text-amber-300" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/></svg>
                                </div>
                                <p class="text-sm text-secondary-400 font-medium">Belum ada riwayat peminjaman</p>
                            </div>
                        </template>
                        <template x-if="topPicks.length > 0">
                            <div class="divide-y divide-secondary-50">
                                <template x-for="(pick, index) in topPicks" :key="pick.sparepart_id">
                                    <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-secondary-50/50 transition-colors duration-150 cursor-pointer"
                                         @click="window.location.href='{{ route('inventory.index') }}'"
                                         :aria-label="'Lihat ' + pick.sparepart_name">
                                        {{-- Rank Badge --}}
                                        <div class="size-6 rounded-full flex items-center justify-center font-bold text-[11px] flex-shrink-0"
                                             :class="index === 0 ? 'bg-amber-400 text-white' : (index === 1 ? 'bg-slate-300 text-slate-700' : 'bg-orange-300 text-orange-900')">
                                            <span x-text="index + 1"></span>
                                        </div>
                                        {{-- Thumbnail --}}
                                        <template x-if="pick.image_url">
                                            <img :src="pick.image_url" :alt="pick.sparepart_name" class="size-8 rounded-lg object-cover flex-shrink-0">
                                        </template>
                                        <template x-if="!pick.image_url">
                                            <div class="size-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                                 :class="index === 0 ? 'bg-amber-50 text-amber-500' : 'bg-secondary-100 text-secondary-400'">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                                            </div>
                                        </template>
                                        {{-- Info --}}
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-secondary-900 truncate" x-text="pick.sparepart_name"></p>
                                            <p class="text-[11px] text-secondary-400 truncate" x-text="pick.category_name || 'Tanpa Kategori'"></p>
                                        </div>
                                        {{-- Count badge --}}
                                        <div class="flex-shrink-0 flex items-center gap-1">
                                            <span class="text-xs font-bold tabular-nums"
                                                  :class="index === 0 ? 'text-amber-600' : 'text-secondary-500'"
                                                  x-text="pick.total_borrows"></span>
                                            <span class="text-[10px] text-secondary-400">×</span>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </template>
                        {{-- Footer --}}
                        <div class="border-t border-secondary-100 px-4 py-2.5 flex items-center justify-between bg-secondary-50/30 mt-auto">
                            <span class="text-[11px] text-secondary-400">Berdasarkan riwayat Anda</span>
                            <a href="{{ route('inventory.index') }}" class="text-[11px] font-semibold text-primary-600 hover:text-primary-700 transition-colors">
                                Lihat Inventaris
                            </a>
                        </div>
                    </div>
                </div>


                {{-- Card 2: Skor Kedisiplinan --}}
                <div class="card flex flex-col overflow-hidden shadow-soft border-none">
                    <div class="border-b border-secondary-100 px-5 py-3 flex items-center gap-3 bg-white flex-shrink-0">
                        <div class="size-8 rounded-lg bg-secondary-50 flex items-center justify-center text-secondary-600 flex-shrink-0 border border-secondary-100">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900 text-sm leading-tight">Skor Kedisiplinan</h3>
                            <p class="text-[11px] text-secondary-400">Tingkat ketepatan pengembalian barang</p>
                        </div>
                    </div>
                    {{-- Body: flex-1, chart di center, stats mini di bawah untuk isi ruang --}}
                    <div class="flex-1 flex flex-col">
                        {{-- Chart area centered --}}
                        <div class="flex-1 flex flex-col items-center justify-center px-5 py-2">
                            <div id="trustScoreChart" class="w-full flex justify-center"></div>
                            <div class="text-center mt-3 space-y-1">
                                @if($trustScore >= 90)
                                    <p class="text-success-600 font-bold text-sm">Sangat Disiplin</p>
                                    <p class="text-secondary-400 text-xs">Andal dalam mengembalikan barang tepat waktu.</p>
                                @elseif($trustScore >= 70)
                                    <p class="text-warning-600 font-bold text-sm">Cukup Baik</p>
                                    <p class="text-secondary-400 text-xs">Tingkatkan lagi ketepatan pengembalian barang.</p>
                                @else
                                    <p class="text-danger-600 font-bold text-sm">Perlu Ditingkatkan</p>
                                    <p class="text-secondary-400 text-xs">Banyak barang terlambat dikembalikan. Perhatikan tenggat.</p>
                                @endif
                            </div>
                        </div>
                        {{-- Stat row pinned di bawah — isi ruang bawah yg kosong --}}
                        <div class="border-t border-secondary-100 px-5 py-2.5 grid grid-cols-3 gap-2 bg-secondary-50/40">
                            <div class="text-center">
                                <p class="text-sm font-black text-secondary-900 tabular-nums">{{ $trustScore }}%</p>
                                <p class="text-[10px] text-secondary-400">Skor</p>
                            </div>
                            <div class="text-center border-x border-secondary-100">
                                <p class="text-sm font-black tabular-nums {{ $trustScore >= 70 ? 'text-success-600' : 'text-danger-600' }}">{{ $trustScore >= 90 ? 'A' : ($trustScore >= 70 ? 'B' : 'C') }}</p>
                                <p class="text-[10px] text-secondary-400">Grade</p>
                            </div>
                            <div class="text-center">
                                <p class="text-sm font-black text-secondary-900 tabular-nums">{{ $activeBorrowingsCount ?? 0 }}</p>
                                <p class="text-[10px] text-secondary-400">Aktif</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Card 3: Aktivitas Terakhir --}}
                <div class="card flex flex-col overflow-hidden shadow-soft border-none">
                    <div class="border-b border-secondary-100 px-5 py-3 flex items-center justify-between bg-white flex-shrink-0">
                        <div class="flex items-center gap-3">
                            <div class="size-8 rounded-lg bg-secondary-50 flex items-center justify-center text-secondary-600 flex-shrink-0 border border-secondary-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <h3 class="font-bold text-secondary-900 text-sm leading-tight">Aktivitas Terakhir</h3>
                                <p class="text-[11px] text-secondary-400">Riwayat interaksi sistem Anda</p>
                            </div>
                        </div>
                        <a href="{{ route('reports.activity-logs.index') }}"
                           class="text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors whitespace-nowrap">
                            Lihat Semua
                        </a>
                    </div>
                    {{-- Activity list: clean list rows + divider, no nested cards --}}
                    <div class="flex-1 flex flex-col">
                        <template x-if="activityLogs.length === 0">
                            <div class="text-center py-10 flex flex-col items-center gap-2">
                                <div class="size-12 rounded-full bg-secondary-50 flex items-center justify-center">
                                    <svg class="w-6 h-6 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-sm text-secondary-400 font-medium">Belum ada aktivitas</p>
                            </div>
                        </template>
                        <template x-if="activityLogs.length > 0">
                            <div class="divide-y divide-secondary-50">
                                <template x-for="log in activityLogs.slice(0, 4)" :key="log.id || Math.random()">
                                    <div class="flex items-center gap-3 px-4 py-2.5 hover:bg-secondary-50/60 transition-colors duration-150">
                                        {{-- Icon dot —  color badge, no scale transform --}}
                                        <div class="size-8 rounded-lg flex items-center justify-center flex-shrink-0"
                                             :class="{
                                                'bg-success-50 text-success-600': log.action_lower.includes('tambah') || log.action_lower.includes('create') || log.action_lower.includes('masuk'),
                                                'bg-danger-50 text-danger-600': log.action_lower.includes('hapus') || log.action_lower.includes('delete') || log.action_lower.includes('tolak'),
                                                'bg-primary-50 text-primary-600': log.action_lower.includes('update') || log.action_lower.includes('edit') || log.action_lower.includes('setuju'),
                                                'bg-purple-50 text-purple-600': log.action_lower.includes('login') || log.action_lower.includes('logout'),
                                                'bg-secondary-100 text-secondary-500': !log.action_lower.includes('tambah') && !log.action_lower.includes('create') && !log.action_lower.includes('masuk') && !log.action_lower.includes('hapus') && !log.action_lower.includes('delete') && !log.action_lower.includes('tolak') && !log.action_lower.includes('update') && !log.action_lower.includes('edit') && !log.action_lower.includes('setuju') && !log.action_lower.includes('login') && !log.action_lower.includes('logout')
                                             }">
                                            <template x-if="log.action_lower.includes('tambah') || log.action_lower.includes('create') || log.action_lower.includes('masuk')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                                            </template>
                                            <template x-if="log.action_lower.includes('hapus') || log.action_lower.includes('delete') || log.action_lower.includes('tolak')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </template>
                                            <template x-if="log.action_lower.includes('update') || log.action_lower.includes('edit') || log.action_lower.includes('setuju')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                            </template>
                                            <template x-if="log.action_lower.includes('login') || log.action_lower.includes('logout')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>
                                            </template>
                                            <template x-if="!log.action_lower.includes('tambah') && !log.action_lower.includes('create') && !log.action_lower.includes('masuk') && !log.action_lower.includes('hapus') && !log.action_lower.includes('delete') && !log.action_lower.includes('tolak') && !log.action_lower.includes('update') && !log.action_lower.includes('edit') && !log.action_lower.includes('setuju') && !log.action_lower.includes('login') && !log.action_lower.includes('logout')">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </template>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-semibold text-secondary-900 truncate" x-text="log.action"></p>
                                            <template x-if="log.details">
                                                <p class="text-xs text-secondary-400 truncate" x-text="log.details"></p>
                                            </template>
                                        </div>
                                        <span class="text-[11px] text-secondary-400 whitespace-nowrap flex-shrink-0 tabular-nums" x-text="log.created_at_diff"></span>
                                    </div>
                                </template>
                             </div>
                        </template>
                    </div>
                </div>

          


            </div>

            <!-- Floating Action Button (FAB) for Scan QR -->
            <button id="start-scan-btn" 
                    aria-label="Mulai Scan QR"
                    class="fixed bottom-6 right-6 sm:bottom-8 sm:right-8 z-50 flex items-center justify-center gap-0 sm:gap-2 p-4 sm:px-6 sm:py-4 bg-primary-600 hover:bg-primary-700 text-white font-bold rounded-full shadow-floating transition-all duration-300 hover:scale-105 active:scale-95 group border border-primary-500/30">
                <x-icon.scan-qr class="w-6 h-6 sm:w-7 sm:h-7 group-hover:-rotate-3 transition-transform duration-300" />
                <span class="hidden sm:inline font-semibold">Scan QR</span>
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
                    show: false, // Membuang grid murni gaya clean minimalist
                    padding: { top: 0, right: 0, bottom: 0, left: 10 }
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
                colors: ['#cbd5e1', '#3b82f6', '#94a3b8'], // Donut with monochromatic theme
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
                    height: 180,
                    fontFamily: 'inherit',
                    offsetY: -15
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
                    resultDiv.innerText = `Memproses data QR...`;
                    
                    let targetUrl = decodedText;
                    try {
                        const url = new URL(decodedText);
                        // Fallback pintar: Jika QR lama masih pakai localhost, otomatis ubah ke domain live saat ini
                        if (url.origin.includes('localhost') || url.origin.includes('127.0.0.1')) {
                            targetUrl = window.location.origin + url.pathname + url.search;
                        } 
                        // Keamanan: Cegah QR dari website lain
                        else if (url.origin !== window.location.origin) {
                            window.showAlert('Akses Ditolak', 'QR Code ini bukan berasal dari sistem Azventory.', 'error');
                            resultDiv.classList.add('hidden');
                            return;
                        }
                    } catch (e) {
                        // Jika bukan URL (teks biasa), arahkan ke pencarian stok
                        targetUrl = `{{ route('inventory.index') }}?search=${encodeURIComponent(decodedText)}`;
                    }

                    resultDiv.innerText = `Mengalihkan ke barang...`;
                    window.location.href = targetUrl;
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
                    window.showAlert('Error', 'Gagal mengakses kamera: ' + err, 'error');
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
                    window.showAlert('Error', 'Gagal memulai scan: ' + err, 'error');
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
                                window.showAlert('Error', 'QR / Barcode tidak ditemukan pada gambar. Pastikan gambar jelas dan tidak buram.', 'error');
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
                                    window.showAlert('Error', 'Gagal memproses file SVG.', 'error');
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
