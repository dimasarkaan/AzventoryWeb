part2 = r"""
            {{-- ================================================================
                 OVERDUE BANNER
                 ================================================================ --}}
            <!-- Skeleton Terlambat -->
            <div x-show="showOverdue && {{ $totalOverdueCount }} > 0 && isLoading" class="mb-6 animate-pulse">
                <div class="card border-l-4 border-danger-200">
                    <div class="card-header p-4 border-b border-gray-100 flex justify-between">
                        <div class="h-6 bg-gray-200 rounded w-64"></div>
                    </div>
                    <div class="p-4 space-y-3">
                        @for($i=0; $i<3; $i++)
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-20"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showOverdue && {{ $totalOverdueCount }} > 0 && !isLoading"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="mb-6">
                <div class="card bg-white shadow-lg transform hover:scale-[1.01] transition-all duration-300 border-none overflow-hidden">
                    <div class="card-header p-4 bg-gradient-to-r from-red-500 to-orange-600 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="font-bold text-white">{{ __('ui.attention_overdue') }} ({{ $totalOverdueCount }})</h3>
                        </div>
                        @if($totalOverdueCount > 0)
                            <a href="{{ route('inventory.index', ['filter' => 'overdue']) }}" class="text-xs text-white hover:text-red-100 font-bold underline decoration-white/50">{{ __('ui.view_all') }}</a>
                        @endif
                    </div>
                    <!-- Desktop table -->
                    <div class="overflow-x-auto md:block hidden">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-secondary-500 uppercase bg-secondary-50 border-b border-secondary-200">
                                <tr>
                                    <th class="px-6 py-3">{{ __('ui.borrower') }}</th>
                                    <th class="px-6 py-3">{{ __('ui.item') }}</th>
                                    <th class="px-6 py-3 text-center">{{ __('ui.due_date_short') }}</th>
                                    <th class="px-6 py-3 text-center">{{ __('ui.late') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100">
                                <template x-for="borrow in overdueBorrowingsList" :key="borrow.id">
                                    <tr class="hover:bg-secondary-50 cursor-pointer" @click="window.location.href = '/inventory/borrow/' + borrow.id">
                                        <td class="px-6 py-3 font-medium text-secondary-900" x-text="borrow.user_name || borrow.borrower_name"></td>
                                        <td class="px-6 py-3"><span x-text="borrow.sparepart_name"></span> (<span x-text="borrow.quantity"></span>)</td>
                                        <td class="px-6 py-3 text-center font-bold text-danger-600" x-text="borrow.due_date_formatted"></td>
                                        <td class="px-6 py-3 text-center text-danger-500" x-text="borrow.due_date_rel"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>
                    <!-- Mobile stacked -->
                    <div class="md:hidden divide-y divide-secondary-100">
                        <template x-for="borrow in overdueBorrowingsList" :key="borrow.id">
                            <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" @click="window.location.href = '/inventory/borrow/' + borrow.id">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-secondary-900" x-text="borrow.user_name || borrow.borrower_name"></div>
                                    <span class="text-xs font-bold text-danger-600 bg-danger-50 px-2 py-1 rounded-full" x-text="borrow.due_date_rel"></span>
                                </div>
                                <div class="text-sm text-secondary-600 mb-1"><span x-text="borrow.sparepart_name"></span> (<span x-text="borrow.quantity"></span> unit)</div>
                                <div class="text-xs text-secondary-500 flex items-center gap-1">
                                    <span>{{ __('ui.due_date_short') }}:</span>
                                    <span class="font-semibold text-danger-600" x-text="borrow.due_date_formatted"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 CHART PERGERAKAN STOK
                 ================================================================ --}}
            <div x-show="showMovement && isLoading" class="card mb-4 animate-pulse">
                <div class="card-header border-b border-gray-100 p-5">
                    <div class="h-5 bg-gray-200 rounded w-48 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-64"></div>
                </div>
                <div class="card-body p-6">
                    <div class="h-[250px] w-full bg-gray-100 rounded flex items-end justify-between px-4 pb-4 gap-2">
                        @for($i=0; $i<12; $i++)
                            <div class="w-full bg-gray-200 rounded-t" style="height: {{ rand(20, 80) }}%"></div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showMovement && !isLoading"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="card mb-4">
                <div class="card-header border-b border-secondary-100 p-5">
                    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                        <div>
                            <h3 class="font-bold text-secondary-900">{{ __('ui.stock_movement') }}</h3>
                            <p class="text-xs text-secondary-500">{{ __('ui.stock_movement_desc') }}</p>
                        </div>
                        {{-- KPI Summary Badges --}}
                        <div class="flex flex-wrap gap-2" id="movement-kpi-badges">
                            <div class="flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5">
                                <span class="w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0"></span>
                                <span class="text-xs text-emerald-700 font-medium whitespace-nowrap">Masuk: <span id="kpi-masuk" class="font-bold">0</span> unit</span>
                            </div>
                            <div class="flex items-center gap-1.5 bg-red-50 border border-red-200 rounded-lg px-3 py-1.5">
                                <span class="w-2 h-2 rounded-full bg-red-500 flex-shrink-0"></span>
                                <span class="text-xs text-red-700 font-medium whitespace-nowrap">Keluar: <span id="kpi-keluar" class="font-bold">0</span> unit</span>
                            </div>
                            <div class="flex items-center gap-1.5 bg-blue-50 border border-blue-200 rounded-lg px-3 py-1.5" id="kpi-net-badge">
                                <span class="w-2 h-2 rounded-full bg-blue-500 flex-shrink-0" id="kpi-net-dot"></span>
                                <span class="text-xs font-medium whitespace-nowrap" id="kpi-net-label">Net: <span id="kpi-net" class="font-bold">0</span></span>
                            </div>
                        </div>
                        {{-- Quick-filter Periode Widget --}}
                        <div class="flex items-center gap-1 bg-secondary-100 rounded-lg p-0.5" id="movement-range-btns">
                            <button onclick="fetchMovementData(7)" id="mov-btn-7"
                                    class="mov-range-btn px-2.5 py-1 rounded-md text-xs font-medium transition-all bg-white shadow-sm text-primary-700">7 Hari</button>
                            <button onclick="fetchMovementData(30)" id="mov-btn-30"
                                    class="mov-range-btn px-2.5 py-1 rounded-md text-xs font-medium transition-all text-secondary-600 hover:bg-white/70">30 Hari</button>
                            <button onclick="fetchMovementData(90)" id="mov-btn-90"
                                    class="mov-range-btn px-2.5 py-1 rounded-md text-xs font-medium transition-all text-secondary-600 hover:bg-white/70">3 Bulan</button>
                        </div>
                    </div>
                </div>
                <div class="card-body p-4 md:p-6">
                    <div class="min-h-[200px] md:h-[280px] w-full">
                        <canvas id="stockMovementChart"></canvas>
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 TOP ITEMS: KELUAR & MASUK
                 ================================================================ --}}
            <div x-show="showTopItems && isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4 animate-pulse">
                <div class="card">
                    <div class="card-header border-b border-gray-100 p-4"><div class="h-4 bg-gray-200 rounded w-32"></div></div>
                    <div class="p-4 space-y-3">
                        @for($i=0; $i<5; $i++)
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-8"></div>
                            </div>
                        @endfor
                    </div>
                </div>
                <div class="card">
                    <div class="card-header border-b border-gray-100 p-4"><div class="h-4 bg-gray-200 rounded w-32"></div></div>
                    <div class="p-4 space-y-3">
                        @for($i=0; $i<5; $i++)
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-8"></div>
                            </div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showTopItems && !isLoading"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">

                {{-- Barang Sering Keluar --}}
                <div class="card flex flex-col overflow-hidden">
                    <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-danger-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900">{{ __('ui.top_exiting_items') }}</h3>
                            <p class="text-xs text-secondary-400">Berdasarkan periode yang dipilih</p>
                        </div>
                    </div>
                    <div class="flex-grow divide-y divide-secondary-100">
                        <template x-for="(item, index) in topExited" :key="item.sparepart_id">
                            <div class="group flex items-center gap-4 px-6 py-3.5 hover:bg-danger-50/40 transition-all duration-150 cursor-pointer"
                                 @click="window.location.href = '/inventory/' + item.sparepart_id">
                                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold transition-transform duration-200 group-hover:scale-110"
                                     :class="{
                                        'bg-amber-100 text-amber-700 shadow shadow-amber-200':  index === 0,
                                        'bg-slate-100  text-slate-600  shadow shadow-slate-200': index === 1,
                                        'bg-orange-100 text-orange-700 shadow shadow-orange-200': index === 2,
                                        'bg-secondary-100 text-secondary-500': index > 2
                                     }"
                                     x-text="index + 1"></div>
                                <span class="flex-grow font-semibold text-secondary-800 text-sm group-hover:text-primary-700 transition-colors truncate" x-text="item.sparepart_name || 'Unknown'"></span>
                                <span class="flex-shrink-0 font-bold text-sm text-danger-600 tabular-nums" x-text="'- ' + parseInt(item.total_qty).toLocaleString('id-ID')"></span>
                            </div>
                        </template>
                        <div x-show="topExited.length === 0" class="px-6 py-10 text-center text-secondary-400">
                            <svg class="w-8 h-8 mx-auto text-secondary-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                            <p class="text-sm italic">{{ __('ui.no_data') }}</p>
                        </div>
                    </div>
                </div>

                {{-- Barang Sering Masuk — emerald border + pill badge --}}
                <div class="card flex flex-col overflow-hidden border-l-4 border-emerald-400">
                    <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900">{{ __('ui.top_entering_items') }}</h3>
                            <p class="text-xs text-secondary-400">Berdasarkan periode yang dipilih</p>
                        </div>
                    </div>
                    <div class="flex-grow divide-y divide-secondary-100">
                        @forelse($topEntered as $item)
                            @php
                                $rank = $loop->iteration;
                                $badgeClass = match(true) {
                                    $rank === 1 => 'bg-amber-100 text-amber-700 shadow shadow-amber-200',
                                    $rank === 2 => 'bg-slate-100  text-slate-600 shadow shadow-slate-200',
                                    $rank === 3 => 'bg-orange-100 text-orange-700 shadow shadow-orange-200',
                                    default     => 'bg-secondary-100 text-secondary-500',
                                };
                            @endphp
                            <div class="group flex items-center gap-4 px-6 py-3.5 hover:bg-emerald-50/50 transition-all duration-150 cursor-pointer"
                                 onclick="window.location.href='{{ route('inventory.show', $item->sparepart_id) }}'">
                                <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold transition-transform duration-200 group-hover:scale-110 {{ $badgeClass }}">
                                    {{ $rank }}
                                </div>
                                <span class="flex-grow font-semibold text-secondary-800 text-sm group-hover:text-primary-700 transition-colors truncate">{{ $item->sparepart_name ?? 'Unknown' }}</span>
                                <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs tabular-nums">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                    {{ number_format($item->total_qty, 0, ',', '.') }}
                                </span>
                            </div>
                        @empty
                            <div class="px-6 py-10 text-center text-secondary-400">
                                <svg class="w-8 h-8 mx-auto text-secondary-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                <p class="text-sm italic">{{ __('ui.no_data') }}</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            {{-- ================================================================
                 CHART DISTRIBUSI STOK (DONUT + BAR)
                 ================================================================ --}}
            <div x-show="showCharts && isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4 animate-pulse">
                <div class="card flex flex-col h-[400px]">
                    <div class="card-header border-b border-gray-100 p-5"><div class="h-5 bg-gray-200 rounded w-48"></div></div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center">
                        <div class="w-48 h-48 rounded-full border-8 border-gray-200"></div>
                    </div>
                </div>
                <div class="card flex flex-col h-[400px]">
                    <div class="card-header border-b border-gray-100 p-5"><div class="h-5 bg-gray-200 rounded w-32"></div></div>
                    <div class="card-body p-6 flex-grow flex items-end justify-around gap-2 px-10">
                        @for($i=0; $i<6; $i++)
                            <div class="w-12 bg-gray-200 rounded-t" style="height: {{ rand(30, 90) }}%"></div>
                        @endfor
                    </div>
                </div>
            </div>

            <div x-show="showCharts && !isLoading"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 translate-y-4"
                 class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                <!-- Grafik Donut: Kategori -->
                <div class="card flex flex-col">
                    <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900">{{ __('ui.stock_distribution_category') }}</h3>
                    </div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center bg-white min-h-[300px]">
                        <div class="w-full h-full max-h-[300px]">
                            <canvas id="stockByCategoryChart"></canvas>
                        </div>
                    </div>
                </div>
                <!-- Grafik Batang: Lokasi -->
                <div class="card flex flex-col">
                    <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900">{{ __('ui.stock_location') }}</h3>
                    </div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center bg-white min-h-[300px]">
                        <div class="w-full h-full max-h-[300px]">
                            <canvas id="stockByLocationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
"""

with open('resources/views/dashboard/admin.blade.php', 'a', encoding='utf-8') as f:
    f.write(part2)

print('Part 2 appended OK')
