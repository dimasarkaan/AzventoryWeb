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
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h1 class="text-3xl font-bold text-secondary-900 tracking-tight">Halo, <?php echo e(Auth::user()->name); ?>!</h1>
                    <p class="mt-1 text-sm text-secondary-500">Ringkasan aktivitas dan status inventaris Anda saat ini.</p>
                </div>
            </div>

            <!-- Dashboard Stats (Colored Gradient Style) -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                <!-- Active Borrowings -->
                <div class="bg-gradient-to-br from-primary-500 to-indigo-600 rounded-xl shadow-md p-6 flex items-center justify-between text-white transform transition-transform hover:-translate-y-1">
                    <div>
                        <p class="text-sm font-medium text-primary-100 mb-1">Total Pinjaman Aktif</p>
                        <h3 class="text-4xl font-extrabold"><?php echo e($activeBorrowingsCount ?? 0); ?></h3>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path></svg>
                    </div>
                </div>

                <!-- Pending Requests -->
                <div class="bg-gradient-to-br from-amber-500 to-orange-500 rounded-xl shadow-md p-6 flex items-center justify-between text-white transform transition-transform hover:-translate-y-1">
                    <div>
                        <p class="text-sm font-medium text-amber-100 mb-1">Pengajuan Stok Menunggu</p>
                        <h3 class="text-4xl font-extrabold"><?php echo e($pendingRequestsCount ?? 0); ?></h3>
                    </div>
                    <div class="w-14 h-14 bg-white/20 backdrop-blur-sm rounded-xl flex items-center justify-center text-white">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
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
                            <form method="GET" action="<?php echo e(route('dashboard')); ?>" x-ref="trendForm">
                                <input type="hidden" name="trend_period" id="trend_period_input" value="<?php echo e($trendPeriod); ?>">
                                
                                <button type="button" @click="open = !open" @click.away="open = false" class="flex-shrink-0 flex items-center gap-2 px-3 py-1.5 text-xs font-semibold text-secondary-700 bg-white border border-secondary-200 rounded-lg hover:bg-secondary-50 focus:outline-none focus:border-primary-500 focus:ring-1 focus:ring-primary-500 shadow-sm transition-all duration-200">
                                    <svg class="w-3.5 h-3.5 text-secondary-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    <span class="whitespace-nowrap">
                                        <?php switch($trendPeriod):
                                            case ('7_days'): ?> 7 Hari Terakhir <?php break; ?>
                                            <?php case ('30_days'): ?> 30 Hari Terakhir <?php break; ?>
                                            <?php case ('1_year'): ?> 1 Tahun Terakhir <?php break; ?>
                                            <?php default: ?> 6 Bulan Terakhir
                                        <?php endswitch; ?>
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
                                    
                                    <?php
                                        $periods = [
                                            '7_days' => '7 Hari Terakhir',
                                            '30_days' => '30 Hari Terakhir',
                                            '6_months' => '6 Bulan Terakhir',
                                            '1_year' => '1 Tahun Terakhir',
                                        ];
                                    ?>

                                    <?php $__currentLoopData = $periods; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $val => $label): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                        <button type="button" 
                                                @click="document.getElementById('trend_period_input').value = '<?php echo e($val); ?>'; $refs.trendForm.submit();" 
                                                class="w-full text-left px-4 py-2.5 text-xs flex items-center justify-between transition-colors <?php echo e($trendPeriod === $val ? 'bg-primary-50 text-primary-700 font-bold' : 'text-secondary-700 hover:bg-secondary-50 font-medium'); ?>">
                                            <?php echo e($label); ?>

                                            <?php if($trendPeriod === $val): ?>
                                                <svg class="w-3.5 h-3.5 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path></svg>
                                            <?php endif; ?>
                                        </button>
                                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
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
                        <a href="<?php echo e(route('profile.inventory')); ?>" class="text-xs font-bold text-primary-600 hover:text-primary-700 transition-colors bg-primary-50 px-3 py-1.5 rounded-full border border-primary-100 shadow-sm hover:bg-primary-100">Lihat Semua</a>
                    </div>
                    <div class="card-body flex-1 p-0">
                        <?php if($activeBorrowingsList->isEmpty()): ?>
                            <div class="p-8 text-center text-secondary-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                <p>Belum ada barang yang Anda pinjam saat ini.</p>
                            </div>
                        <?php else: ?>
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
                                        <?php $__currentLoopData = $activeBorrowingsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrowing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hover:bg-secondary-50/50 transition-colors cursor-pointer" onclick="window.location.href='<?php echo e(route('inventory.show', $borrowing->sparepart_id)); ?>'">
                                                <td>
                                                    <div class="font-medium text-secondary-900 line-clamp-1" title="<?php echo e($borrowing->sparepart->name); ?>"><?php echo e($borrowing->sparepart->name); ?></div>
                                                </td>
                                                <td>
                                                    <div class="font-bold text-secondary-900"><?php echo e($borrowing->remaining_quantity); ?></div>
                                                </td>
                                                <td>
                                                    <div class="text-xs text-secondary-500">
                                                        Tgl Pinjam: <span class="text-secondary-700"><?php echo e($borrowing->borrowed_at->format('d M Y')); ?></span><br>
                                                        Tenggat Kembali: <span class="font-medium <?php echo e($borrowing->isOverdue() ? 'text-danger-600' : 'text-secondary-700'); ?>"><?php echo e($borrowing->expected_return_at->format('d M Y')); ?></span>
                                                    </div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Mobile view -->
                            <div class="md:hidden divide-y divide-secondary-100">
                                <?php $__currentLoopData = $activeBorrowingsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $borrowing): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" onclick="window.location.href='<?php echo e(route('inventory.show', $borrowing->sparepart_id)); ?>'">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="font-bold text-secondary-900 leading-tight pr-4"><?php echo e($borrowing->sparepart->name); ?></div>
                                            <span class="text-sm font-bold text-primary-600 bg-primary-50 px-2.5 py-1 rounded-full whitespace-nowrap"><?php echo e($borrowing->remaining_quantity); ?> Unit</span>
                                        </div>
                                        <div class="text-xs text-secondary-500 flex flex-col gap-1 mt-2">
                                            <div class="flex items-center justify-between">
                                                <span>Pinjam: <?php echo e($borrowing->borrowed_at->format('d M Y')); ?></span>
                                            </div>
                                            <div class="flex items-center justify-between">
                                                <span>Tenggat: <span class="font-semibold <?php echo e($borrowing->isOverdue() ? 'text-danger-600' : 'text-secondary-700'); ?>"><?php echo e($borrowing->expected_return_at->format('d M Y')); ?></span></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
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
                        <?php if($pendingRequestsList->isEmpty()): ?>
                            <div class="p-8 text-center text-secondary-500">
                                <svg class="w-12 h-12 mx-auto mb-3 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <p>Tidak ada pengajuan stok yang menunggu persetujuan.</p>
                            </div>
                        <?php else: ?>
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
                                        <?php $__currentLoopData = $pendingRequestsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                            <tr class="hover:bg-secondary-50/50 transition-colors cursor-pointer" onclick="window.location.href='<?php echo e(route('inventory.show', $request->sparepart_id)); ?>'">
                                                <td>
                                                    <div class="font-medium text-secondary-900 line-clamp-1" title="<?php echo e($request->sparepart->name); ?>"><?php echo e($request->sparepart->name); ?></div>
                                                    <div class="text-[10px] text-secondary-500"><?php echo e($request->created_at->format('d M Y H:i')); ?></div>
                                                </td>
                                                <td>
                                                    <?php if($request->type === 'masuk'): ?>
                                                        <span class="badge bg-success-50 text-success-700 border border-success-200">Stok Masuk</span>
                                                    <?php else: ?>
                                                        <span class="badge bg-orange-50 text-orange-700 border border-orange-200">Stok Keluar</span>
                                                    <?php endif; ?>
                                                </td>
                                                <td>
                                                    <div class="font-bold text-secondary-900"><?php echo e($request->quantity); ?> <span class="text-xs font-normal text-secondary-500"><?php echo e($request->sparepart->unit ?? 'Pcs'); ?></span></div>
                                                </td>
                                            </tr>
                                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                                    </tbody>
                                </table>
                            </div>
                            
                            <!-- Mobile view -->
                            <div class="md:hidden divide-y divide-secondary-100">
                                <?php $__currentLoopData = $pendingRequestsList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $request): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="p-4 bg-white hover:bg-secondary-50 transition-colors cursor-pointer" onclick="window.location.href='<?php echo e(route('inventory.show', $request->sparepart_id)); ?>'">
                                        <div class="flex justify-between items-start mb-2">
                                            <div class="font-bold text-secondary-900 leading-tight pr-4"><?php echo e($request->sparepart->name); ?></div>
                                            <span class="text-sm font-bold text-secondary-900 whitespace-nowrap"><?php echo e($request->quantity); ?> <span class="text-[10px] text-secondary-500 font-normal"><?php echo e($request->sparepart->unit ?? 'Pcs'); ?></span></span>
                                        </div>
                                        <div class="flex items-center justify-between mt-2">
                                            <?php if($request->type === 'masuk'): ?>
                                                <span class="badge bg-success-50 text-success-700 border border-success-200 text-xs py-0.5">Stok Masuk</span>
                                            <?php else: ?>
                                                <span class="badge bg-orange-50 text-orange-700 border border-orange-200 text-xs py-0.5">Stok Keluar</span>
                                            <?php endif; ?>
                                            <span class="text-[10px] text-secondary-400 font-medium"><?php echo e($request->created_at->format('d M Y H:i')); ?></span>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
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
                        <?php if($topPicks->isEmpty()): ?>
                            <div class="text-center py-10 flex flex-col items-center justify-center h-full">
                                <p class="text-sm text-secondary-500">Belum ada barang favorit.</p>
                            </div>
                        <?php else: ?>
                            <div class="space-y-3">
                                <?php $__currentLoopData = $topPicks; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $index => $pick): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <div class="relative group p-3 border border-secondary-200 rounded-xl hover:shadow-sm hover:border-primary-300 transition-all duration-300 bg-white">
                                        <div class="absolute -top-2.5 -right-2.5 w-6 h-6 rounded-full <?php echo e($index === 0 ? 'bg-amber-400 text-white shadow-amber-200' : ($index === 1 ? 'bg-slate-300 text-slate-700 shadow-slate-200' : 'bg-orange-300 text-orange-800 shadow-orange-200')); ?> shadow-sm flex items-center justify-center font-bold text-[10px] z-10">
                                            #<?php echo e($index + 1); ?>

                                        </div>
                                        <div class="flex items-center gap-3">
                                            <?php if($pick->sparepart->image): ?>
                                                <img src="<?php echo e(Storage::url($pick->sparepart->image)); ?>" alt="<?php echo e($pick->sparepart->name); ?>" class="w-12 h-12 rounded-lg object-cover border border-secondary-100 flex-shrink-0">
                                            <?php else: ?>
                                                <div class="w-12 h-12 rounded-lg bg-secondary-50 border border-secondary-100 flex items-center justify-center flex-shrink-0 text-secondary-400">
                                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                </div>
                                            <?php endif; ?>
                                            <div class="flex-1 min-w-0">
                                                <h4 class="font-bold text-sm text-secondary-900 line-clamp-1 cursor-pointer hover:text-primary-600 transition-colors" onclick="window.location.href='<?php echo e(route('inventory.show', $pick->sparepart->id)); ?>'"><?php echo e($pick->sparepart->name); ?></h4>
                                                <p class="text-[10px] text-secondary-500 line-clamp-1 mb-1"><?php echo e($pick->sparepart->category->name ?? '-'); ?></p>
                                                <div class="inline-flex items-center gap-1.5 px-2 py-0.5 rounded text-[10px] bg-primary-50 text-primary-700 font-bold border border-primary-100">
                                                    <?php echo e($pick->total_borrows); ?>x Dipinjam
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
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
                        <a href="<?php echo e(route('reports.activity-logs.index')); ?>" class="text-[10px] sm:text-xs font-semibold text-primary-600 hover:text-primary-700 transition-colors whitespace-nowrap">Lihat Semua</a>
                    </div>
                    <div class="p-0 flex-1 overflow-y-auto" style="max-height: 400px;">
                        <?php if($activityLogs->isEmpty()): ?>
                            <div class="text-center py-10 flex flex-col items-center">
                                <div class="w-16 h-16 bg-secondary-50 rounded-full flex items-center justify-center mb-4">
                                    <svg class="w-8 h-8 text-secondary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-sm font-medium text-secondary-900">Belum ada aktivitas</p>
                            </div>
                        <?php else: ?>
                            <div class="divide-y divide-secondary-100">
                                <?php $__currentLoopData = $activityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                                    <?php
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
                                    ?>
                                    <div class="p-4 hover:bg-secondary-50/50 transition-colors flex gap-3 items-start">
                                        <div class="flex-shrink-0 w-8 h-8 rounded-lg <?php echo e($iconBg); ?> <?php echo e($iconColor); ?> flex items-center justify-center">
                                            <?php echo $icon; ?>

                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-1 sm:gap-2 mb-1">
                                                <p class="text-sm font-semibold text-secondary-900 truncate"><?php echo e($log->action); ?></p>
                                                <span class="text-[10px] text-secondary-500 whitespace-nowrap"><?php echo e($log->created_at->diffForHumans()); ?></span>
                                            </div>
                                            <?php if($log->details): ?>
                                                <p class="text-xs text-secondary-600 line-clamp-2 leading-relaxed"><?php echo e(strip_tags($log->details)); ?></p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                            </div>
                        <?php endif; ?>
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
                            <?php if($trustScore >= 90): ?>
                                <p class="text-success-600 font-bold text-sm">Sangat Disiplin! 🌟</p>
                                <p class="text-secondary-500 text-[10px] mt-1">Anda andal dalam mengembalikan barang tepat pada waktunya.</p>
                            <?php elseif($trustScore >= 70): ?>
                                <p class="text-warning-600 font-bold text-sm">Cukup Baik 👍</p>
                                <p class="text-secondary-500 text-[10px] mt-1">Tingkatkan lagi untuk mengembalikan barang selalu tepat waktu.</p>
                            <?php else: ?>
                                <p class="text-danger-600 font-bold text-sm">Kurang Disiplin ⚠️</p>
                                <p class="text-secondary-500 text-[10px] mt-1">Banyak barang terlambat dikembalikan. Harap perhatikan tenggat Anda.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>

    <?php $__env->startPush('scripts'); ?>
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 1. Borrowing Trend Chart
            const trendData = <?php echo json_encode($borrowingTrend, 15, 512) ?>;
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
            const statusData = <?php echo json_encode($stockChartData, 15, 512) ?>;
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
            const trustScore = <?php echo e($trustScore); ?>;
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
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/dashboard/operator.blade.php ENDPATH**/ ?>