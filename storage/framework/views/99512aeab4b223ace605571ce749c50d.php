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
            <div class="mb-6">
                <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                    <?php echo e(__('ui.profile_title')); ?>

                </h2>
                <p class="mt-1 text-sm text-secondary-500">
                    <?php echo e(__('ui.profile_desc')); ?>

                </p>
            </div>

            <!-- Profile Stats -->
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">


                <!-- Total Borrowed -->
                <a href="<?php echo e(route('profile.inventory')); ?>" class="card p-4 flex items-center gap-4 hover:bg-gray-50 transition-colors cursor-pointer group">
                    <div class="p-3 rounded-xl bg-primary-50 text-primary-600 group-hover:bg-primary-100 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-secondary-500 uppercase tracking-wider"><?php echo e(__('ui.stats_total_borrowed')); ?></p>
                        <p class="text-2xl font-bold text-secondary-900"><?php echo e($totalBorrowed); ?></p>
                    </div>
                </a>

                <!-- Active Borrows -->
                <a href="<?php echo e(route('profile.inventory')); ?>" class="card p-4 flex items-center gap-4 hover:bg-gray-50 transition-colors cursor-pointer group">
                    <div class="p-3 rounded-xl <?php echo e($activeBorrows > 0 ? 'bg-warning-50 text-warning-600 group-hover:bg-warning-100' : 'bg-success-50 text-success-600 group-hover:bg-success-100'); ?> transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-secondary-500 uppercase tracking-wider"><?php echo e(__('ui.stats_active_borrows')); ?></p>
                        <p class="text-2xl font-bold text-secondary-900"><?php echo e($activeBorrows); ?></p>
                    </div>
                </a>

                <!-- Join Date -->
                <div class="card p-4 flex items-center gap-4">
                    <div class="p-3 rounded-xl bg-info-50 text-info-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-secondary-500 uppercase tracking-wider"><?php echo e(__('ui.stats_joined_at')); ?></p>
                        <p class="text-lg font-bold text-secondary-900"><?php echo e($user->created_at->format('d M Y')); ?></p>
                    </div>
                </div>

                <!-- Account Status -->
                <div class="card p-4 flex items-center gap-4">
                    <div class="p-3 rounded-xl bg-success-50 text-success-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-secondary-500 uppercase tracking-wider"><?php echo e(__('ui.stats_status')); ?></p>
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-success-100 text-success-800">
                            <?php echo e(__('ui.status_active')); ?>

                        </span>
                    </div>
                </div>
            </div>

            <div class="space-y-4">
                <!-- Profile Information -->
                <div class="card">
                    <div class="card-header px-4 py-2 flex flex-col items-start gap-0.5">
                        <h3 class="text-lg font-bold text-secondary-900"><?php echo e(__('ui.profile_info_title')); ?></h3>
                        <p class="text-sm text-secondary-500">
                            <?php echo e(__('ui.profile_info_desc')); ?>

                        </p>
                    </div>
                    <div class="card-body p-4 pt-2">
                        <?php echo $__env->make('profile.partials.update-profile-information-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>

                <!-- Update Password -->
                <div class="card">
                    <div class="card-header px-4 py-2 flex flex-col items-start gap-0.5">
                        <h3 class="text-lg font-bold text-secondary-900"><?php echo e(__('ui.profile_password_title')); ?></h3>
                        <p class="text-sm text-secondary-500">
                            <?php echo e(__('ui.profile_password_desc')); ?>

                        </p>
                    </div>
                    <div class="card-body p-4 pt-2">
                         <?php echo $__env->make('profile.partials.update-password-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>

                <!-- API Tokens (Hanya Superadmin) -->
                <?php if(auth()->user()->role === \App\Enums\UserRole::SUPERADMIN): ?>
                <div class="card">
                    <div class="card-header px-4 py-2 flex flex-col items-start gap-0.5">
                        <h3 class="text-lg font-bold text-secondary-900"><?php echo e(__('Kunci Akses API (Token)')); ?></h3>
                        <p class="text-sm text-secondary-500">
                            <?php echo e(__('Kelola token API yang mengizinkan layanan eksternal untuk mengakses data inventaris.')); ?>

                        </p>
                    </div>
                    <div class="card-body p-4 pt-4 border-t border-secondary-100">
                        <?php echo $__env->make('profile.partials.api-tokens-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Delete Account -->
                <div class="card border-danger-200">
                    <div class="card-header px-4 py-2 bg-danger-50 border-danger-100 flex flex-col items-start gap-1">
                        <h3 class="text-lg font-bold text-danger-900"><?php echo e(__('ui.profile_delete_title')); ?></h3>
                        <p class="text-sm text-danger-700">
                            <?php echo e(__('ui.profile_delete_desc')); ?>

                        </p>
                    </div>
                     <div class="card-body p-4">
                        <?php echo $__env->make('profile.partials.delete-user-form', array_diff_key(get_defined_vars(), ['__data' => 1, '__path' => 1]))->render(); ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
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
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/profile/edit.blade.php ENDPATH**/ ?>