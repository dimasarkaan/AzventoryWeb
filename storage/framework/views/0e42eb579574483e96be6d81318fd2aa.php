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
            <!-- Header -->
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        <?php echo e(__('ui.approvals_title')); ?>

                    </h2>
                    <p class="mt-1 text-sm text-secondary-500"><?php echo e(__('ui.approvals_desc')); ?></p>
                </div>
                <!-- Bulk Actions (Desktop Only for now) -->
                <?php if($pendingApprovals->isNotEmpty()): ?>
                <div id="bulk-actions-container" class="hidden items-center gap-2 bg-white p-2 rounded-xl border border-primary-100 shadow-sm animate-fade-in-down">
                    <span class="text-xs font-bold text-secondary-500 ml-2 mr-1"><span id="selected-count">0</span> Terpilih:</span>
                    <button type="button" onclick="submitBulk('approved')" class="btn btn-success text-xs py-1.5 px-3 flex items-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Setujui Semua
                    </button>
                    <button type="button" onclick="submitBulk('rejected')" class="btn btn-danger text-xs py-1.5 px-3 flex items-center gap-1.5 shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        Tolak Semua
                    </button>
                </div>
                <?php endif; ?>
            </div>

            <!-- Session Alerts -->
            <?php if(session('errors_list')): ?>
                <div class="mb-4 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-r-xl shadow-sm animate-shake">
                    <p class="font-bold flex items-center gap-2 mb-2">
                        <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        Beberapa pengajuan gagal diproses:
                    </p>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-6">
                        <?php $__currentLoopData = session('errors_list'); $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $error): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                            <li><?php echo e($error); ?></li>
                        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
                    </ul>
                </div>
            <?php endif; ?>

            <div id="approvals-list-container">
                <!-- Mobile Card View (Refined) -->
                <div class="md:hidden space-y-4" id="mobile-approvals-list">
                    <?php $__empty_1 = true; $__currentLoopData = $pendingApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <?php if (isset($component)) { $__componentOriginal81186006f81d6b8b963727f90aa900ef = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal81186006f81d6b8b963727f90aa900ef = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.approval.card','data' => ['approval' => $approval]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('approval.card'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['approval' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($approval)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal81186006f81d6b8b963727f90aa900ef)): ?>
<?php $attributes = $__attributesOriginal81186006f81d6b8b963727f90aa900ef; ?>
<?php unset($__attributesOriginal81186006f81d6b8b963727f90aa900ef); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal81186006f81d6b8b963727f90aa900ef)): ?>
<?php $component = $__componentOriginal81186006f81d6b8b963727f90aa900ef; ?>
<?php unset($__componentOriginal81186006f81d6b8b963727f90aa900ef); ?>
<?php endif; ?>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="card p-8 text-center text-secondary-500 rounded-xl" id="empty-state-mobile">
                            <p class="text-sm"><?php echo e(__('ui.no_pending')); ?></p>
                        </div>
                    <?php endif; ?>
                </div>

                </div>
                
                
                <form id="bulk-approval-form" action="<?php echo e(route('inventory.stock-approvals.bulk-approve')); ?>" method="POST" class="hidden">
                    <?php echo csrf_field(); ?>
                    <input type="hidden" name="status" id="bulk-status" value="approved">
                    <div id="bulk-ids-container"></div>
                </form>

                <div class="hidden md:block card overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="table-modern w-full">
                            <thead>
                                <tr>
                                    <th class="w-10">
                                        <input type="checkbox" id="select-all" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500">
                                    </th>
                                    <th><?php echo e(__('ui.item_column')); ?></th>
                                    <th><?php echo e(__('ui.applicant_column')); ?></th>
                                    <th><?php echo e(__('ui.type_column')); ?></th>
                                    <th><?php echo e(__('ui.amount_column')); ?></th>
                                    <th><?php echo e(__('ui.reason_column')); ?></th>
                                    <th><?php echo e(__('ui.date_column')); ?></th>
                                    <th class="text-right"><?php echo e(__('ui.action_column')); ?></th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100" id="desktop-approvals-list">
                                <?php $__empty_1 = true; $__currentLoopData = $pendingApprovals; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $approval): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <?php if (isset($component)) { $__componentOriginal1547b1a03e695266aa38e35b7290bb93 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1547b1a03e695266aa38e35b7290bb93 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.approval.table-row','data' => ['approval' => $approval]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('approval.table-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['approval' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($approval)]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1547b1a03e695266aa38e35b7290bb93)): ?>
<?php $attributes = $__attributesOriginal1547b1a03e695266aa38e35b7290bb93; ?>
<?php unset($__attributesOriginal1547b1a03e695266aa38e35b7290bb93); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1547b1a03e695266aa38e35b7290bb93)): ?>
<?php $component = $__componentOriginal1547b1a03e695266aa38e35b7290bb93; ?>
<?php unset($__componentOriginal1547b1a03e695266aa38e35b7290bb93); ?>
<?php endif; ?>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr id="empty-state-desktop">
                                        <td colspan="8" class="p-8 text-center text-secondary-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <svg class="w-12 h-12 text-success-100 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                <p><?php echo e(__('ui.no_pending_approvals')); ?></p>
                                            </div>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6" id="approvals-pagination">
                    <?php echo e($pendingApprovals->links()); ?>

                </div>
            </div>
        </div>
    </div>
    <?php $__env->startPush('scripts'); ?>
    <script>
        function confirmReject(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: 'Konfirmasi Penolakan',
                text: "Apakah Anda yakin ingin menolak pengajuan stok ini?",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Ya, Tolak',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: '!rounded-2xl !font-sans',
                    title: '!text-secondary-900 !text-xl !font-bold',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    confirmButton: 'btn btn-danger px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-danger-500',
                    cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                },
                buttonsStyling: false,
                iconColor: '#ef4444',
                padding: '2em',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        }

        function confirmApprove(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: 'Konfirmasi Persetujuan',
                text: "Apakah Anda yakin ingin menyetujui pengajuan stok ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: 'Ya, Setujui',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: '!rounded-2xl !font-sans',
                    title: '!text-secondary-900 !text-xl !font-bold',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    confirmButton: 'btn btn-success px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-success-500',
                    cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                },
                buttonsStyling: false,
                iconColor: '#10b981',
                padding: '2em',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        }

        // Bulk Actions Logic
        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const rowCheckboxes = document.querySelectorAll('.row-checkbox');
            const bulkContainer = document.getElementById('bulk-actions-container');
            const selectedCountDisplay = document.getElementById('selected-count');

            function updateBulkUI() {
                const checkedCount = document.querySelectorAll('.row-checkbox:checked').length;
                if (checkedCount > 0) {
                    bulkContainer.classList.remove('hidden');
                    bulkContainer.classList.add('flex');
                    selectedCountDisplay.textContent = checkedCount;
                } else {
                    bulkContainer.classList.add('hidden');
                    bulkContainer.classList.remove('flex');
                }
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    document.querySelectorAll('.row-checkbox').forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                    updateBulkUI();
                });
            }

            document.body.addEventListener('change', function(e) {
                if(e.target.classList.contains('row-checkbox')) {
                    updateBulkUI();
                }
            });

            // Re-Initialize after DOM replacement
            window.rebindBulkEvents = function() {
                const newSelectAll = document.getElementById('select-all');
                if (newSelectAll) {
                    newSelectAll.addEventListener('change', function() {
                        document.querySelectorAll('.row-checkbox').forEach(cb => {
                            cb.checked = newSelectAll.checked;
                        });
                        updateBulkUI();
                    });
                }
            };
            
            // Real-time listener
            if (window.Echo) {
                window.Echo.private('stock-approvals')
                    .listen('.StockApprovalUpdated', (e) => {
                        console.log('Stock Approval Event:', e);
                        
                        // Show subtle toast
                        const toast = document.createElement('div');
                        toast.className = 'fixed bottom-4 right-4 bg-primary-900 bg-opacity-90 text-white px-4 py-3 rounded-lg shadow-xl z-50 flex items-center gap-3 animate-fade-in-up';
                        toast.innerHTML = `
                            <svg class="w-5 h-5 text-primary-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <div>
                                <p class="text-sm font-bold">${e.action === 'created' ? 'Pengajuan Baru Masuk!' : 'Pengajuan Diproses'}</p>
                                <p class="text-xs text-primary-200">Menyegarkan daftar otomatis...</p>
                            </div>
                        `;
                        document.body.appendChild(toast);
                        setTimeout(() => {
                            toast.classList.replace('animate-fade-in-up', 'opacity-0');
                            setTimeout(() => toast.remove(), 500);
                        }, 3000);

                        // Save current checked IDs so we don't lose them
                        const checkedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);

                        // Fetch updated list via AJAX DOM replacement
                        fetch(window.location.href)
                            .then(res => res.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newContainer = doc.getElementById('approvals-list-container');
                                
                                if (newContainer) {
                                    document.getElementById('approvals-list-container').innerHTML = newContainer.innerHTML;
                                    
                                    // Restore checked state
                                    checkedIds.forEach(id => {
                                        const cb = document.querySelector(`.row-checkbox[value="${id}"]`);
                                        if (cb) cb.checked = true;
                                    });
                                    
                                    // Rebind select all event
                                    window.rebindBulkEvents();
                                    updateBulkUI();
                                }
                            });
                    });
            }
        });

        function submitBulk(status) {
            const form = document.getElementById('bulk-approval-form');
            const statusInput = document.getElementById('bulk-status');
            const idsContainer = document.getElementById('bulk-ids-container');
            const selectedCheckBoxes = document.querySelectorAll('.row-checkbox:checked');
            const checkedCount = selectedCheckBoxes.length;

            if (checkedCount === 0) return;

            // Bersihkan kontainer ID sebelumnya
            idsContainer.innerHTML = '';
            
            // Tambahkan input hidden untuk setiap ID yang dipilih
            selectedCheckBoxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                idsContainer.appendChild(input);
            });

            statusInput.value = status;
            
            const actionText = status === 'approved' ? 'menyetujui' : 'menolak';
            const icon = status === 'approved' ? 'question' : 'warning';
            const iconColor = status === 'approved' ? '#10b981' : '#ef4444';
            const btnClass = status === 'approved' ? 'btn btn-success' : 'btn btn-danger';
            const ringColor = status === 'approved' ? 'ring-success-500' : 'ring-danger-500';

            Swal.fire({
                title: `Konfirmasi Bulk ${status === 'approved' ? 'Approve' : 'Reject'}`,
                text: `Anda akan ${actionText} ${checkedCount} pengajuan sekaligus. Lanjutkan?`,
                icon: icon,
                showCancelButton: true,
                confirmButtonText: 'Ya, Lanjutkan',
                cancelButtonText: 'Batal',
                reverseButtons: true,
                customClass: {
                    popup: '!rounded-2xl !font-sans',
                    title: '!text-secondary-900 !text-xl !font-bold',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    confirmButton: `${btnClass} px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ${ringColor}`,
                    cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                },
                buttonsStyling: false,
                iconColor: iconColor,
                padding: '2em',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            });
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
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/inventory/approvals/index.blade.php ENDPATH**/ ?>