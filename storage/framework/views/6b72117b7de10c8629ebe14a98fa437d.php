<?php if (isset($component)) { $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54 = $attributes; } ?>
<?php $component = App\View\Components\AppLayout::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('app-layout'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\AppLayout::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes([]); ?>
<script>
    (function () {
        const STORAGE_KEY = 'dashboard_period';
        const params = new URLSearchParams(window.location.search);

        // Jika URL tidak punya ?period=, coba restore dari sessionStorage
        if (!params.has('period')) {
            const saved = sessionStorage.getItem(STORAGE_KEY);
            if (saved && saved !== 'today') {
                // Redirect ke URL yang sama + ?period=saved
                params.set('period', saved);
                window.location.replace(window.location.pathname + '?' + params.toString());
            }
        } else {
            // Simpan periode saat ini ke sessionStorage
            sessionStorage.setItem(STORAGE_KEY, params.get('period'));
        }

        // Fungsi global untuk dipanggil saat klik tab
        window.savePeriod = function (key) {
            sessionStorage.setItem(STORAGE_KEY, key);
        };

        // Fungsi global untuk dipakai tombol logout
        window.clearDashboardPeriod = function () {
            sessionStorage.removeItem(STORAGE_KEY);
        };
    })();
</script>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" 
             x-data="dashboardData()">
             
            
            <div class="mb-6">
                
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3 mb-4">
                    <div>
                        <h1 class="text-3xl font-bold text-secondary-900 tracking-tight"><?php echo e(__('ui.dashboard')); ?></h1>
                        <p class="mt-1 text-sm text-secondary-500"><?php echo e(__('ui.dashboard_desc')); ?></p>
                    </div>

                    <div class="flex items-center gap-2 flex-shrink-0">
                        
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                    class="btn btn-secondary flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                <span class="hidden sm:inline"><?php echo e(__('ui.display_settings')); ?></span>
                            </button>
                            <div x-show="open" x-transition
                                 class="absolute left-0 sm:left-auto sm:right-0 mt-2 w-56 bg-white rounded-xl shadow-xl py-1 z-50 border border-secondary-100 max-h-[80vh] overflow-y-auto">
                                <div class="px-4 py-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider"><?php echo e(__('ui.active_widgets')); ?></div>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showStats" @change="toggle('showStats')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700"><?php echo e(__('ui.widget_main_stats')); ?></span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showCharts" @change="toggle('showCharts')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700"><?php echo e(__('ui.widget_distribution_location')); ?></span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showLowStock" @change="toggle('showLowStock')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700"><?php echo e(__('ui.widget_stock_alerts')); ?></span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showBorrowings" @change="toggle('showBorrowings')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700"><?php echo e(__('ui.widget_active_borrowings')); ?></span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showOverdue" @change="toggle('showOverdue')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700"><?php echo e(__('ui.widget_overdue')); ?></span>
                                </label>
                                <div class="border-t border-secondary-100 my-1"></div>
                                <div class="px-4 py-2 text-xs font-semibold text-secondary-400 uppercase tracking-wider"><?php echo e(__('ui.widget_analytics')); ?></div>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showMovement" @change="toggle('showMovement')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700"><?php echo e(__('ui.widget_stock_movement')); ?></span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showTopItems" @change="toggle('showTopItems')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700"><?php echo e(__('ui.widget_popular_items')); ?></span>
                                </label>

                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showDeadStock" @change="toggle('showDeadStock')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700"><?php echo e(__('ui.widget_dead_stock')); ?></span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showLeaderboard" @change="toggle('showLeaderboard')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700"><?php echo e(__('ui.widget_top_contributors')); ?></span>
                                </label>
                                <label class="flex items-center px-4 py-2 hover:bg-secondary-50 cursor-pointer">
                                    <input type="checkbox" :checked="showRecent" @change="toggle('showRecent')" class="rounded border-secondary-300 text-primary-600 shadow-sm">
                                    <span class="ml-2 text-sm text-secondary-700"><?php echo e(__('ui.widget_recent_activity')); ?></span>
                                </label>
                            </div>
                        </div>

                        
                        <div x-data="{ open: false }" class="relative">
                            <button @click="open = !open" @click.away="open = false"
                                    class="btn btn-secondary flex items-center gap-2 text-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                                </svg>
                                <span class="hidden sm:inline">Ekspor</span>
                            </button>
                            <div x-show="open" x-transition
                                 class="absolute right-0 mt-2 w-44 bg-white rounded-xl shadow-xl py-1 z-50 border border-secondary-100">
                                <button onclick="exportDashboardPDF()" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"/></svg>
                                    Cetak / PDF
                                </button>
                                <button onclick="exportDashboardPNG()" class="w-full text-left flex items-center gap-2 px-4 py-2.5 text-sm text-secondary-700 hover:bg-primary-50 hover:text-primary-700 transition-colors">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                                    Simpan sebagai PNG
                                </button>
                            </div>
                        </div>

                        
                        <a href="<?php echo e(route('inventory.stock-approvals.index')); ?>" class="btn btn-primary flex items-center gap-2 relative">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"/></svg>
                            <span class="hidden sm:inline"><?php echo e(__('ui.approvals')); ?></span>
                            <?php if($pendingApprovalsCount > 0): ?>
                                <span class="absolute -top-1 -right-1 flex h-3 w-3">
                                    <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-danger-400 opacity-75"></span>
                                    <span class="relative inline-flex rounded-full h-3 w-3 bg-danger-500"></span>
                                </span>
                            <?php endif; ?>
                        </a>
                    </div>
                </div>

                
                <?php
                    $activePeriod = $period ?? 'today';
                    $tabDefs = [
                        'today'      => 'Hari Ini',
                        'this_week'  => 'Minggu Ini',
                        'this_month' => 'Bulan Ini',
                        'this_year'  => 'Tahun Ini',
                    ];
                ?>
                
                
                <div x-data="globalPeriodFilter()" class="flex flex-col gap-2">
                    
                    <div class="flex flex-wrap items-center gap-1 bg-secondary-100/60 rounded-xl p-1.5">
                        <?php $__currentLoopData = $tabDefs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <a href="<?php echo e(route('dashboard.superadmin', ['period' => $key])); ?>"
                               onclick="savePeriod('<?php echo e($key); ?>')"
                               class="px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-150 whitespace-nowrap
                                      <?php echo e($activePeriod === $key
                                          ? 'bg-white text-primary-700 shadow-sm font-semibold ring-1 ring-secondary-200'
                                          : 'text-secondary-600 hover:text-secondary-900 hover:bg-white/60'); ?>">
                                <?php echo e($label); ?>

                            </a>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

                        
                        <button @click="showCustom = !showCustom"
                                class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-sm font-medium transition-all duration-150 whitespace-nowrap
                                       <?php echo e(in_array($activePeriod, ['custom','custom_year'])
                                           ? 'bg-white text-primary-700 shadow-sm font-semibold ring-1 ring-secondary-200'
                                           : 'text-secondary-600 hover:text-secondary-900 hover:bg-white/60'); ?>">
                            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                            </svg>
                            <span>Custom</span>
                            <?php if(in_array($activePeriod, ['custom','custom_year'])): ?>
                                <span class="text-xs text-secondary-500">
                                    (<?php echo e($year); ?><?php echo e(isset($month) && $month !== 'all' ? '/' . str_pad($month,2,'0',STR_PAD_LEFT) : ''); ?>)
                                </span>
                            <?php endif; ?>
                        </button>

                        
                        <span class="ml-auto text-xs text-secondary-400 hidden sm:block">
                            Data: <?php echo e(\Carbon\Carbon::parse($start)->format('d M Y')); ?> — <?php echo e(\Carbon\Carbon::parse($end)->format('d M Y')); ?>

                        </span>
                    </div>

                    
                    <div x-show="showCustom"
                         x-transition:enter="transition ease-out duration-200"
                         x-transition:enter-start="opacity-0 -translate-y-2"
                         x-transition:enter-end="opacity-100 translate-y-0"
                         x-cloak>
                        <form method="GET" action="<?php echo e(route('dashboard.superadmin')); ?>"
                              class="bg-white border border-secondary-200 rounded-xl p-4 flex flex-wrap items-end gap-3 shadow-sm">
                            <input type="hidden" name="period" value="custom">

                            
                            <div x-data="{
                                    open: false,
                                    selected: '<?php echo e($year ?? now()->year); ?>',
                                    options: [<?php echo e(implode(',', range(now()->year, now()->year - 5))); ?>]
                                }" class="relative">
                                <label class="block text-xs font-semibold text-secondary-600 mb-1">Tahun</label>
                                <input type="hidden" name="year" :value="selected">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                        class="flex items-center justify-between gap-2 w-28 px-3 py-2 text-sm bg-white border border-secondary-300 rounded-lg text-secondary-800 hover:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200 transition-all">
                                    <span x-text="selected"></span>
                                    <svg class="w-4 h-4 text-secondary-400 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition
                                     class="absolute z-50 mt-1 w-28 bg-white border border-secondary-200 rounded-lg shadow-lg py-1 max-h-48 overflow-y-auto">
                                    <template x-for="opt in options" :key="opt">
                                        <button type="button"
                                                @click="selected = opt; open = false"
                                                class="w-full text-left px-3 py-2 text-sm hover:bg-primary-50 hover:text-primary-700 transition-colors"
                                                :class="selected == opt ? 'text-primary-700 bg-primary-50 font-semibold' : 'text-secondary-700'"
                                                x-text="opt">
                                        </button>
                                    </template>
                                </div>
                            </div>

                            
                            <?php
                                $bulanList = [
                                    '' => 'Semua Bulan',
                                    '1' => 'Januari', '2' => 'Februari', '3' => 'Maret',
                                    '4' => 'April', '5' => 'Mei', '6' => 'Juni',
                                    '7' => 'Juli', '8' => 'Agustus', '9' => 'September',
                                    '10' => 'Oktober', '11' => 'November', '12' => 'Desember',
                                ];
                                $activeMonthLabel = $bulanList[$month ?? ''] ?? 'Semua Bulan';
                            ?>
                            <div x-data="{
                                    open: false,
                                    selectedVal: '<?php echo e($month ?? ''); ?>',
                                    selectedLabel: '<?php echo e($activeMonthLabel); ?>',
                                    options: [
                                        { val: '', label: 'Semua Bulan' },
                                        { val: '1', label: 'Januari' }, { val: '2', label: 'Februari' },
                                        { val: '3', label: 'Maret' }, { val: '4', label: 'April' },
                                        { val: '5', label: 'Mei' }, { val: '6', label: 'Juni' },
                                        { val: '7', label: 'Juli' }, { val: '8', label: 'Agustus' },
                                        { val: '9', label: 'September' }, { val: '10', label: 'Oktober' },
                                        { val: '11', label: 'November' }, { val: '12', label: 'Desember' },
                                    ]
                                }" class="relative">
                                <label class="block text-xs font-semibold text-secondary-600 mb-1">Bulan</label>
                                <input type="hidden" name="month" :value="selectedVal">
                                <button type="button" @click="open = !open" @click.away="open = false"
                                        class="flex items-center justify-between gap-2 w-40 px-3 py-2 text-sm bg-white border border-secondary-300 rounded-lg text-secondary-800 hover:border-primary-400 focus:outline-none focus:ring-2 focus:ring-primary-200 transition-all">
                                    <span x-text="selectedLabel" class="truncate"></span>
                                    <svg class="w-4 h-4 text-secondary-400 transition-transform flex-shrink-0" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                    </svg>
                                </button>
                                <div x-show="open" x-transition
                                     class="absolute z-50 mt-1 w-40 bg-white border border-secondary-200 rounded-lg shadow-lg py-1 max-h-60 overflow-y-auto">
                                    <template x-for="opt in options" :key="opt.val">
                                        <button type="button"
                                                @click="selectedVal = opt.val; selectedLabel = opt.label; open = false"
                                                class="w-full text-left px-3 py-2 text-sm hover:bg-primary-50 hover:text-primary-700 transition-colors"
                                                :class="selectedVal === opt.val ? 'text-primary-700 bg-primary-50 font-semibold' : 'text-secondary-700'"
                                                x-text="opt.label">
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <button type="submit" class="btn btn-primary text-sm">Terapkan</button>
                            <a href="<?php echo e(route('dashboard.superadmin')); ?>" class="btn btn-secondary text-sm">Reset</a>
                        </form>
                    </div>
                </div>
            </div>

            
            

            <!-- Bagian Ikhtisar Statistik -->
            <!-- Loading Skeleton -->
            <div x-show="showStats && isLoading" class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-6 animate-pulse">
                <?php for($i = 0; $i < 5; $i++): ?>
                    <div class="card p-6 flex flex-col justify-between h-40">
                        <div class="flex justify-between items-start">
                            <div class="h-4 bg-gray-200 rounded w-24"></div>
                            <div class="h-10 w-10 bg-gray-200 rounded-bl-full -mr-6 -mt-6"></div>
                        </div>
                        <div class="mt-2 text-3xl font-bold text-gray-200">000</div>
                        <div class="mt-4 flex items-center">
                            <div class="p-2 bg-gray-100 rounded-lg w-9 h-9"></div>
                            <div class="ml-2 h-4 bg-gray-100 rounded w-16"></div>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>

            <!-- Konten Asli -->
            <div x-show="showStats && !isLoading" 
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-5 gap-6 mb-6">
                <!-- Total Sparepart -->
                <div class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-primary-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-primary-200"></div>
                    <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative"><?php echo e(__('ui.total_items')); ?></p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative" x-text="totalSpareparts"><?php echo e($totalSpareparts); ?></h3>
                    </div>
                    <div class="mt-4 flex items-center text-primary-600 z-10 relative">
                        <div class="p-2 bg-primary-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-primary-50 text-primary-700 px-2 py-0.5 rounded-full"><?php echo e(__('ui.sku_items')); ?></span>
                    </div>
                </div>

                <!-- Total Stok -->
                <div class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-success-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-success-200"></div>
                    <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative"><?php echo e(__('ui.total_physical_stock')); ?></p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative" x-text="totalStock"><?php echo e($totalStock); ?></h3>
                    </div>
                    <div class="mt-4 flex items-center text-success-600 z-10 relative">
                        <div class="p-2 bg-success-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 8h14M5 8a2 2 0 110-4h14a2 2 0 110 4M5 8v10a2 2 0 002 2h10a2 2 0 002-2V8m-9 4h4"></path></svg>
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-success-50 text-success-700 px-2 py-0.5 rounded-full"><?php echo e(__('ui.units')); ?></span>
                    </div>
                </div>

                <!-- Total Kategori -->
                <div class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-warning-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-warning-200"></div>
                     <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative"><?php echo e(__('ui.categories')); ?></p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative"><?php echo e($totalCategories); ?></h3>
                    </div>
                    <div class="mt-4 flex items-center text-warning-600 z-10 relative">
                        <div class="p-2 bg-warning-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path></svg>
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-warning-50 text-warning-700 px-2 py-0.5 rounded-full"><?php echo e(__('ui.item_types')); ?></span>
                    </div>
                </div>

                <!-- Widget Peminjaman Aktif -->
                <div x-show="showBorrowings" class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-indigo-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-indigo-200"></div>
                    <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative"><?php echo e(__('ui.currently_borrowed')); ?></p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative"><?php echo e($activeBorrowingsCount); ?></h3>
                    </div>
                    <div class="mt-4 flex items-center text-indigo-600 z-10 relative">
                        <div class="p-2 bg-indigo-100 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-indigo-50 text-indigo-700 px-2 py-0.5 rounded-full"><?php echo e(__('ui.units_out')); ?></span>
                    </div>
                </div>

                <!-- Total Lokasi -->
                <div class="card p-6 flex flex-col justify-between relative overflow-hidden group hover:scale-[1.02] transition-transform duration-300">
                    <div class="absolute right-0 top-0 h-24 w-24 bg-secondary-100 rounded-bl-full -mr-4 -mt-4 transition-colors group-hover:bg-secondary-200"></div>
                     <div>
                        <p class="text-sm font-medium text-secondary-500 z-10 relative"><?php echo e(__('ui.storage_locations')); ?></p>
                        <h3 class="text-3xl font-bold text-secondary-900 mt-2 z-10 relative"><?php echo e($totalLocations); ?></h3>
                    </div>
                    <div class="mt-4 flex items-center text-secondary-600 z-10 relative">
                        <div class="p-2 bg-secondary-200 rounded-lg group-hover:bg-white group-hover:shadow-sm transition-all">
                             <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <span class="ml-2 text-xs font-semibold bg-secondary-100 text-secondary-700 px-2 py-0.5 rounded-full"><?php echo e(__('ui.warehouse_racks')); ?></span>
                    </div>
                </div>
            </div>


            <!-- Widget Item Terlambat (Baris Baru) -->
            <!-- Skeleton Terlambat -->
            <div x-show="showOverdue && <?php echo e($totalOverdueCount); ?> > 0 && isLoading" class="mb-6 animate-pulse">
                <div class="card border-l-4 border-danger-200">
                    <div class="card-header p-4 border-b border-gray-100 flex justify-between">
                         <div class="h-6 bg-gray-200 rounded w-64"></div>
                    </div>
                    <div class="p-4 space-y-3">
                        <?php for($i=0; $i<3; $i++): ?>
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-1/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-20"></div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
            </div>

            <div x-show="showOverdue && <?php echo e($totalOverdueCount); ?> > 0 && !isLoading" 
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
                            <h3 class="font-bold text-white"><?php echo e(__('ui.attention_overdue')); ?> (<?php echo e($totalOverdueCount); ?>)</h3>
                        </div>
                        <!-- Link ke inventaris jika > 5 -->
                        <?php if($totalOverdueCount > 0): ?>
                            <a href="<?php echo e(route('inventory.index', ['filter' => 'overdue'])); ?>" class="text-xs text-white hover:text-red-100 font-bold underline decoration-white/50"><?php echo e(__('ui.view_all')); ?></a>
                        <?php endif; ?>
                    </div>
                    <div class="overflow-x-auto md:block hidden">
                        <table class="w-full text-sm text-left">
                            <thead class="text-xs text-secondary-500 uppercase bg-secondary-50 border-b border-secondary-200">
                                <tr>
                                    <th class="px-6 py-3"><?php echo e(__('ui.borrower')); ?></th>
                                    <th class="px-6 py-3"><?php echo e(__('ui.item')); ?></th>
                                    <th class="px-6 py-3 text-center"><?php echo e(__('ui.due_date_short')); ?></th>
                                    <th class="px-6 py-3 text-center"><?php echo e(__('ui.late')); ?></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100">
                                <template x-for="borrow in overdueBorrowingsList" :key="borrow.id">
                                    <tr class="hover:bg-secondary-50 cursor-pointer" @click="window.location.href = '/inventory/borrow/' + borrow.id">
                                        <td class="px-6 py-3 font-medium text-secondary-900" x-text="borrow.user_name || borrow.borrower_name"></td>
                                        <td class="px-6 py-3">
                                            <span x-text="borrow.sparepart_name"></span> (<span x-text="borrow.quantity"></span>)
                                        </td>
                                        <td class="px-6 py-3 text-center font-bold text-danger-600" x-text="borrow.due_date_formatted"></td>
                                        <td class="px-6 py-3 text-center text-danger-500" x-text="borrow.due_date_rel"></td>
                                    </tr>
                                </template>
                            </tbody>
                        </table>
                    </div>

                    <!-- Tampilan Bertumpuk Mobile -->
                    <div class="md:hidden divide-y divide-secondary-100">
                        <template x-for="borrow in overdueBorrowingsList" :key="borrow.id">
                            <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" @click="window.location.href = '/inventory/borrow/' + borrow.id">
                                <div class="flex justify-between items-start mb-2">
                                    <div class="font-bold text-secondary-900" x-text="borrow.user_name || borrow.borrower_name"></div>
                                    <span class="text-xs font-bold text-danger-600 bg-danger-50 px-2 py-1 rounded-full" x-text="borrow.due_date_rel"></span>
                                </div>
                                <div class="text-sm text-secondary-600 mb-1">
                                    <span x-text="borrow.sparepart_name"></span> (<span x-text="borrow.quantity"></span> unit)
                                </div>
                                <div class="text-xs text-secondary-500 flex items-center gap-1">
                                    <span><?php echo e(__('ui.due_date_short')); ?>:</span>
                                    <span class="font-semibold text-danger-600" x-text="borrow.due_date_formatted"></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>


            <!-- Baru: Grafik Pergerakan Stok -->
            <div x-show="showMovement && isLoading" class="card mb-4 animate-pulse">
                <div class="card-header border-b border-gray-100 p-5">
                    <div class="h-5 bg-gray-200 rounded w-48 mb-2"></div>
                    <div class="h-3 bg-gray-200 rounded w-64"></div>
                </div>
                <div class="card-body p-6">
                    <div class="h-[250px] w-full bg-gray-100 rounded flex items-end justify-between px-4 pb-4 gap-2">
                         <?php for($i=0; $i<12; $i++): ?>
                            <div class="w-full bg-gray-200 rounded-t" style="height: <?php echo e(rand(20, 80)); ?>%"></div>
                         <?php endfor; ?>
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
                            <h3 class="font-bold text-secondary-900"><?php echo e(__('ui.stock_movement')); ?></h3>
                            <p class="text-xs text-secondary-500"><?php echo e(__('ui.stock_movement_desc')); ?></p>
                        </div>
                    
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

            <!-- Baru: Bagian Item Teratas -->
            <div x-show="showTopItems && isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4 animate-pulse">
                 <!-- Skeleton Teratas Keluar -->
                 <div class="card">
                     <div class="card-header border-b border-gray-100 p-4">
                         <div class="h-4 bg-gray-200 rounded w-32"></div>
                     </div>
                     <div class="p-4 space-y-3">
                        <?php for($i=0; $i<5; $i++): ?>
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-8"></div>
                            </div>
                        <?php endfor; ?>
                     </div>
                 </div>
                 <!-- Skeleton Teratas Masuk -->
                 <div class="card">
                    <div class="card-header border-b border-gray-100 p-4">
                        <div class="h-4 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="p-4 space-y-3">
                       <?php for($i=0; $i<5; $i++): ?>
                           <div class="flex justify-between">
                               <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                               <div class="h-4 bg-gray-200 rounded w-8"></div>
                           </div>
                       <?php endfor; ?>
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
                 
                 <div class="card flex flex-col overflow-hidden">
                     <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                         <div class="w-8 h-8 rounded-lg bg-danger-50 flex items-center justify-center flex-shrink-0">
                             <svg class="w-4 h-4 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                         </div>
                         <div>
                             <h3 class="font-bold text-secondary-900"><?php echo e(__('ui.top_exiting_items')); ?></h3>
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
                                 <span class="flex-shrink-0 font-bold text-sm text-danger-600 tabular-nums" x-text="'− ' + parseInt(item.total_qty).toLocaleString('id-ID')"></span>
                             </div>
                         </template>
                         <div x-show="topExited.length === 0" class="px-6 py-10 text-center text-secondary-400">
                             <svg class="w-8 h-8 mx-auto text-secondary-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                             <p class="text-sm italic"><?php echo e(__('ui.no_data')); ?></p>
                         </div>
                     </div>
                 </div>

                 
                 <div class="card flex flex-col overflow-hidden border-l-4 border-emerald-400">
                     <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                         <div class="w-8 h-8 rounded-lg bg-emerald-50 flex items-center justify-center flex-shrink-0">
                             <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 17h8m0 0V9m0 8l-8-8-4 4-6-6"></path></svg>
                         </div>
                         <div>
                             <h3 class="font-bold text-secondary-900"><?php echo e(__('ui.top_entering_items')); ?></h3>
                             <p class="text-xs text-secondary-400">Berdasarkan periode yang dipilih</p>
                         </div>
                     </div>
                     <div class="flex-grow divide-y divide-secondary-100">
                         <?php $__empty_1 = true; $__currentLoopData = $topEntered; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $item): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                             <?php
                                  $rank = $loop->iteration;
                                  $badgeClass = match(true) {
                                      $rank === 1 => 'bg-amber-100 text-amber-700 shadow shadow-amber-200',
                                      $rank === 2 => 'bg-slate-100  text-slate-600 shadow shadow-slate-200',
                                      $rank === 3 => 'bg-orange-100 text-orange-700 shadow shadow-orange-200',
                                      default     => 'bg-secondary-100 text-secondary-500',
                                  };
                             ?>
                             <div class="group flex items-center gap-4 px-6 py-3.5 hover:bg-emerald-50/50 transition-all duration-150 cursor-pointer"
                                  onclick="window.location.href='<?php echo e(route('inventory.show', $item->sparepart_id)); ?>'">
                                 <div class="flex-shrink-0 w-8 h-8 flex items-center justify-center rounded-full text-sm font-bold transition-transform duration-200 group-hover:scale-110 <?php echo e($badgeClass); ?>">
                                     <?php echo e($rank); ?>

                                 </div>
                                 <span class="flex-grow font-semibold text-secondary-800 text-sm group-hover:text-primary-700 transition-colors truncate"><?php echo e($item->sparepart_name ?? 'Unknown'); ?></span>
                                 <span class="flex-shrink-0 inline-flex items-center gap-1 px-2.5 py-1 rounded-full bg-emerald-100 text-emerald-700 font-bold text-xs tabular-nums">
                                     <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 10l7-7m0 0l7 7m-7-7v18"/></svg>
                                     <?php echo e(number_format($item->total_qty, 0, ',', '.')); ?>

                                 </span>
                             </div>
                         <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                              <div class="px-6 py-10 text-center text-secondary-400">
                                  <svg class="w-8 h-8 mx-auto text-secondary-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                                  <p class="text-sm italic"><?php echo e(__('ui.no_data')); ?></p>
                              </div>
                         <?php endif; ?>
                     </div>
                 </div>

             </div>

            <!-- Bagian Grafik -->
            <div x-show="showCharts && isLoading" class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4 animate-pulse">
                <!-- Skeleton Donut -->
                <div class="card flex flex-col h-[400px]">
                    <div class="card-header border-b border-gray-100 p-5">
                        <div class="h-5 bg-gray-200 rounded w-48"></div>
                    </div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center">
                        <div class="w-48 h-48 rounded-full border-8 border-gray-200"></div>
                    </div>
                </div>
                <!-- Skeleton Bar -->
                <div class="card flex flex-col h-[400px]">
                    <div class="card-header border-b border-gray-100 p-5">
                        <div class="h-5 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="card-body p-6 flex-grow flex items-end justify-around gap-2 px-10">
                        <?php for($i=0; $i<6; $i++): ?>
                            <div class="w-12 bg-gray-200 rounded-t" style="height: <?php echo e(rand(30, 90)); ?>%"></div>
                        <?php endfor; ?>
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
                <!-- Grafik Donut -->
                <div class="card flex flex-col">
                    <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900"><?php echo e(__('ui.stock_distribution_category')); ?></h3>
 
                    </div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center bg-white min-h-[300px]">
                        <div class="w-full h-full max-h-[300px]">
                            <canvas id="stockByCategoryChart"></canvas>
                        </div>
                    </div>
                </div>

                <!-- Grafik Batang -->
                <div class="card flex flex-col">
                     <div class="card-header border-b border-secondary-100 p-5 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900"><?php echo e(__('ui.stock_location')); ?></h3>

                    </div>
                    <div class="card-body p-6 flex-grow flex items-center justify-center bg-white min-h-[300px]">
                        <div class="w-full h-full max-h-[300px]">
                            <canvas id="stockByLocationChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Bawah: Stok Rendah & Aktivitas -->
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                <!-- Skeleton Stok Rendah -->
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
                         <?php for($i=0; $i<5; $i++): ?>
                             <div class="h-10 bg-gray-100 rounded w-full"></div>
                         <?php endfor; ?>
                    </div>
                </div>

                <!-- Item Stok Rendah (2 kolom) -->
                <div x-show="showLowStock && !isLoading" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-4"
                     class="card bg-white shadow-lg transform hover:scale-[1.01] transition-all duration-300 border-none overflow-hidden" :class="{ 'lg:col-span-3': !showRecent, 'lg:col-span-2': showRecent }">
                    <div class="card-header p-5 bg-gradient-to-r from-amber-500 to-orange-500 flex justify-between items-center">
                        <div class="flex items-center gap-2">
                             <div class="p-1.5 bg-white/20 text-white rounded-lg backdrop-blur-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                             </div>
                            <h3 class="font-bold text-white"><?php echo e(__('ui.warning_low_stock')); ?></h3>
                        </div>
                        <a href="<?php echo e(route('inventory.index', ['filter' => 'low_stock'])); ?>" class="text-sm text-white hover:text-amber-100 font-medium underline decoration-white/50"><?php echo e(__('ui.view_all')); ?></a>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-sm text-left text-secondary-500">
                            <thead class="text-xs text-secondary-700 uppercase bg-secondary-50 border-b border-secondary-200">
                                <tr>
                                    <th class="px-6 py-3 font-semibold tracking-wider"><?php echo e(__('ui.item')); ?></th>
                                    <th class="px-6 py-3 font-semibold tracking-wider hidden md:table-cell"><?php echo e(__('ui.categories')); ?></th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center"><?php echo e(__('ui.stock')); ?></th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center hidden md:table-cell"><?php echo e(__('ui.min_stock')); ?></th>
                                    <th class="px-6 py-3 font-semibold tracking-wider text-center"><?php echo e(__('ui.status')); ?></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100">
                            <tbody class="divide-y divide-secondary-100">
                                <template x-for="item in lowStockItems" :key="item.id">
                                    <tr class="bg-white hover:bg-secondary-50 transition-colors cursor-pointer" @click="window.location.href = '/inventory/' + item.id">
                                        <td class="px-4 py-3 font-medium text-secondary-800" x-text="item.name || 'Unknown'"></td>
                                        <td class="px-6 py-4 hidden md:table-cell" x-text="item.category || '-'"></td>
                                        <td class="px-6 py-4 text-center font-bold text-danger-600" x-text="item.stock"></td>
                                        <td class="px-6 py-4 text-center text-secondary-600 hidden md:table-cell" x-text="item.minimum_stock"></td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="badge badge-danger" x-show="item.stock == 0"><?php echo e(__('ui.status_out_of_stock')); ?></span>
                                            <span class="badge badge-warning" x-show="item.stock > 0"><?php echo e(__('ui.status_critical')); ?></span>
                                        </td>
                                    </tr>
                                </template>
                                <tr x-show="lowStockItems.length === 0">
                                    <td colspan="5" class="px-6 py-8 text-center text-secondary-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-success-200 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p><?php echo e(__('ui.all_stock_safe')); ?></p>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Skeleton Terkini -->
                <div x-show="showRecent && isLoading" class="card lg:col-span-1 animate-pulse h-[400px]">
                    <div class="card-header p-5 border-b border-gray-100 flex justify-between">
                        <div class="h-5 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="p-5 space-y-4">
                        <?php for($i=0; $i<5; $i++): ?>
                            <div class="flex gap-4">
                                <div class="h-8 w-8 bg-gray-200 rounded-full flex-shrink-0"></div>
                                <div class="flex-1 space-y-2">
                                    <div class="h-3 bg-gray-200 rounded w-full"></div>
                                    <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                                </div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>

                <!-- Aktivitas Terkini (1 kolom di web, penuh di cetak jika diperlukan) -->

                <div x-show="showRecent && !isLoading" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4"
                     x-transition:enter-end="opacity-100 translate-y-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-y-0"
                     x-transition:leave-end="opacity-0 translate-y-4"
                     class="card p-0 flex flex-col h-full print-safe" :class="{ 'lg:col-span-3': !showLowStock, 'lg:col-span-1': showLowStock }">
                     <div class="card-header p-5 border-b border-secondary-100 flex justify-between items-center">
                        <h3 class="font-bold text-secondary-900"><?php echo e(__('ui.recent_activities')); ?></h3>
                        <a href="<?php echo e(route('reports.activity-logs.index')); ?>" class="text-sm text-primary-600 hover:text-primary-700 font-medium"><?php echo e(__('ui.view_all')); ?></a>
                     </div>
                    <div class="card-body p-0 overflow-y-auto max-h-[500px] custom-scrollbar">
                        <div class="flex flex-col">
                            <!-- Alpine Loop -->
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
                            
                            <!-- Empty State -->
                            <div x-show="recentActivities.length === 0" class="px-5 py-12 text-center">
                                <div class="flex flex-col items-center gap-3">
                                    <div class="w-16 h-16 rounded-full bg-secondary-100 flex items-center justify-center">
                                        <svg class="w-8 h-8 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-secondary-700">Belum ada aktivitas</p>
                                        <p class="text-xs text-secondary-400 mt-1"><?php echo e(__('ui.no_recent_activities')); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bagian Baru: Analitik & Perkiraan -->
            <!-- Skeleton Analitik -->
            <div x-show="showStats && isLoading" class="grid grid-cols-1 lg:grid-cols-3 gap-4 mt-4 animate-pulse">
                 <!-- Skeleton Stok Mati -->
                <div class="card h-[300px]">
                    <div class="card-header p-4 border-b border-gray-100">
                        <div class="h-4 bg-gray-200 rounded w-32"></div>
                    </div>
                    <div class="p-4 space-y-3">
                        <?php for($i=0; $i<5; $i++): ?>
                            <div class="flex justify-between">
                                <div class="h-4 bg-gray-200 rounded w-2/3"></div>
                                <div class="h-4 bg-gray-200 rounded w-10"></div>
                            </div>
                        <?php endfor; ?>
                    </div>
                </div>
                <!-- Skeleton Papan Peringkat -->
                <div class="card h-[300px]">
                    <div class="card-header p-4 border-b border-gray-100">
                        <div class="h-4 bg-gray-200 rounded w-40"></div>
                    </div>
                    <div class="p-4 space-y-3">
                        <?php for($i=0; $i<5; $i++): ?>
                            <div class="flex justify-between items-center">
                                <div class="flex items-center gap-2 w-2/3">
                                    <div class="h-6 w-6 rounded-full bg-gray-200"></div>
                                    <div class="h-4 bg-gray-200 rounded w-20"></div>
                                </div>
                                <div class="h-4 bg-gray-200 rounded w-16"></div>
                            </div>
                        <?php endfor; ?>
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

                
                <div x-show="showDeadStock" x-transition class="card flex flex-col overflow-hidden border-l-4 border-amber-400">
                    <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-amber-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900"><?php echo e(__('ui.dead_stock_title')); ?></h3>
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
                                    <span x-text="item.stock"></span>&nbsp;<?php echo e(__('ui.units')); ?>

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

                
                <div x-show="showLeaderboard" x-transition class="card flex flex-col overflow-hidden border-l-4 border-success-400">
                    <div class="card-header border-b border-secondary-100 px-6 py-4 flex items-center gap-3">
                        <div class="w-8 h-8 rounded-lg bg-success-50 flex items-center justify-center flex-shrink-0">
                            <svg class="w-4 h-4 text-success-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <h3 class="font-bold text-secondary-900"><?php echo e(__('ui.top_contributors_title')); ?></h3>
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
                                    <span x-text="userLog.total_actions"></span> <?php echo e(__('ui.actions_count')); ?>

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
                <p><?php echo e(__('ui.printed_at')); ?> <?php echo e(now()->format('d M Y H:i')); ?> <?php echo e(__('ui.by')); ?> <?php echo e(auth()->user()->name); ?></p>
                <p>Azventory Management System - <?php echo e(__('ui.stock_inventory_report')); ?></p>
            </div>

            <style>
                @media print {
                    @page { margin: 0.5cm; }
                    body { visibility: hidden; background: white; }
                    .print-safe, .print-safe * { visibility: visible; }
                    .max-w-7xl { max-width: none !important; margin: 0 !important; padding: 0 !important; }
                    
                    /* Sembunyikan Sidebar, Header, Tombol */
                    nav, header, form, button, .btn, .no-print { display: none !important; }
                    
                    /* Pastikan Tata Letak Grid berfungsi saat dicetak */
                    .grid { display: grid !important; }
                    .lg\:grid-cols-4 { grid-template-columns: repeat(4, 1fr) !important; }
                    .lg\:grid-cols-3 { grid-template-columns: repeat(3, 1fr) !important; }
                    .lg\:grid-cols-2 { grid-template-columns: repeat(2, 1fr) !important; }
                    .print-grid-3 { grid-template-columns: repeat(3, 1fr) !important; }

                    /* Buat konten terlihat absolut ke atas */
                    .py-6 > div {
                        position: absolute;
                        top: 0;
                        left: 0;
                        width: 100%;
                        visibility: visible;
                    }
                    
                    .card { break-inside: avoid; border: 1px solid #ddd; box-shadow: none; }
                    .text-3xl { font-size: 1.5rem; } /* Perkecil judul */
                }
            </style>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Default Chart untuk konsistensi
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';
        Chart.defaults.scale.grid.color = '#f1f5f9';

        // =====================================================================
        // Helper: Update KPI Summary Badges dari data movement
        // =====================================================================
        function updateMovementKPI(movementData) {
            const totalMasuk = (movementData.masuk || []).reduce((a, b) => a + b, 0);
            const totalKeluar = (movementData.keluar || []).reduce((a, b) => a + b, 0);
            const net = totalMasuk - totalKeluar;

            const elMasuk = document.getElementById('kpi-masuk');
            const elKeluar = document.getElementById('kpi-keluar');
            const elNet = document.getElementById('kpi-net');
            const elNetDot = document.getElementById('kpi-net-dot');
            const elNetLabel = document.getElementById('kpi-net-label');
            const elNetBadge = document.getElementById('kpi-net-badge');

            if (elMasuk) elMasuk.textContent = totalMasuk.toLocaleString('id-ID');
            if (elKeluar) elKeluar.textContent = totalKeluar.toLocaleString('id-ID');
            if (elNet && elNetDot && elNetLabel && elNetBadge) {
                const prefix = net >= 0 ? '+' : '';
                elNet.textContent = prefix + net.toLocaleString('id-ID');
                // Warna dinamis: hijau positif, merah negatif, abu netral
                if (net > 0) {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-emerald-700 font-medium whitespace-nowrap';
                } else if (net < 0) {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-red-50 border border-red-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-red-500 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-red-700 font-medium whitespace-nowrap';
                } else {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-blue-50 border border-blue-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-blue-400 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-blue-700 font-medium whitespace-nowrap';
                }
            }
        }

        // =====================================================================
        // Grafik Pergerakan Stok — Grouped Bar Chart
        // =====================================================================

        // =====================================================================
        // Export Dashboard — PDF (Print) and PNG (html2canvas)
        // =====================================================================
        function exportDashboardPDF() {
            document.title = 'Dashboard Azventory - ' + new Date().toLocaleDateString('id-ID');
            window.print();
        }

        function exportDashboardPNG() {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 z-[9999] bg-secondary-900 text-white text-sm px-4 py-3 rounded-xl shadow-xl flex items-center gap-2';
            toast.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyiapkan gambar...';
            document.body.appendChild(toast);

            // Load html2canvas jika belum ada
            if (typeof html2canvas === 'undefined') {
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
                s.onload = () => doCapture(toast);
                document.head.appendChild(s);
            } else {
                doCapture(toast);
            }
        }

        function doCapture(toastEl) {
            const target = document.querySelector('[x-data]') || document.body;
            html2canvas(target, {
                scale: 1.5,
                useCORS: true,
                backgroundColor: '#f8fafc',
                logging: false,
            }).then(canvas => {
                const link = document.createElement('a');
                const d = new Date();
                const dateStr = d.toISOString().slice(0, 10);
                link.download = `dashboard-azventory-${dateStr}.png`;
                link.href = canvas.toDataURL('image/png');
                link.click();
                if (toastEl) toastEl.remove();
            }).catch(() => {
                if (toastEl) toastEl.remove();
                alert('Gagal mengambil screenshot. Gunakan opsi Cetak/PDF.');
            });
        }

        const movementDataKey = <?php echo json_encode($movementData, 15, 512) ?>;

        // Inisialisasi KPI Badge saat load
        updateMovementKPI(movementDataKey);

        // Fungsi pembuatan gradient (dipakai ulang)
        function makeGradient(ctx, color1, color2) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, color1);
            gradient.addColorStop(1, color2);
            return gradient;
        }

        const movCtx = document.getElementById('stockMovementChart').getContext('2d');
        const gradMasuk = makeGradient(movCtx, 'rgba(16,185,129,0.85)', 'rgba(16,185,129,0.15)');
        const gradKeluar = makeGradient(movCtx, 'rgba(239,68,68,0.85)', 'rgba(239,68,68,0.15)');

        // Jika tidak ada label (periode kosong), tampilkan placeholder
        const movLabels = movementDataKey.labels.length > 0 ? movementDataKey.labels : ['Tidak ada data'];
        const movMasuk  = movementDataKey.masuk.length > 0  ? movementDataKey.masuk  : [0];
        const movKeluar = movementDataKey.keluar.length > 0 ? movementDataKey.keluar : [0];

        let movementChart = new Chart(movCtx, {
            type: 'line',
            data: {
                labels: movLabels,
                datasets: [
                    {
                        label: '📦 Barang Masuk',
                        data: movMasuk,
                        backgroundColor: gradMasuk,
                        borderColor: '#10b981',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    },
                    {
                        label: '📤 Barang Keluar',
                        data: movKeluar,
                        backgroundColor: gradKeluar,
                        borderColor: '#ef4444',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: {
                    mode: 'index',
                    intersect: false,
                },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'rectRounded',
                            padding: 16,
                            font: { size: 12, weight: '500' }
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            title(ctx) {
                                return '🗓 ' + ctx[0].label;
                            },
                            label(ctx) {
                                const val = ctx.parsed.y.toLocaleString('id-ID');
                                return `  ${ctx.dataset.label}: ${val} unit`;
                            },
                            afterBody(ctx) {
                                if (ctx.length < 2) return '';
                                const masuk  = ctx.find(c => c.datasetIndex === 0)?.parsed.y ?? 0;
                                const keluar = ctx.find(c => c.datasetIndex === 1)?.parsed.y ?? 0;
                                const net = masuk - keluar;
                                const prefix = net >= 0 ? '+' : '';
                                return [`  ─────────────────`, `  🔄 Net Stok: ${prefix}${net.toLocaleString('id-ID')} unit`];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148,163,184,0.15)',
                            borderDash: [4, 4]
                        },
                        ticks: {
                            callback: val => val.toLocaleString('id-ID')
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 45,
                            autoSkipPadding: 8
                        }
                    }
                }
            }
        });

        // =====================================================================
        // Grafik Donut: Stok berdasarkan Kategori
        // =====================================================================
        const stockByCategoryData = <?php echo json_encode($stockByCategory, 15, 512) ?>;
        const catCtx = document.getElementById('stockByCategoryChart').getContext('2d');
        // Cool-tone Gradient Colors (Blue -> Violet -> Pink -> Cyan)
        const baseColors = [
            '#3b82f6', // Blue
            '#8b5cf6', // Violet
            '#ec4899', // Pink
            '#06b6d4', // Cyan
            '#6366f1', // Indigo
            '#14b8a6', // Teal
        ];
        const chartColors = baseColors.map(c => {
            const grd = catCtx.createLinearGradient(0, 0, 0, 300);
            grd.addColorStop(0, c);
            grd.addColorStop(1, c + '90'); // Less transparency for richer color
            return grd;
        });
        const chartColorsBorder = baseColors;

        // Total untuk persentase tooltip
        const catTotal = Object.values(stockByCategoryData).reduce((a, b) => a + b, 0);

        // Responsive legend position
        const isSmallScreen = window.innerWidth < 640;

        // Custom Plugin untuk menggambar dashed ring yang presisi di tengah chart
        const outerDashedRing = {
            id: 'outerDashedRing',
            beforeDraw(chart) {
                const {ctx, chartArea: {top, bottom, left, right, width, height}} = chart;
                const centerX = (left + right) / 2;
                const centerY = (top + bottom) / 2;
                
                // Pastikan radius ring dihitung dari radius chart sebenarnya
                const meta = chart.getDatasetMeta(0);
                if (meta.data.length > 0) {
                    const outerRadius = meta.data[0].outerRadius;
                    const ringRadius = outerRadius + 15; // Jarak ring dari chart

                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, ringRadius, 0, 2 * Math.PI);
                    ctx.lineWidth = 2;
                    ctx.strokeStyle = '#e0e7ff'; // Indigo-100
                    ctx.setLineDash([6, 6]); // Garis putus-putus
                    ctx.stroke();
                    ctx.restore();
                }
            }
        };

        let stockCategoryChart = new Chart(document.getElementById('stockByCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(stockByCategoryData),
                datasets: [{
                    label: '<?php echo e(__('ui.total_stock')); ?>',
                    data: Object.values(stockByCategoryData),
                    backgroundColor: chartColors,
                    borderColor: '#ffffff',
                    borderWidth: 0,
                    hoverOffset: 8
                }]
            },
            plugins: [outerDashedRing], // Register plugin
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%', // Balanced & Modern
                layout: {
                    padding: 20 // Extra padding for ring
                },
                elements: {
                    arc: {
                        borderWidth: 0,
                        borderColor: '#ffffff',
                        borderRadius: 5,
                        hoverOffset: 10
                    }
                },
                plugins: {
                    legend: {
                        position: isSmallScreen ? 'bottom' : 'right',
                        labels: {
                            usePointStyle: true,
                            pointStyle: 'circle',
                            padding: 20,
                            font: { size: 12 },
                            boxWidth: 8
                        }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label(ctx) {
                                const val = ctx.parsed;
                                const pct = catTotal > 0 ? ((val / catTotal) * 100).toFixed(1) : 0;
                                return `  ${ctx.label}: ${val.toLocaleString('id-ID')} unit (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });

        // =====================================================================
        // Grafik Batang: Stok berdasarkan Lokasi
        // =====================================================================
        const stockByLocationData = <?php echo json_encode($stockByLocation, 15, 512) ?>;
        const locCtx = document.getElementById('stockByLocationChart').getContext('2d');
        const gradLoc = locCtx.createLinearGradient(0, 0, 0, 280);
        gradLoc.addColorStop(0, 'rgba(59,130,246,0.9)');
        gradLoc.addColorStop(1, 'rgba(59,130,246,0.2)');

        let stockLocationChart = new Chart(locCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(stockByLocationData),
                datasets: [{
                    label: '<?php echo e(__('ui.total_stock')); ?>',
                    data: Object.values(stockByLocationData),
                    backgroundColor: gradLoc,
                    borderColor: '#3b82f6',
                    borderWidth: 1.5,
                    borderRadius: 6,
                    borderSkipped: false,
                    barPercentage: 0.65,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label(ctx) {
                                return `  📦 Stok: ${ctx.parsed.y.toLocaleString('id-ID')} unit`;
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(148,163,184,0.15)',
                            borderDash: [4, 4]
                        },
                        ticks: {
                            callback: val => val.toLocaleString('id-ID')
                        }
                    },
                    x: {
                        grid: { display: false },
                        ticks: {
                            maxRotation: 35,
                            autoSkipPadding: 6
                        }
                    }
                }
            }
        });

        // =====================================================================
        // Global Function untuk Update Chart (Real-time Safe)
        // Dipanggil oleh real-time listener Alpine.js saat ada event baru
        // =====================================================================
        window.updateDashboardCharts = function(movementData, stockByCategory, stockByLocation) {
            // Update Movement Chart + KPI Badges
            if (movementData && movementChart) {
                const newLabels  = movementData.labels.length > 0 ? movementData.labels : ['Tidak ada data'];
                const newMasuk   = movementData.masuk.length  > 0 ? movementData.masuk  : [0];
                const newKeluar  = movementData.keluar.length > 0 ? movementData.keluar : [0];
                movementChart.data.labels = newLabels;
                movementChart.data.datasets[0].data = newMasuk;
                movementChart.data.datasets[1].data = newKeluar;
                movementChart.update();
                // Sinkronkan KPI badges dengan data terbaru
                updateMovementKPI(movementData);
            }

            // Update Category Chart
            if (stockByCategory && stockCategoryChart) {
                stockCategoryChart.data.labels = Object.keys(stockByCategory);
                stockCategoryChart.data.datasets[0].data = Object.values(stockByCategory);
                stockCategoryChart.update();
            }

            // Update Location Chart
            if (stockByLocation && stockLocationChart) {
                stockLocationChart.data.labels = Object.keys(stockByLocation);
                stockLocationChart.data.datasets[0].data = Object.values(stockByLocation);
                stockLocationChart.update();
            }
        };

        function dashboardData() {
            return {
                showStats: localStorage.getItem('dashboard_showStats') !== 'false',
                showCharts: localStorage.getItem('dashboard_showCharts') !== 'false',
                showMovement: localStorage.getItem('dashboard_showMovement') !== 'false',
                showTopItems: localStorage.getItem('dashboard_showTopItems') !== 'false',
                showLowStock: localStorage.getItem('dashboard_showLowStock') !== 'false',
                showRecent: localStorage.getItem('dashboard_showRecent') !== 'false',
                showDeadStock: localStorage.getItem('dashboard_showDeadStock') !== 'false',
                showLeaderboard: localStorage.getItem('dashboard_showLeaderboard') !== 'false',
                showBorrowings: localStorage.getItem('dashboard_showBorrowings') !== 'false',
                showOverdue: localStorage.getItem('dashboard_showOverdue') !== 'false',
                
                isLoading: true,
                
                // Data Statis & List
                totalSpareparts: <?php echo e($totalSpareparts); ?>,
                totalStock: <?php echo e($totalStock); ?>,
                totalCategories: <?php echo e($totalCategories); ?>,
                totalLocations: <?php echo e($totalLocations); ?>,
                pendingApprovalsCount: <?php echo e($pendingApprovalsCount); ?>,
                activeBorrowingsCount: <?php echo e($activeBorrowingsCount); ?>,

                // Arrays (untuk x-for)
                recentActivities: <?php echo json_encode($recentActivities, 15, 512) ?>,
                topExited: <?php echo json_encode($topExited, 15, 512) ?>,
                topEntered: <?php echo json_encode($topEntered, 15, 512) ?>,
                deadStockItems: <?php echo json_encode($deadStockItems, 15, 512) ?>,
                activeUsers: <?php echo json_encode($activeUsers, 15, 512) ?>,
                activeBorrowingsList: <?php echo json_encode($activeBorrowingsList, 15, 512) ?>,
                overdueBorrowingsList: <?php echo json_encode($overdueBorrowingsList, 15, 512) ?>,
                lowStockItems: <?php echo json_encode($lowStockItems, 15, 512) ?>,

                // Charts Data (Disimpan sementara untuk update chart)
                movementData: <?php echo json_encode($movementData, 15, 512) ?>,
                stockByCategory: <?php echo json_encode($stockByCategory, 15, 512) ?>,
                stockByLocation: <?php echo json_encode($stockByLocation, 15, 512) ?>,
                
                init() {
                    // Simulasikan delay loading
                    setTimeout(() => {
                        this.isLoading = false;
                    }, 800);

                    // Real-time listener
                    if (window.Echo) {
                        window.Echo.channel('inventory-updates')
                            .listen('.InventoryUpdated', (e) => {
                                console.log('Realtime Update:', e);
                                this.refreshData();
                                
                                // Tampilkan notifikasi toast
                                if (window.showToast) {
                                    window.showToast('info', e.message);
                                }
                            });
                    }
                },

                async refreshData() {
                   try {
                       const response = await fetch('<?php echo e(route("dashboard.superadmin")); ?>', {
                           headers: {
                               'X-Requested-With': 'XMLHttpRequest',
                               'Accept': 'application/json'
                           }
                       });
                       
                       if (!response.ok) throw new Error('Network response was not ok');
                       
                       const data = await response.json();
                       
                       // Update Single Values
                       this.totalSpareparts = data.totalSpareparts;
                       this.totalStock = data.totalStock;
                       this.totalCategories = data.totalCategories;
                       this.totalLocations = data.totalLocations;
                       this.pendingApprovalsCount = data.pendingApprovalsCount;
                       this.activeBorrowingsCount = data.activeBorrowingsCount;

                       // Update Lists
                       this.recentActivities = data.recentActivities;
                       this.topExited = data.topExited;
                       this.topEntered = data.topEntered;
                       this.forecasts = data.forecasts;
                       this.deadStockItems = data.deadStockItems;
                       this.activeUsers = data.activeUsers;
                       this.activeBorrowingsList = data.activeBorrowingsList;
                       this.overdueBorrowingsList = data.overdueBorrowingsList;
                       this.lowStockItems = data.lowStockItems;

                       // Update Charts (Function defined globally or attached to window)
                       if (window.updateDashboardCharts) {
                           window.updateDashboardCharts(data.movementData, data.stockByCategory, data.stockByLocation);
                       }

                   } catch (error) {
                       console.error('Failed to refresh dashboard data:', error);
                   }
                },

                toggle(key) {
                    this[key] = !this[key];
                    localStorage.setItem('dashboard_' + key, this[key]);
                }
            };
        }
        // =====================================================================
        // Alpine Component: Tab Period Global
        // Mengelola state panel "Custom" (Opsi F)
        // =====================================================================
        function globalPeriodFilter() {
            return {
                // Buka panel custom secara otomatis jika periode aktif = custom
                showCustom: <?php echo e(in_array($period ?? 'today', ['custom','custom_year']) ? 'true' : 'false'); ?>,
            };
        }

        // =====================================================================
        // Opsi C: Quick-filter per-widget Pergerakan Stok
        // Fetch data movement dari endpoint ringan tanpa reload halaman
        // =====================================================================
        let movementActiveRange = 7; // default aktif

        async function fetchMovementData(range) {
            movementActiveRange = range;

            // Update tampilan state tombol aktif
            document.querySelectorAll('.mov-range-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'shadow-sm', 'text-primary-700');
                btn.classList.add('text-secondary-600');
            });
            const activeBtn = document.getElementById('mov-btn-' + range);
            if (activeBtn) {
                activeBtn.classList.add('bg-white', 'shadow-sm', 'text-primary-700');
                activeBtn.classList.remove('text-secondary-600');
            }

            // Tampilkan loading state pada canvas
            const canvas = document.getElementById('stockMovementChart');
            if (canvas) canvas.style.opacity = '0.5';

            try {
                const response = await fetch('<?php echo e(route("dashboard.movement-data")); ?>?range=' + range, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });

                if (!response.ok) throw new Error('Gagal memuat data movement');

                const data = await response.json();

                // Update chart dengan data baru
                if (movementChart) {
                    const newLabels  = (data.labels  || []).length > 0 ? data.labels  : ['Tidak ada data'];
                    const newMasuk   = (data.masuk   || []).length > 0 ? data.masuk   : [0];
                    const newKeluar  = (data.keluar  || []).length > 0 ? data.keluar  : [0];

                    movementChart.data.labels = newLabels;
                    movementChart.data.datasets[0].data = newMasuk;
                    movementChart.data.datasets[1].data = newKeluar;
                    movementChart.update('active');
                }

                // Update KPI badges
                updateMovementKPI(data);

            } catch (err) {
                console.error('fetchMovementData error:', err);
            } finally {
                // Hapus loading state
                if (canvas) canvas.style.opacity = '1';
            }
        }
    </script>
    <?php $__env->stopPush(); ?>
 <?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $attributes = $__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__attributesOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54)): ?>
<?php $component = $__componentOriginal9ac128a9029c0e4701924bd2d73d7f54; ?>
<?php unset($__componentOriginal9ac128a9029c0e4701924bd2d73d7f54); ?>
<?php endif; ?>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/dashboard/superadmin.blade.php ENDPATH**/ ?>