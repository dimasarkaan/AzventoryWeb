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
            </div>

            <!-- Mobile Card View (Refined) -->
            <div class="md:hidden space-y-4">
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
                    <div class="card p-8 text-center text-secondary-500">
                        <p class="text-sm"><?php echo e(__('ui.no_pending')); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th><?php echo e(__('ui.item_column')); ?></th>
                                <th><?php echo e(__('ui.applicant_column')); ?></th>
                                <th><?php echo e(__('ui.type_column')); ?></th>
                                <th><?php echo e(__('ui.amount_column')); ?></th>
                                <th><?php echo e(__('ui.reason_column')); ?></th>
                                <th><?php echo e(__('ui.date_column')); ?></th>
                                <th class="text-right"><?php echo e(__('ui.action_column')); ?></th>
                            </tr>
                        </thead>
                        <tbody>
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
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-secondary-500">
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


            <div class="mt-6">
                <?php echo e($pendingApprovals->links()); ?>

            </div>
        </div>
    </div>
    <?php $__env->startPush('scripts'); ?>
    <script>
        function confirmReject(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: '<?php echo e(__('ui.confirm_reject_title')); ?>',
                text: "<?php echo e(__('ui.confirm_reject_text')); ?>",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '<?php echo e(__('ui.btn_yes_reject')); ?>',
                cancelButtonText: '<?php echo e(__('ui.btn_cancel')); ?>',
                reverseButtons: true,
                customClass: {
                    popup: '!rounded-2xl !font-sans',
                    title: '!text-secondary-900 !text-xl !font-bold',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    confirmButton: 'btn btn-danger px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200',
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