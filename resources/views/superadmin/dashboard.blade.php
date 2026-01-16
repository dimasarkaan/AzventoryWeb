<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header Section -->
            <div class="mb-4 flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">Dashboard</h1>
                    <p class="mt-1 text-sm text-secondary-500">Ringkasan aktivitas dan status inventaris Anda.</p>
                </div>
                <div class="flex items-center gap-3">
                    <button class="btn btn-secondary flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                        <span>Unduh Laporan</span>
                    </button>
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

            <!-- Stats Grid -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-4">
                <!-- Total Sparepart -->
                <div class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-primary-50 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-primary-100"></div>
                    <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative">Total Sparepart</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative">{{ number_format($totalSpareparts) }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-primary-600 z-10 relative">
                        <div class="p-2 bg-primary-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-primary-50 text-primary-700 px-2 py-0.5 rounded-full">Item Terdaftar</span>
                    </div>
                </div>

                <!-- Total Stok -->
                <div class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                     <div class="absolute right-0 top-0 h-24 w-24 bg-success-50 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-success-100"></div>
                    <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative">Total Stok Unit</p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative">{{ number_format($totalStock) }}</h3>
                    </div>
                    <div class="mt-4 flex items-center text-success-600 z-10 relative">
                        <div class="p-2 bg-success-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                         <span class="ml-2 text-xs font-semibold bg-success-50 text-success-700 px-2 py-0.5 rounded-full">Siap Pakai</span>
                    </div>
                </div>

                <!-- Jumlah Kategori -->
                <div class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                     <div class="absolute right-0 top-0 h-24 w-24 bg-warning-50 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-warning-100"></div>
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

                <!-- Jumlah Lokasi -->
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

            <!-- Charts Section -->
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <!-- Donut Chart -->
                <div class="card flex flex-col">
                    <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900">Distribusi Stok per Kategori</h3>
                         <button class="text-secondary-400 hover:text-primary-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                        </button>
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
                        <button class="text-secondary-400 hover:text-primary-600 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z"></path></svg>
                        </button>
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
                <div class="lg:col-span-2 card">
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
                                    <tr class="bg-white hover:bg-secondary-50 transition-colors">
                                        <td class="px-6 py-4 font-medium text-secondary-900">{{ $item->name }}</td>
                                        <td class="px-6 py-4 hidden md:table-cell">{{ $item->category->name ?? '-' }}</td>
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

                <!-- Recent Activities (1 col) -->
                <div class="card p-0 flex flex-col h-full">
                     <div class="card-header p-5 border-b border-secondary-100 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900">Aktivitas Terbaru</h3>
                         <button class="text-secondary-400 hover:text-primary-600 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>
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
                    @if($recentActivities->count() > 0)
                        <div class="p-4 border-t border-secondary-100 bg-secondary-50 rounded-b-xl text-center">
                            <a href="{{ route('superadmin.activity-logs.index') }}" class="text-xs font-semibold text-primary-600 hover:text-primary-800 uppercase tracking-wide">Lihat Semua Log</a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Chart defaults for consistency
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';
        Chart.defaults.scale.grid.color = '#f1f5f9';

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
