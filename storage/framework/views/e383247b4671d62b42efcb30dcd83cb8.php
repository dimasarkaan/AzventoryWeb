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
            <div class="flex items-center justify-between mb-6">
                <div>
                     <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        <?php echo e(__('ui.add_new_user')); ?>

                    </h2>
                    <p class="mt-1 text-sm text-secondary-500"><?php echo e(__('ui.add_user_desc')); ?></p>
                </div>
                 <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
                    <?php echo e(__('ui.back')); ?>

                </a>
            </div>

            <div class="bg-white rounded-xl border border-secondary-200 shadow-card p-8 overflow-visible">
                <form action="<?php echo e(route('users.store')); ?>" method="POST" novalidate>
                    <?php echo csrf_field(); ?>
                    
                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                        <!-- Account Details -->
                        <div class="space-y-4">
                            <h3 class="text-lg font-bold text-secondary-900 border-b border-secondary-100 pb-2"><?php echo e(__('ui.account_info')); ?></h3>
                            
                            <!-- Email -->
                            <div>
                                <label for="email" class="input-label"><?php echo e(__('ui.email_address')); ?> <span class="text-danger-500">*</span></label>
                                <input id="email" class="input-field" type="email" name="email" value="<?php echo e(old('email')); ?>" placeholder="contoh@gmail.com" autofocus />
                                <?php if (isset($component)) { $__componentOriginal824404ceeb4a1e7de17bfcaedf377360 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal824404ceeb4a1e7de17bfcaedf377360 = $attributes; } ?>
<?php $component = App\View\Components\InputError::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\InputError::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('email')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal824404ceeb4a1e7de17bfcaedf377360)): ?>
<?php $attributes = $__attributesOriginal824404ceeb4a1e7de17bfcaedf377360; ?>
<?php unset($__attributesOriginal824404ceeb4a1e7de17bfcaedf377360); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal824404ceeb4a1e7de17bfcaedf377360)): ?>
<?php $component = $__componentOriginal824404ceeb4a1e7de17bfcaedf377360; ?>
<?php unset($__componentOriginal824404ceeb4a1e7de17bfcaedf377360); ?>
<?php endif; ?>
                            </div>

                             <!-- Status -->
                             <div>
                                <label for="status" class="input-label"><?php echo e(__('ui.account_status')); ?></label>
                                <?php
                                    $statusOptions = [
                                        'active' => __('ui.active'),
                                        'inactive' => __('ui.inactive'),
                                    ];
                                ?>
                                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'status','options' => $statusOptions,'selected' => old('status', 'active'),'placeholder' => ''.e(__('ui.select_status')).'','width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'status','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($statusOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('status', 'active')),'placeholder' => ''.e(__('ui.select_status')).'','width' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                            </div>
                        </div>

                        <!-- Role & Job -->
                        <div class="space-y-4">
                             <h3 class="text-lg font-bold text-secondary-900 border-b border-secondary-100 pb-2"><?php echo e(__('ui.access_job')); ?></h3>

                            <!-- Role -->
                            <div>
                                <label for="role" class="input-label"><?php echo e(__('ui.access_role')); ?> <span class="text-danger-500">*</span></label>
                                <?php
                                    $roleOptions = [
                                        \App\Enums\UserRole::OPERATOR->value => __('ui.role_operator_desc'),
                                        \App\Enums\UserRole::ADMIN->value => __('ui.role_admin_desc'),
                                        \App\Enums\UserRole::SUPERADMIN->value => __('ui.role_superadmin_desc'),
                                    ];
                                ?>
                                <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'role','options' => $roleOptions,'selected' => old('role'),'placeholder' => ''.e(__('ui.select_role')).'','width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'role','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($roleOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(old('role')),'placeholder' => ''.e(__('ui.select_role')).'','width' => 'w-full']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $attributes = $__attributesOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__attributesOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
<?php if (isset($__componentOriginaled2cde6083938c436304f332ba96bb7c)): ?>
<?php $component = $__componentOriginaled2cde6083938c436304f332ba96bb7c; ?>
<?php unset($__componentOriginaled2cde6083938c436304f332ba96bb7c); ?>
<?php endif; ?>
                                <?php if (isset($component)) { $__componentOriginal824404ceeb4a1e7de17bfcaedf377360 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal824404ceeb4a1e7de17bfcaedf377360 = $attributes; } ?>
<?php $component = App\View\Components\InputError::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\InputError::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('role')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal824404ceeb4a1e7de17bfcaedf377360)): ?>
<?php $attributes = $__attributesOriginal824404ceeb4a1e7de17bfcaedf377360; ?>
<?php unset($__attributesOriginal824404ceeb4a1e7de17bfcaedf377360); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal824404ceeb4a1e7de17bfcaedf377360)): ?>
<?php $component = $__componentOriginal824404ceeb4a1e7de17bfcaedf377360; ?>
<?php unset($__componentOriginal824404ceeb4a1e7de17bfcaedf377360); ?>
<?php endif; ?>
                            </div>

                            <!-- Jabatan -->
                            <div>
                                <label for="jabatan" class="input-label"><?php echo e(__('ui.job_position')); ?> <span class="text-danger-500">*</span></label>
                                <input id="jabatan" class="input-field" type="text" name="jabatan" value="<?php echo e(old('jabatan')); ?>" placeholder="<?php echo e(__('ui.placeholder_job')); ?>" />
                                <?php if (isset($component)) { $__componentOriginal824404ceeb4a1e7de17bfcaedf377360 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal824404ceeb4a1e7de17bfcaedf377360 = $attributes; } ?>
<?php $component = App\View\Components\InputError::resolve([] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('input-error'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\App\View\Components\InputError::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['messages' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($errors->get('jabatan')),'class' => 'mt-2']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal824404ceeb4a1e7de17bfcaedf377360)): ?>
<?php $attributes = $__attributesOriginal824404ceeb4a1e7de17bfcaedf377360; ?>
<?php unset($__attributesOriginal824404ceeb4a1e7de17bfcaedf377360); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal824404ceeb4a1e7de17bfcaedf377360)): ?>
<?php $component = $__componentOriginal824404ceeb4a1e7de17bfcaedf377360; ?>
<?php unset($__componentOriginal824404ceeb4a1e7de17bfcaedf377360); ?>
<?php endif; ?>
                            </div>

                             <div class="bg-primary-50 border border-primary-100 rounded-lg p-4 flex items-start gap-3 mt-8">
                                <svg class="w-5 h-5 text-primary-600 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div class="text-sm text-primary-700">
                                    <p class="font-semibold"><?php echo e(__('ui.default_system_info')); ?></p>
                                    <ul class="list-disc list-inside mt-1 space-y-1 text-primary-600">
                                        <li><strong><?php echo e(__('ui.default_username_info')); ?></strong></li>
                                        <li><strong><?php echo e(__('ui.default_password_info')); ?></strong> <code>password123</code></li>
                                        <li><?php echo e(__('ui.user_can_set_username')); ?></li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-end gap-3 mt-8 pt-6 border-t border-secondary-100">
                        <a href="<?php echo e(route('users.index')); ?>" class="btn btn-secondary">
                            <?php echo e(__('ui.cancel')); ?>

                        </a>
                        <button type="submit" class="btn btn-primary">
                            <?php echo e(__('ui.save_user')); ?>

                        </button>
                    </div>
                </form>
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
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/users/create.blade.php ENDPATH**/ ?>