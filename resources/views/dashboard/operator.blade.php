<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">Halo, {{ Auth::user()->name }}!</h1>
                    <p class="mt-1 text-sm text-secondary-500">Ringkasan aktivitas dan status inventaris Anda saat ini.</p>
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
                            <form method="GET" action="{{ route('dashboard') }}" x-ref="trendForm">
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
                            <h2 class="font-bold text-secondary-900">Daftar Pinjaman Aktif</h2>
                        </div>
                        <a href="{{ route('profile.inventory') }}" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition-colors bg-primary-50 px-3 py-1.5 rounded-full border border-primary-100 shadow-sm hover:bg-primary-100">Lihat Semua</a>
                    </div>
                    <div class="card-body flex-1 p-0">
                        @if($activeBorrowingsList->isEmpty())
                            <div class="p-8 text-center text-secondary-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <p>Belum ada barang yang Anda pinjam saat ini.</p>
                            </div>
                        @else
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
                                        @foreach($activeBorrowingsList as $borrowing)
                                            <tr class="hover:bg-secondary-50/50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('inventory.show', $borrowing->sparepart_id) }}'">
                                                <td>
                                                    <div class="font-medium text-secondary-900 line-clamp-1" title="{{ $borrowing->sparepart->name }}">{{ $borrowing->sparepart->name }}</div>
                                                </td>
                                                <td>
                                                    <div class="font-bold text-secondary-900">{{ $borrowing->remaining_quantity }}</div>
                                                </td>
                                                <td>
                                                    <div class="text-xs text-secondary-500">
                                                        Tgl Pinjam: <span class="text-secondary-700">{{ $borrowing->borrowed_at->format('d M Y') }}</span><br>
                                                        Tenggat Kembali: <span class="font-medium {{ $borrowing->isOverdue() ? 'text-danger-600' : 'text-secondary-700' }}">{{ $borrowing->expected_return_at->format('d M Y') }}</span>
                                                    </div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Mobile view -->
                            <div class="md:hidden divide-y divide-secondary-100">
                                @foreach($activeBorrowingsList as $borrowing)
                                    <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('inventory.show', $borrowing->sparepart_id) }}'">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="font-bold text-secondary-900 leading-tight pr-4">{{ $borrowing->sparepart->name }}</div>
                                            <span class="text-sm font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-full whitespace-nowrap">{{ $borrowing->remaining_quantity }} Unit</span>
                                        </div>
                                        <div class="text-xs text-secondary-500 flex flex-col gap-1 mt-2">
                                            <div class="flex items-center justify-between">
                                                <span>Pinjam: {{ $borrowing->borrowed_at->format('d M Y') }}</span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span>Tenggat: <span class="font-semibold {{ $borrowing->isOverdue() ? 'text-danger-600' : 'text-secondary-700' }}">{{ $borrowing->expected_return_at->format('d M Y') }}</span></span>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
                        @if($pendingRequestsList->isEmpty())
                            <div class="p-8 text-center text-secondary-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p>Tidak ada pengajuan stok yang menunggu persetujuan.</p>
                            </div>
                        @else
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
                                        @foreach($pendingRequestsList as $request)
                                            <tr class="hover:bg-secondary-50/50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('inventory.show', $request->sparepart_id) }}'">
                                                <td>
                                                    <div class="font-medium text-secondary-900 line-clamp-1" title="{{ $request->sparepart->name }}">{{ $request->sparepart->name }}</div>
                                                    <div class="text-[10px] text-secondary-500">{{ $request->created_at->format('d M Y H:i') }}</div>
                                                </td>
                                                <td>
                                                    @if($request->type === 'masuk')
                                                        <span class="badge bg-success-50 text-success-700 border border-success-200">Stok Masuk</span>
                                                    @else
                                                        <span class="badge bg-orange-50 text-orange-700 border border-orange-200">Stok Keluar</span>
                                                    @endif
                                                </td>
                                                <td>
                                                    <div class="font-bold text-secondary-900">{{ $request->quantity }} <span class="text-xs font-normal text-secondary-500">{{ $request->sparepart->unit ?? 'Pcs' }}</span></div>
                                                </td>
                                            </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Mobile view -->
                            <div class="md:hidden divide-y divide-secondary-100">
                                @foreach($pendingRequestsList as $request)
                                    <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" onclick="window.location.href='{{ route('inventory.show', $request->sparepart_id) }}'">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="font-bold text-secondary-900 leading-tight pr-4">{{ $request->sparepart->name }}</div>
                                            <span class="text-sm font-bold text-secondary-900 whitespace-nowrap">{{ $request->quantity }} <span class="text-[10px] text-secondary-500 font-normal">{{ $request->sparepart->unit ?? 'Pcs' }}</span></span>
                                        </div>
                                        <div class="flex items-center justify-between mt-2">
                                            @if($request->type === 'masuk')
                                                <span class="badge bg-success-50 text-success-700 border border-success-200 text-xs py-0.5">Stok Masuk</span>
                                            @else
                                                <span class="badge bg-orange-50 text-orange-700 border border-orange-200 text-xs py-0.5">Stok Keluar</span>
                                            @endif
                                            <span class="text-[10px] text-secondary-400 font-medium">{{ $request->created_at->format('d M Y H:i') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
                        @if($topPicks->isEmpty())
                            <div class="text-center py-10 flex flex-col items-center justify-center h-full">
                                <p class="text-sm text-secondary-500">Belum ada barang favorit.</p>
                            </div>
                        @else
                            <div class="space-y-3">
                                @foreach($topPicks as $index => $pick)
                                    <div class="relative group p-3 border border-secondary-200 rounded-xl hover:shadow-sm hover:border-primary-300 transition-all duration-300 bg-white">
                                        <div class="absolute -top-2.5 -right-2.5 w-6 h-6 rounded-full {{ $index === 0 ? 'bg-amber-400 text-white shadow-amber-200' : ($index === 1 ? 'bg-slate-300 text-slate-700 shadow-slate-200' : 'bg-orange-300 text-orange-800 shadow-orange-200') }} shadow-sm flex items-center justify-center font-bold text-[10px] z-10">
                                            #{{ $index + 1 }}
                                        </div>
                                        <div class="flex items-center gap-3">
                                            @if($pick->sparepart->image)
                                                <img src="{{ Storage::url($pick->sparepart->image) }}" alt="{{ $pick->sparepart->name }}" class="w-12 h-12 rounded-lg object-cover border border-secondary-100 flex-shrink-0">
                                            @else
                                                <div class="w-12 h-12 rounded-lg bg-secondary-50 border border-secondary-100 flex items-center justify-center flex-shrink-0 text-secondary-400">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            @endif
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-bold text-sm text-secondary-900 line-clamp-1 cursor-pointer hover:text-primary-600 transition-colors" onclick="window.location.href='{{ route('inventory.show', $pick->sparepart->id) }}'">{{ $pick->sparepart->name }}</h4>
                                                <p class="text-[10px] text-secondary-500 line-clamp-1 mb-1">{{ $pick->sparepart->category->name ?? '-' }}</p>
                                                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] bg-primary-50 text-primary-700 font-bold border border-primary-100">
                                                    {{ $pick->total_borrows }}x Dipinjam
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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
                        @if($activityLogs->isEmpty())
                            <div class="text-center py-10 flex flex-col items-center">
                                <div class="w-16 h-16 bg-secondary-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-sm font-medium text-secondary-900">Belum ada aktivitas</p>
                            </div>
                        @else
                            <div class="divide-y divide-secondary-100">
                                @foreach($activityLogs as $log)
                                    @php
                                        // Determine icon and colors based on action type
                                        $actionLower = strtolower($log->action);
                                        
                                        if(str_contains($actionLower, 'tambah') || str_contains($actionLower, 'create') || str_contains($actionLower, 'masuk')) {
                                            $iconBg = 'bg-success-50';
                                            $iconColor = 'text-success-600';
                                            $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>';
                                        } elseif(str_contains($actionLower, 'hapus') || str_contains($actionLower, 'delete') || str_contains($actionLower, 'tolak')) {
                                            $iconBg = 'bg-danger-50';
                                            $iconColor = 'text-danger-600';
                                            $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>';
                                        } elseif(str_contains($actionLower, 'update') || str_contains($actionLower, 'edit') || str_contains($actionLower, 'setuju')) {
                                            $iconBg = 'bg-primary-50';
                                            $iconColor = 'text-primary-600';
                                            $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>';
                                        } elseif(str_contains($actionLower, 'login') || str_contains($actionLower, 'logout')) {
                                            $iconBg = 'bg-purple-50';
                                            $iconColor = 'text-purple-600';
                                            $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path></svg>';
                                        } else {
                                            $iconBg = 'bg-secondary-100';
                                            $iconColor = 'text-secondary-600';
                                            $icon = '<svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>';
                                        }
                                    @endphp
                                    <div class="p-4 hover:bg-secondary-50/50 transition-colors flex gap-3 items-start">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg {{ $iconBg }} {{ $iconColor }} flex items-center justify-center">
                                            {!! $icon !!}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-2 mb-1">
                                                <p class="text-sm font-semibold text-secondary-900 truncate">{{ $log->action }}</p>
                                                <span class="text-[10px] text-secondary-500 whitespace-nowrap">{{ $log->created_at->diffForHumans() }}</span>
                                            </div>
                                            @if($log->details)
                                                <p class="text-xs text-secondary-600 line-clamp-2 leading-relaxed">{{ strip_tags($log->details) }}</p>
                                            @endif
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @endif
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

            </div>
        </div>
    </div>

    @push('scripts')
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
                    labels: { style: { colors: '#64748b' } } // secondary-500
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
                }
            };
            
            if(document.querySelector("#operatorBorrowingChart")) {
                const borrowingChart = new ApexCharts(document.querySelector("#operatorBorrowingChart"), trendOptions);
                borrowingChart.render();
            }

            // 2. Stock Request Status Distribution Chart
            const statusData = @json($stockChartData);
            const statusSeries = [statusData.pending || 0, statusData.approved || 0, statusData.rejected || 0];
            
            // Generate chart only if there is data
            const hasData = statusSeries.some(val => val > 0);
            
            if (hasData) {
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
                    tooltip: {
                        theme: 'light',
                        y: { formatter: function(val) { return val + " Pengajuan" } }
                    }
                };
                
                if(document.querySelector("#operatorRequestStatusChart")) {
                    const statusChart = new ApexCharts(document.querySelector("#operatorRequestStatusChart"), statusOptions);
                    statusChart.render();
                }
            } else {
                if(document.querySelector("#operatorRequestStatusChart")) {
                    document.querySelector("#operatorRequestStatusChart").innerHTML = `
                        <div class="text-center text-secondary-400 py-10 flex flex-col items-center">
                            <svg class="w-12 h-12 mb-3 text-secondary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 12H4"></path></svg>
                            <p class="text-sm">Belum ada data pengajuan stok.</p>
                        </div>
                    `;
                }
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
                const trustChart = new ApexCharts(document.querySelector("#trustScoreChart"), trustScoreOptions);
                trustChart.render();
            }
        });
    </script>
    @endpush
</x-app-layout>
