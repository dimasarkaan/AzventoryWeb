part3 = r"""
            {{-- ================================================================
                 BAGIAN BAWAH: LOW STOCK + AKTIVITAS TERBARU
                 ================================================================ --}}
            <!-- Skeleton Low Stock -->
            <div x-show="showLowStock && isLoading"
                 class="card animate-pulse h-[400px]"
                 :class="{ 'lg:col-span-3': !showRecent, 'lg:col-span-2': showRecent }">
                <div class="card-header p-5 border-b border-gray-100 flex justify-between">
                    <div class="h-5 bg-gray-200 rounded w-48"></div>
                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex gap-4 mb-4">
                        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                        <div class="h-4 bg-gray-200 rounded w-1/4"></div>
                    </div>
                    @for($i=0; $i<5; $i++)
                        <div class="h-10 bg-gray-100 rounded w-full"></div>
                    @endfor
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Item Stok Rendah -->
                <div x-show="showLowStock && !isLoading"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-4"
                     class="card bg-white shadow-lg transform hover:scale-[1.01] transition-all duration-300 border-none overflow-hidden"
                     :class="{ 'lg:col-span-3': !showRecent, 'lg:col-span-2': showRecent }">
                    <div class="card-header p-5 bg-gradient-to-r from-amber-500 to-orange-500 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <div class="p-1.5 bg-white/20 text-white rounded-lg backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            </div>
                            <h3 class="font-bold text-white">{{ __('ui.warning_low_stock') }}</h3>
                        </div>
                        <a href="{{ route('inventory.index', ['filter' => 'low_stock']) }}" class="text-sm text-white hover:text-amber-100 font-medium underline decoration-white/50">{{ __('ui.view_all') }}</a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-secondary-500">
                            <thead class="text-xs text-secondary-700 uppercase bg-secondary-50 border-b border-secondary-200">
                                <tr>
                                    <th class="px-6 py-3 font-semibold tracking-wider">{{ __('ui.item') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider hidden md:table-cell">{{ __('ui.categories') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center">{{ __('ui.stock') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center hidden md:table-cell">{{ __('ui.min_stock') }}</th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center">{{ __('ui.status') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100">
                                <template x-for="item in lowStockItems" :key="item.id">
                                    <tr class="bg-white hover:bg-secondary-50 transition-colors cursor-pointer"
                                        @click="window.location.href = '/inventory/' + item.id">
                                        <td class="px-4 py-3 font-medium text-secondary-800" x-text="item.name || 'Unknown'"></td>
                                        <td class="px-6 py-4 hidden md:table-cell" x-text="item.category || '-'"></td>
                                        <td class="px-6 py-4 text-center font-bold text-danger-600" x-text="item.stock"></td>
                                        <td class="px-6 py-4 text-center text-secondary-600 hidden md:table-cell" x-text="item.minimum_stock"></td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="badge badge-danger" x-show="item.stock == 0">{{ __('ui.status_out_of_stock') }}</span>
                                            <span class="badge badge-warning" x-show="item.stock > 0">{{ __('ui.status_critical') }}</span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="lowStockItems.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-secondary-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-success-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p>{{ __('ui.all_stock_safe') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Skeleton Aktivitas Terkini -->
                <div x-show="showRecent && isLoading" class="card lg:col-span-1 animate-pulse h-[400px]">
                    <div class="card-header p-5 border-b border-gray-100 flex justify-between">
                        <div class="h-5 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="p-5 space-y-4">
                        @for($i=0; $i<5; $i++)
                            <div class="flex gap-4">
                                <div class="h-8 w-8 bg-gray-200 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-3 bg-gray-200 rounded w-full"></div>
                                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                </div>
                            </div>
                        @endfor
                    </div>
                </div>

                <!-- Aktivitas Terkini -->
                <div x-show="showRecent && !isLoading"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-4"
                     class="card p-0 flex flex-col h-full print-safe"
                     :class="{ 'lg:col-span-3': !showLowStock, 'lg:col-span-1': showLowStock }">
                    <div class="card-header p-5 border-b border-secondary-100 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900">{{ __('ui.recent_activities') }}</h3>
                        {{-- Admin tidak bisa akses route activity-logs (superadmin only) --}}
                        <span class="text-xs text-secondary-400">{{ count($recentActivities ?? []) }} {{ __('ui.recent_short') }}</span>
                    </div>
                    <div class="card-body p-0 overflow-y-auto max-h-[500px] custom-scrollbar">
                        <div class="flex flex-col">
                            <template x-for="log in recentActivities" :key="log.id">
                                <div class="px-5 py-4 hover:bg-secondary-50 transition-colors group">
                                    <div class="flex gap-4">
                                        <div class="flex-shrink-0 mt-1">
                                            <div class="w-8 h-8 rounded-full bg-primary-100 flex items-center justify-center text-primary-600 group-hover:bg-primary-600 group-hover:text-white transition-all ring-2 ring-white">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            </div>
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <p class="text-sm font-medium text-secondary-900 line-clamp-2" x-text="log.description"></p>
                                            <div class="flex items-center gap-2 mt-1">
                                                <p class="text-xs text-secondary-500 font-semibold" x-text="log.user_name || log.user?.name || 'Sistem'"></p>
                                                <span class="text-secondary-300">&bull;</span>
                                                <p class="text-xs text-secondary-400" x-text="log.created_at_diff"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </template>
                            <div x-show="recentActivities.length === 0" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-secondary-100 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-secondary-700">Belum ada aktivitas</p>
                                        <p class="text-xs text-secondary-400 mt-1">{{ __('ui.no_recent_activities') }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 DEAD STOCK + LEADERBOARD
                 ================================================================ --}}
            <!-- Skeleton Analytics -->
            <div x-show="showStats && isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4 animate-pulse">
                <div class="card h-[300px]">
                    <div class="card-header p-4 border-b border-gray-100"><div class="h-4 bg-gray-200 rounded w-32"></div></div>
                    <div class="p-4 space-y-3">
                        @for($i=0; $i<5; $i++)
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-10"></div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="card h-[300px]">
                    <div class="card-header p-4 border-b border-gray-100"><div class="h-4 bg-gray-200 rounded w-40"></div></div>
                    <div class="p-4 space-y-3">
                        @for($i=0; $i<5; $i++)
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2 w-2/3">
                                    <div class="h-6 w-6 rounded-full bg-gray-200"></div>
                                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                                </div>
                                <div class="h-4 bg-gray-200 rounded w-16"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showStats && !isLoading"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4 print-grid-3">

                {{-- Widget Stok Mati --}}
                <div x-show="showDeadStock" x-transition class="card flex flex-col overflow-hidden border-l-4 border-amber-400">
                    <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900">{{ __('ui.dead_stock_title') }}</h3>
                            <p class="text-xs text-secondary-400">Barang tidak bergerak dalam periode ini</p>
                        </div>
                    </div>
                    <div class="flex-grow">
                        <template x-for="(item, index) in deadStockItems" :key="item.id">
                            <div class="group flex items-center gap-4 px-6 py-3.5 transition-all duration-150 cursor-pointer"
                                 :class="index % 2 === 0 ? 'bg-white hover:bg-amber-50/50' : 'bg-secondary-50/50 hover:bg-amber-50/50'"
                                 @click="window.location.href = '/inventory/' + item.id">
                                <div class="flex-shrink-0 w-2 h-2 rounded-full bg-amber-400"></div>
                                <span class="flex-grow font-semibold text-secondary-800 text-sm group-hover:text-primary-700 transition-colors truncate" x-text="item.name"></span>
                                <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-amber-100 text-amber-700 font-bold text-xs tabular-nums">
                                    <span x-text="item.stock"></span>&nbsp;{{ __('ui.units') }}
                                </span>
                            </div>
                        </template>
                        <div x-show="deadStockItems.length === 0" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-success-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                <p class="text-sm font-medium text-secondary-700">Semua barang bergerak aktif</p>
                                <p class="text-xs text-secondary-400">Tidak ada barang yang stagnan dalam periode ini</p>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Papan Peringkat (Leaderboard) --}}
                <div x-show="showLeaderboard" x-transition class="card flex flex-col overflow-hidden border-l-4 border-success-400">
                    <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-success-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900">{{ __('ui.top_contributors_title') }}</h3>
                            <p class="text-xs text-secondary-400">Pengguna paling aktif dalam periode ini</p>
                        </div>
                    </div>
                    <div class="flex-grow divide-y divide-secondary-100">
                        <template x-for="(userLog, index) in activeUsers" :key="userLog.user_id || Math.random()">
                            <div class="group flex items-center gap-4 px-6 py-3.5 hover:bg-success-50/40 transition-all duration-150">
                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-sm font-bold transition-transform duration-200 group-hover:scale-110"
                                     :class="{
                                        'bg-amber-100 text-amber-700 ring-2 ring-amber-300':  index === 0,
                                        'bg-slate-100  text-slate-600  ring-2 ring-slate-300': index === 1,
                                        'bg-orange-100 text-orange-700 ring-2 ring-orange-300': index === 2,
                                        'bg-success-100 text-success-700': index > 2
                                     }"
                                     x-text="userLog.user ? userLog.user.name.charAt(0).toUpperCase() : '?'"></div>
                                <span class="flex-grow font-semibold text-secondary-800 text-sm group-hover:text-primary-700 transition-colors truncate" x-text="userLog.user ? userLog.user.name : 'Unknown'"></span>
                                <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-success-100 text-success-700 font-bold text-xs tabular-nums">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                    <span x-text="userLog.total_actions"></span> {{ __('ui.actions_count') }}
                                </span>
                            </div>
                        </template>
                        <div x-show="activeUsers.length === 0" class="px-6 py-10 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-10 h-10 text-secondary-200" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <p class="text-sm font-medium text-secondary-600">Belum ada aktivitas</p>
                                <p class="text-xs text-secondary-400">Data akan muncul setelah ada transaksi stok</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Footer Cetak -->
            <div class="hidden print:block mt-8 text-center text-xs text-secondary-400">
                <p>{{ __('ui.printed_at') }} {{ now()->format('d M Y H:i') }} {{ __('ui.by') }} {{ auth()->user()->name }}</p>
                <p>Azventory Management System &mdash; {{ __('ui.stock_inventory_report') }}</p>
            </div>

            <style>
                @media print {
                    @page { margin: 0.5cm; }
                    body { visibility: hidden; background: white; }
                    .print-safe, .print-safe * { visibility: visible; }
                    .max-w-7xl { max-width: none !important; margin: 0 !important; padding: 0 !important; }
                    nav, header, form, button, .btn, .no-print { display: none !important; }
                    .grid { display: grid !important; }
                    .lg\\:grid-cols-4 { grid-template-columns: repeat(4, 1fr) !important; }
                    .lg\\:grid-cols-3 { grid-template-columns: repeat(3, 1fr) !important; }
                    .lg\\:grid-cols-2 { grid-template-columns: repeat(2, 1fr) !important; }
                    .print-grid-3 { grid-template-columns: repeat(3, 1fr) !important; }
                    .py-6 > div { position: absolute; top: 0; left: 0; width: 100%; visibility: visible; }
                    .card { break-inside: avoid; border: 1px solid #ddd; box-shadow: none; }
                    .text-3xl { font-size: 1.5rem; }
                }
            </style>
        </div>
    </div>
"""

with open('resources/views/dashboard/admin.blade.php', 'a', encoding='utf-8') as f:
    f.write(part3)

print('Part 3 OK')
