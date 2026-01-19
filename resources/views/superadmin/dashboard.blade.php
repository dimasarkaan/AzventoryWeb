<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" 
             x-data="{ 
                showStats: localStorage.getItem('dashboard_showStats') !== 'false',
                showCharts: localStorage.getItem('dashboard_showCharts') !== 'false',
                showMovement: localStorage.getItem('dashboard_showMovement') !== 'false',
                showTopItems: localStorage.getItem('dashboard_showTopItems') !== 'false',
                showLowStock: localStorage.getItem('dashboard_showLowStock') !== 'false',
                showRecent: localStorage.getItem('dashboard_showRecent') !== 'false',
                showForecast: localStorage.getItem('dashboard_showForecast') !== 'false',
                showDeadStock: localStorage.getItem('dashboard_showDeadStock') !== 'false',
                showLeaderboard: localStorage.getItem('dashboard_showLeaderboard') !== 'false',
                
                toggle(key) {
                    this[key] = !this[key];
                    localStorage.setItem('dashboard_' + key, this[key]);
                }
             }">
             
            <!-- Header Section (Static Title & Global Actions) -->
            <div class="mb-6 flex flex-col xl:flex-row xl:items-center xl:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">Dashboard</h1>
                    <p class="mt-1 text-sm text-secondary-500">Overview Status Inventaris (Real-time)</p>
                </div>
                
                <div class="flex items-center gap-3">
                    <!-- Settings Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="btn btn-secondary flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            <span>Atur Tampilan</span>
                        </button>
                        <div x-show="open" x-transition class="absolute right-0 mt-2 w-56 bg-white rounded-md shadow-lg py-1 z-50 border border-secondary-100">
                            <div class="px-4 py-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider">Widget Aktif</div>
                            <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                <input type="checkbox" :checked="showStats" @change="toggle('showStats')" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-secondary-700">Statistik Utama</span>
                            </label>
                            <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                <input type="checkbox" :checked="showCharts" @change="toggle('showCharts')" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-secondary-700">Distribusi & Lokasi</span>
                            </label>
                            <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                <input type="checkbox" :checked="showLowStock" @change="toggle('showLowStock')" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-secondary-700">Peringatan Stok</span>
                            </label>
                            <div class="border-t border-secondary-100 my-1"></div>
                            <div class="px-4 py-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider">Widget Analitik</div>
                            <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                <input type="checkbox" :checked="showMovement" @change="toggle('showMovement')" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-secondary-700">Pergerakan Stok</span>
                            </label>
                            <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                <input type="checkbox" :checked="showTopItems" @change="toggle('showTopItems')" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-secondary-700">Barang Populer</span>
                            </label>
                            <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                <input type="checkbox" :checked="showForecast" @change="toggle('showForecast')" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-secondary-700">Forecasting</span>
                            </label>
                            <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                <input type="checkbox" :checked="showDeadStock" @change="toggle('showDeadStock')" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-secondary-700">Dead Stock</span>
                            </label>
                            <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                <input type="checkbox" :checked="showLeaderboard" @change="toggle('showLeaderboard')" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-secondary-700">Top Kontributor</span>
                            </label>
                            <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                <input type="checkbox" :checked="showRecent" @change="toggle('showRecent')" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                                <span class="ml-2 text-sm text-secondary-700">Aktivitas Terbaru</span>
                            </label>
                        </div>
                    </div>

                    <a href="{{ route('superadmin.stock-approvals.index') }}" class="btn btn-primary flex items-center gap-2 relative">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path></svg>
                        <span>Persetujuan</span>
                        @if($pendingApprovalsCount > 0)
                            <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-danger-400 opacity-75"></span>
                                <span class="relative inline-flex rounded-full h-3 w-3 bg-danger-500"></span>
                            </span>
                        @endif
                    </a>
                </div>
            </div>

            <!-- Stats Overview Section -->
            <div x-show="showStats" x-transition class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
                <!-- Total Spareparts -->
                <div class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-primary-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-primary-200"></div>
                    <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative">Total Barang</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative">{{ $totalSpareparts }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-primary-600 z-10 relative">
                        <div class="p-2 bg-primary-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-primary-50 text-primary-700 px-2 py-0.5 rounded-full">Item SKU</span>
                    </div>
                </div>

                <!-- Total Stock -->
                <div class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-success-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-success-200"></div>
                    <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative">Total Stok Fisik</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative">{{ $totalStock }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-success-600 z-10 relative">
                        <div class="p-2 bg-success-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-success-50 text-success-700 px-2 py-0.5 rounded-full">Unit</span>
                    </div>
                </div>

                <!-- Total Categories -->
                <div class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-warning-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-warning-200"></div>
                     <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative">Kategori</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative">{{ $totalCategories }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-warning-600 z-10 relative">
                        <div class="p-2 bg-warning-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-warning-50 text-warning-700 px-2 py-0.5 rounded-full">Jenis Barang</span>
                    </div>
                </div>

                <!-- Total Locations -->
                <div class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-secondary-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-secondary-200"></div>
                     <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative">Lokasi Penyimpanan</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative">{{ $totalLocations }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-secondary-600 z-10 relative">
                        <div class="p-2 bg-secondary-200 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-secondary-100 text-secondary-700 px-2 py-0.5 rounded-full">Gudang & Rak</span>
                    </div>
                </div>
            </div>


            <!-- New: Stock Movement Chart -->
            <div x-show="showMovement" x-transition class="card mb-4">
                <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                    <div>
                        <h3 class="font-bold text-secondary-900">Pergerakan Stok</h3>
                        <p class="text-xs text-secondary-500">Aktivitas barang masuk vs keluar periode ini.</p>
                    </div>
                </div>
                <div class="card-body p-6">
                    <div class="h-[250px] w-full">
                        <canvas id="stockMovementChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- New: Top Items Section -->
            <div x-show="showTopItems" x-transition class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                 <!-- Top Exited (Sales/Usage) -->
                 <div class="card">
                     <div class="card-header border-b border-secondary-100 p-4">
                         <h3 class="font-bold text-secondary-900 text-sm uppercase tracking-wide">Barang Sering Keluar (Top 5)</h3>
                     </div>
                     <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-secondary-500 bg-secondary-50 border-b border-secondary-100">
                                <tr>
                                    <th class="px-4 py-2">Barang</th>
                                    <th class="px-4 py-2 text-right">Qty</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-50">
                                @forelse($topExited as $item)
                                    <tr>
                                        <!-- Note: Using sparepart_name from JOIN query -->
                                        <td class="px-4 py-3 font-medium text-secondary-800">{{ $item->sparepart_name ?? 'Unknown' }}</td>
                                        <td class="px-4 py-3 text-right font-bold text-danger-600">-{{ $item->total_qty }}</td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-3 text-center text-xs text-secondary-400">Belum ada data</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                     </div>
                 </div>

                 <!-- Top Entered (Restock) -->
                 <div class="card">
                    <div class="card-header border-b border-secondary-100 p-4">
                        <h3 class="font-bold text-secondary-900 text-sm uppercase tracking-wide">Barang Sering Masuk (Top 5)</h3>
                    </div>
                    <div class="p-0 overflow-x-auto">
                       <table class="w-full text-sm text-left">
                           <thead class="text-xs text-secondary-500 bg-secondary-50 border-b border-secondary-100">
                               <tr>
                                   <th class="px-4 py-2">Barang</th>
                                   <th class="px-4 py-2 text-right">Qty</th>
                               </tr>
                           </thead>
                           <tbody class="divide-y divide-secondary-50">
                               @forelse($topEntered as $item)
                                   <tr>
                                       <td class="px-4 py-3 font-medium text-secondary-800">{{ $item->sparepart_name ?? 'Unknown' }}</td>
                                       <td class="px-4 py-3 text-right font-bold text-success-600">+{{ $item->total_qty }}</td>
                                   </tr>
                               @empty
                                   <tr><td colspan="2" class="px-4 py-3 text-center text-xs text-secondary-400">Belum ada data</td></tr>
                               @endforelse
                           </tbody>
                       </table>
                    </div>
                </div>
            </div>

            <!-- Charts Section -->
            <div x-show="showCharts" x-transition class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <!-- Donut Chart -->
                <div class="card flex flex-col">
                    <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900">Distribusi Stok per Kategori</h3>
 
                    </div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center bg-white min-h-[300px]">
                        <div class="w-full h-full max-h-[300px]">
                            <canvas id="stockByCategoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Bar Chart -->
                <div class="card flex flex-col">
                     <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900">Stok per Lokasi</h3>

                    </div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center bg-white min-h-[300px]">
                        <div class="w-full h-full max-h-[300px]">
                            <canvas id="stockByLocationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bottom Section: Low Stock & Activities -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Low Stock Items (2 cols) -->
                <div x-show="showLowStock" x-transition class="card" :class="{ 'lg:col-span-3': !showRecent, 'lg:col-span-2': showRecent }">
                    <div class="card-header p-5 border-b border-secondary-100 flex justify-between items-center bg-danger-50/30">
                        <div class="flex items-center gap-2">
                             <div class="p-1.5 bg-danger-100 text-danger-600 rounded-lg">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                             </div>
                            <h3 class="font-bold text-secondary-900">Peringatan: Stok Menipis</h3>
                        </div>
                        <a href="{{ route('superadmin.inventory.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Lihat Semua</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-secondary-500">
                            <thead class="text-xs text-secondary-700 uppercase bg-secondary-50 border-b border-secondary-200">
                                <tr>
                                    <th class="px-6 py-3 font-semibold tracking-wider">Sparepart</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider hidden md:table-cell">Kategori</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center">Stok</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center hidden md:table-cell">Min. Stok</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100">
                                @forelse ($lowStockItems as $item)
                                    <tr class="bg-white hover:bg-secondary-50 transition-colors cursor-pointer" onclick="window.location='{{ route('superadmin.inventory.show', $item) }}'">
                                        <td class="px-4 py-3 font-medium text-secondary-800">{{ $item->name ?? 'Unknown' }}</td>
                                        <td class="px-6 py-4 hidden md:table-cell">{{ $item->category ?? '-' }}</td>
                                        <td class="px-6 py-4 text-center font-bold text-danger-600">{{ $item->stock }}</td>
                                        <td class="px-6 py-4 text-center text-secondary-600 hidden md:table-cell">{{ $item->minimum_stock }}</td>
                                        <td class="px-6 py-4 text-center">
                                            @if($item->stock == 0)
                                                <span class="badge badge-danger">Habis</span>
                                            @else
                                                <span class="badge badge-warning">Kritis</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="px-6 py-8 text-center text-secondary-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-success-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <p>Semua stok aman!</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Recent Activities (1 col in web, full in print if needed) -->
                <div x-show="showRecent" x-transition class="card p-0 flex flex-col h-full print-safe" :class="{ 'lg:col-span-3': !showLowStock, 'lg:col-span-1': showLowStock }">
                     <div class="card-header p-5 border-b border-secondary-100 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900">Aktivitas Terbaru</h3>
                        <a href="{{ route('superadmin.activity-logs.index') }}" class="text-sm text-primary-600 hover:text-primary-700 font-medium">Lihat Semua</a>
                     </div>
                    <div class="card-body p-0 overflow-y-auto max-h-[500px] custom-scrollbar">
                        <div class="divide-y divide-secondary-50">
                            @forelse ($recentActivities as $log)
                                <div class="px-5 py-4 hover:bg-secondary-50 transition-colors group">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 group-hover:bg-primary-600 group-hover:text-white transition-all ring-2 ring-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-secondary-900 line-clamp-2">{{ $log->description }}</p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <p class="text-xs text-secondary-500 font-semibold">{{ $log->user->name ?? 'Sistem' }}</p>
                                                <span class="text-secondary-300">&bull;</span>
                                                <p class="text-xs text-secondary-400">{{ $log->created_at->diffForHumans() }}</p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="px-5 py-8 text-center text-secondary-500">
                                    <p class="text-sm">Tidak ada aktivitas terbaru.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            <!-- New Section: Analytics & Forecasting -->
            <div x-show="showStats" class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4 print-grid-3">
                <!-- Forecast Widget -->
                <!-- Forecast Widget (Hidden) -->
                {{-- <div x-show="showForecast" x-transition class="card bg-gradient-to-br from-indigo-50 to-white border-l-4 border-indigo-500">
                    <div class="card-header p-4 border-b border-secondary-100">
                         <h3 class="font-bold text-indigo-900 text-sm uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            Prediksi Kebutuhan (Bulan Depan)
                         </h3>
                    </div>
                    <div class="p-4">
                        <ul class="space-y-3">
                            @forelse($forecasts as $forecast)
                                <li class="flex justify-between items-center text-sm">
                                    <span class="text-secondary-700 font-medium">{{ $forecast['name'] }}</span>
                                    <div class="text-right">
                                        <div class="font-bold text-indigo-700">{{ $forecast['predicted_need'] }} Unit</div>
                                        <div class="text-[10px] text-secondary-400">Stok saat ini: {{ $forecast['current_stock'] }}</div>
                                    </div>
                                </li>
                            @empty
                                <li class="text-center text-xs text-secondary-400 italic py-2">Belum cukup data untuk prediksi</li>
                            @endforelse
                        </ul>
                    </div>
                </div> --}}

                <!-- Dead Stock Widget -->
                <div x-show="showDeadStock" x-transition class="card border-l-4 border-secondary-400">
                    <div class="card-header p-4 border-b border-secondary-100">
                        <h3 class="font-bold text-secondary-900 text-sm uppercase tracking-wide flex items-center gap-2">
                            <svg class="w-4 h-4 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Dead Stock (Lama Mengendap)
                        </h3>
                    </div>
                     <div class="p-0 overflow-x-auto">
                        <table class="w-full text-sm text-left">
                            <tbody class="divide-y divide-secondary-50">
                                @forelse($deadStockItems as $item)
                                    <tr>
                                        <td class="px-4 py-3 font-medium text-secondary-800">{{ $item->name }}</td>
                                        <td class="px-4 py-3 text-right">
                                            <span class="px-2 py-1 text-xs font-semibold bg-secondary-100 text-secondary-600 rounded-full">{{ $item->stock }} Unit</span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="2" class="px-4 py-3 text-center text-xs text-secondary-400">Tidak ada dead stock signifikan</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- User Leaderboard -->
                <div x-show="showLeaderboard" x-transition class="card border-l-4 border-success-400">
                    <div class="card-header p-4 border-b border-secondary-100">
                         <h3 class="font-bold text-secondary-900 text-sm uppercase tracking-wide flex items-center gap-2">
                             <svg class="w-4 h-4 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Top Kontributor (Aktivitas)
                         </h3>
                    </div>
                     <div class="p-4">
                        <ul class="space-y-3">
                            @forelse($activeUsers as $userLog)
                                <li class="flex justify-between items-center text-sm">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-success-100 text-success-700 flex items-center justify-center text-xs font-bold">{{ substr($userLog->user->name ?? '?', 0, 1) }}</div>
                                        <span class="text-secondary-700 font-medium">{{ $userLog->user->name ?? 'Unknown' }}</span>
                                    </div>
                                    <span class="font-bold text-secondary-900">{{ $userLog->total_actions }} Aksi</span>
                                </li>
                            @empty
                                <li class="text-center text-xs text-secondary-400 italic py-2">Belum ada data aktivitas</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Print Footer -->
            <div class="hidden print:block mt-8 text-center text-xs text-secondary-400">
                <p>Dicetak pada {{ now()->format('d M Y H:i') }} oleh {{ auth()->user()->name }}</p>
                <p>Azventory Management System - Laporan Stok & Inventaris</p>
            </div>

            <style>
                @media print {
                    @page { margin: 0.5cm; }
                    body { visibility: hidden; background: white; }
                    .print-safe, .print-safe * { visibility: visible; }
                    .max-w-7xl { max-width: none !important; margin: 0 !important; padding: 0 !important; }
                    
                    /* Hide Sidebar, Header, Buttons */
                    nav, header, form, button, .btn, .no-print { display: none !important; }
                    
                    /* Ensure Grid Layout works in print */
                    .grid { display: grid !important; }
                    .lg\:grid-cols-4 { grid-template-columns: repeat(4, 1fr) !important; }
                    .lg\:grid-cols-3 { grid-template-columns: repeat(3, 1fr) !important; }
                    .lg\:grid-cols-2 { grid-template-columns: repeat(2, 1fr) !important; }
                    .print-grid-3 { grid-template-columns: repeat(3, 1fr) !important; }

                    /* Make visible content absolute to top */
                    .py-6 > div {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        visibility: visible;
                    }
                    
                    .card { break-inside: avoid; border: 1px solid #ddd; box-shadow: none; }
                    .text-3xl { font-size: 1.5rem; } /* Scale down title */
                }
            </style>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart defaults for consistency
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';
        Chart.defaults.scale.grid.color = '#f1f5f9';

        // Stock Movement Chart (Line)
        const movementDataKey = @json($movementData);
        new Chart(document.getElementById('stockMovementChart'), {
            type: 'line',
            data: {
                labels: movementDataKey.labels,
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: movementDataKey.masuk,
                        borderColor: '#10b981', // Success
                        backgroundColor: 'rgba(16, 185, 129, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    },
                    {
                        label: 'Barang Keluar',
                        data: movementDataKey.keluar,
                        borderColor: '#ef4444', // Danger
                        backgroundColor: 'rgba(239, 68, 68, 0.1)',
                        borderWidth: 2,
                        tension: 0.3,
                        fill: true
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 2]
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // Donut Chart: Stock by Category
        const stockByCategoryData = @json($stockByCategory);
        const categoryLabels = Object.keys(stockByCategoryData);
        const categoryData = Object.values(stockByCategoryData);
        
        // Custom palette matching our theme
        const chartColors = [
            '#3b82f6', // primary-500
            '#ef4444', // danger-500
            '#f59e0b', // warning-500
            '#10b981', // success-500
            '#8b5cf6', // purple
            '#ec4899', // pink
            '#06b6d4', // cyan
        ];

        new Chart(document.getElementById('stockByCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Total Stok',
                    data: categoryData,
                    backgroundColor: chartColors,
                    borderWidth: 0,
                    hoverOffset: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'right',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: {
                                size: 12
                            }
                        }
                    }
                }
            }
        });

        // Bar Chart: Stock by Location
        const stockByLocationData = @json($stockByLocation);
        const locationLabels = Object.keys(stockByLocationData);
        const locationData = Object.values(stockByLocationData);

        new Chart(document.getElementById('stockByLocationChart'), {
            type: 'bar',
            data: {
                labels: locationLabels,
                datasets: [{
                    label: 'Total Stok',
                    data: locationData,
                    backgroundColor: '#3b82f6',
                    borderRadius: 4,
                    barThickness: 20,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            borderDash: [2, 2]
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
