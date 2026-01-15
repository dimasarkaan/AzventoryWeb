<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dasbor Super Admin') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <!-- Total Sparepart -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-500">Total Sparepart</h3>
                    <p class="text-3xl font-bold mt-2">{{ $totalSpareparts }}</p>
                </x-card>

                <!-- Total Stok -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-500">Total Stok</h3>
                    <p class="text-3xl font-bold mt-2">{{ $totalStock }}</p>
                </x-card>

                <!-- Jumlah Kategori -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-500">Jumlah Kategori</h3>
                    <p class="text-3xl font-bold mt-2">{{ $totalCategories }}</p>
                </x-card>

                <!-- Jumlah Lokasi -->
                <x-card>
                    <h3 class="text-lg font-semibold text-gray-500">Jumlah Lokasi</h3>
                    <p class="text-3xl font-bold mt-2">{{ $totalLocations }}</p>
                </x-card>

                <!-- Persetujuan Tertunda -->
                <a href="{{ route('superadmin.stock-approvals.index') }}" class="block hover:bg-gray-50">
                    <x-card>
                        <h3 class="text-lg font-semibold text-gray-500">Persetujuan Tertunda</h3>
                        <p class="text-3xl font-bold mt-2 text-orange-500">{{ $pendingApprovalsCount }}</p>
                    </x-card>
                </a>
            </div>

            <!-- Charts Section -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Donut Chart -->
                <x-card>
                    <x-slot name="header">Distribusi Stok per Kategori</x-slot>
                    <canvas id="stockByCategoryChart"></canvas>
                </x-card>

                <!-- Bar Chart -->
                <x-card>
                    <x-slot name="header">Stok per Lokasi</x-slot>
                    <canvas id="stockByLocationChart"></canvas>
                </x-card>
            </div>

            <!-- New Sections: Low Stock and Recent Activities -->
            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-6">
                <!-- Low Stock Items -->
                <x-card>
                    <x-slot name="header">Stok Menipis</x-slot>
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Sparepart</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Stok</th>
                                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase">Min. Stok</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @forelse ($lowStockItems as $item)
                                    <tr>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm font-medium text-gray-900">{{ $item->name }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-red-600 font-bold">{{ $item->stock }}</td>
                                        <td class="px-4 py-2 whitespace-nowrap text-sm text-gray-500">{{ $item->minimum_stock }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="px-4 py-2 text-sm text-gray-500 text-center">Semua stok aman.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </x-card>

                <!-- Recent Activities -->
                <x-card>
                    <x-slot name="header">Aktivitas Terbaru</x-slot>
                    <div class="space-y-4">
                        @forelse ($recentActivities as $log)
                            <div class="flex items-start">
                                <div class="flex-shrink-0">
                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full bg-blue-100 text-blue-800">
                                        <svg class="h-5 w-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd" /></svg>
                                    </span>
                                </div>
                                <div class="ms-3">
                                    <p class="text-sm text-gray-700">{{ $log->description }}</p>
                                    <p class="text-xs text-gray-500">{{ $log->user->name ?? 'Sistem' }} &bull; {{ $log->created_at->diffForHumans() }}</p>
                                </div>
                            </div>
                        @empty
                            <p class="text-sm text-gray-500">Tidak ada aktivitas terbaru.</p>
                        @endforelse
                    </div>
                </x-card>
            </div>
        </div>
    </div>

    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Donut Chart: Stock by Category
        const stockByCategoryData = @json($stockByCategory);
        const categoryLabels = Object.keys(stockByCategoryData);
        const categoryData = Object.values(stockByCategoryData);

        new Chart(document.getElementById('stockByCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    label: 'Total Stok',
                    data: categoryData,
                    backgroundColor: [
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(239, 68, 68, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(139, 92, 246, 0.8)',
                    ],
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
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
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
    @endpush
</x-app-layout>
