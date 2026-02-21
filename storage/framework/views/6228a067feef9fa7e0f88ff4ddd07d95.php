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
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8" x-data="{ showFilters: false }">
            <!-- Header & Actions -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        <?php echo e(__('ui.activity_logs_title')); ?>

                    </h2>
                    <p class="mt-1 text-sm text-secondary-500"><?php echo e(__('ui.activity_logs_desc')); ?></p>
                </div>
                
                <div class="flex gap-2">
                    <!-- Export Dropdown -->
                    <div x-data="{ open: false }" class="relative">
                        <button @click="open = !open" @click.away="open = false" class="btn btn-outline-secondary flex items-center gap-2">
                            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4" />
                            </svg>
                            <span><?php echo e(__('ui.export_data')); ?></span>
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>
                        <div x-show="open" 
                             class="absolute left-0 sm:left-auto sm:right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-secondary-100"
                             style="display: none;">
                            <a href="<?php echo e(route('reports.activity-logs.export', array_merge(request()->query(), ['format' => 'pdf']))); ?>" class="px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                <?php echo e(__('ui.export_pdf')); ?>

                            </a>
                            <a href="<?php echo e(route('reports.activity-logs.export', array_merge(request()->query(), ['format' => 'csv']))); ?>" class="px-4 py-2 text-sm text-secondary-700 hover:bg-secondary-50 flex items-center gap-2">
                                <svg class="w-4 h-4 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                <?php echo e(__('ui.export_excel')); ?>

                            </a>
                        </div>
                    </div>

                    <button @click="showFilters = !showFilters" 
                            class="btn btn-secondary flex items-center gap-2"
                            :class="{ 'bg-secondary-100 ring-2 ring-secondary-200': showFilters }">
                        <svg class="w-5 h-5 text-secondary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"></path></svg>
                        <span><?php echo e(__('ui.filter_search')); ?></span>
                        <svg class="w-4 h-4 text-secondary-400 transition-transform duration-200" :class="{ 'rotate-180': showFilters }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </button>
                </div>
            </div>

            <!-- Collabsible Filter Section -->
            <div x-show="showFilters" 
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform -translate-y-2"
                 x-transition:enter-end="opacity-100 transform translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 transform translate-y-0"
                 x-transition:leave-end="opacity-0 transform -translate-y-2"
                 class="mb-6"
                 style="display: none;">
                 
                <form action="<?php echo e(route('reports.activity-logs.index')); ?>" method="GET" class="card p-6 border border-secondary-200 shadow-lg overflow-visible">
                    <div class="grid grid-cols-1 sm:grid-cols-2 <?php echo e(auth()->user()->role === \App\Enums\UserRole::OPERATOR ? 'lg:grid-cols-5' : 'lg:grid-cols-4'); ?> gap-4 items-end">
                        
                        <!-- Search -->
                        <div class="space-y-1">
                            <label for="search" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider"><?php echo e(__('ui.search_keyword')); ?></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                </span>
                                <input type="text" name="search" id="search" value="<?php echo e(request('search')); ?>" placeholder="Deskripsi..."
                                    class="form-input pl-10 block w-full rounded-lg border-secondary-300 focus:ring-primary-500 focus:border-primary-500 text-sm h-[42px]">
                            </div>
                        </div>

                        <!-- Role Filter -->
                        <?php if(auth()->user()->role !== \App\Enums\UserRole::OPERATOR): ?>
                        <div class="space-y-1">
                            <label for="role" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider"><?php echo e(__('ui.role_filter')); ?></label>
                            <?php
                                $roleOptions = [];
                                if (auth()->user()->role === \App\Enums\UserRole::SUPERADMIN) {
                                    $roleOptions[\App\Enums\UserRole::SUPERADMIN->value] = \App\Enums\UserRole::SUPERADMIN->label();
                                }
                                $roleOptions[\App\Enums\UserRole::ADMIN->value] = \App\Enums\UserRole::ADMIN->label();
                                $roleOptions[\App\Enums\UserRole::OPERATOR->value] = \App\Enums\UserRole::OPERATOR->label();
                            ?>
                            <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'role','options' => $roleOptions,'selected' => request('role'),'placeholder' => ''.e(__('ui.all_roles')).'','width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'role','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($roleOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('role')),'placeholder' => ''.e(__('ui.all_roles')).'','width' => 'w-full']); ?>
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
                        <?php endif; ?>

                        <!-- Action Filter -->
                        <div class="space-y-1">
                            <label for="action" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider"><?php echo e(__('ui.action_type')); ?></label>
                            <?php
                                $actionOptions = $actions->mapWithKeys(fn($item) => [$item => $item])->toArray();
                            ?>
                            <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'action','options' => $actionOptions,'selected' => request('action'),'placeholder' => ''.e(__('ui.all_actions')).'','width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'action','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($actionOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('action')),'placeholder' => ''.e(__('ui.all_actions')).'','width' => 'w-full']); ?>
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

                        <!-- User Filter -->
                        <?php if(auth()->user()->role !== \App\Enums\UserRole::OPERATOR): ?>
                        <div class="space-y-1">
                            <label for="user_id" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider"><?php echo e(__('ui.user_filter')); ?></label>
                            <?php
                                $userOptions = $users->pluck('name', 'id')->toArray();
                            ?>
                            <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'user_id','options' => $userOptions,'selected' => request('user_id'),'placeholder' => ''.e(__('ui.all_users')).'','width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'user_id','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($userOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('user_id')),'placeholder' => ''.e(__('ui.all_users')).'','width' => 'w-full']); ?>
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
                        <?php endif; ?>

                        <!-- Date Start -->
                        <div class="space-y-1">
                            <label for="start_date" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider"><?php echo e(__('ui.from_date')); ?></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </span>
                                <input type="date" name="start_date" id="start_date" value="<?php echo e(request('start_date')); ?>" 
                                    class="form-input pl-10 block w-full rounded-lg border-secondary-300 focus:ring-primary-500 focus:border-primary-500 text-sm h-[42px]">
                            </div>
                        </div>

                        <!-- Date End -->
                        <div class="space-y-1">
                            <label for="end_date" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider"><?php echo e(__('ui.to_date')); ?></label>
                            <div class="relative">
                                <span class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-secondary-400">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </span>
                                <input type="date" name="end_date" id="end_date" value="<?php echo e(request('end_date')); ?>" 
                                    class="form-input pl-10 block w-full rounded-lg border-secondary-300 focus:ring-primary-500 focus:border-primary-500 text-sm h-[42px]">
                            </div>
                        </div>

                        <!-- NEW: Subject Type Filter -->
                        <?php if(auth()->user()->role !== \App\Enums\UserRole::OPERATOR): ?>
                        <div class="space-y-1">
                            <label for="subject_type" class="text-xs font-semibold text-secondary-600 uppercase tracking-wider"><?php echo e(__('ui.subject_type')); ?></label>
                            <?php
                                $subjectOptions = [
                                    'inventory' => __('ui.subject_inventory'),
                                    'user' => __('ui.subject_user'),
                                    'auth' => __('ui.subject_auth'),
                                    'report' => __('ui.subject_report'),
                                ];
                            ?>
                            <?php if (isset($component)) { $__componentOriginaled2cde6083938c436304f332ba96bb7c = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginaled2cde6083938c436304f332ba96bb7c = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.select','data' => ['name' => 'subject_type','options' => $subjectOptions,'selected' => request('subject_type'),'placeholder' => ''.e(__('ui.all_types')).'','width' => 'w-full']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('select'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['name' => 'subject_type','options' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($subjectOptions),'selected' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('subject_type')),'placeholder' => ''.e(__('ui.all_types')).'','width' => 'w-full']); ?>
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
                        <?php endif; ?>

                        <!-- Buttons -->
                        <div class="flex gap-2 w-full">
                             <button type="submit" class="btn btn-primary flex-1 justify-center flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                                <?php echo e(__('ui.apply_filter')); ?>

                            </button>
                            <a href="<?php echo e(route('reports.activity-logs.index')); ?>" class="btn btn-secondary flex items-center justify-center gap-2 px-3" title="<?php echo e(__('ui.reset_filter')); ?>">
                                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" />
                                </svg>
                            </a>
                        </div>
                    </div>
                </form>
            </div>



            <!-- Desktop Table View -->
            <div class="hidden md:block card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th><?php echo e(__('ui.user_header')); ?></th>
                                <th><?php echo e(__('ui.action_header')); ?></th>
                                <th><?php echo e(__('ui.description_header')); ?></th>
                                <th><?php echo e(__('ui.time_header')); ?></th>
                            </tr>
                        </thead>
                        <tbody id="desktop-logs-body">
                            <?php $__empty_1 = true; $__currentLoopData = $activityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                <tr class="group hover:bg-secondary-50 transition-colors">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                                <?php if($log->user && $log->user->avatar): ?>
                                                    <img src="<?php echo e(asset('storage/' . $log->user->avatar)); ?>" alt="" class="h-full w-full object-cover rounded-full">
                                                <?php else: ?>
                                                    <span class="font-bold text-xs"><?php echo e(substr($log->user->name ?? 'S', 0, 1)); ?></span>
                                                <?php endif; ?>
                                            </div>
                                            <div>
                                                <div class="font-medium text-secondary-900"><?php echo e($log->user->name ?? __('ui.system_user')); ?></div>
                                                <div class="text-xs text-secondary-500 font-mono"><?php echo e($log->user->email ?? '-'); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-secondary-100 text-secondary-800">
                                            <?php echo e($log->action); ?>

                                        </span>
                                    </td>
                                    <td class="text-sm text-secondary-600">
                                        <div class="max-w-lg whitespace-normal break-words">
                                            <?php echo e($log->description); ?>

                                        </div>
                                    </td>
                                    <td class="text-sm text-secondary-500 whitespace-nowrap">
                                        <?php echo e($log->created_at->format('d M Y H:i:s')); ?>

                                    </td>
                                </tr>
                            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                <tr>
                                    <td colspan="4" class="p-8 text-center text-secondary-500">
                                        <?php echo e(__('ui.no_activity_logs')); ?>

                                    </td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div id="mobile-logs-container" class="md:hidden space-y-4">
                <?php $__empty_1 = true; $__currentLoopData = $activityLogs; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $log): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <div class="card p-4 flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 font-bold overflow-hidden">
                                     <?php if($log->user && $log->user->avatar): ?>
                                        <img src="<?php echo e(asset('storage/' . $log->user->avatar)); ?>" alt="" class="h-full w-full object-cover">
                                    <?php else: ?>
                                        <?php echo e(substr($log->user->name ?? 'S', 0, 1)); ?>

                                    <?php endif; ?>
                                </div>
                                <div>
                                    <div class="font-bold text-secondary-900"><?php echo e($log->user->name ?? __('ui.system_user')); ?></div>
                                    <div class="text-xs text-secondary-500"><?php echo e($log->created_at->diffForHumans()); ?></div>
                                </div>
                            </div>
                            <span class="badge badge-secondary text-[10px]"><?php echo e($log->action); ?></span>
                        </div>
                        
                        <div class="text-sm text-secondary-600 bg-secondary-50 p-3 rounded-lg border border-secondary-100">
                            <?php echo e($log->description); ?>

                        </div>

                        <div class="text-xs text-secondary-400 text-right">
                            <?php echo e($log->created_at->format('d M Y • H:i:s')); ?>

                        </div>
                    </div>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <div class="card p-8 text-center text-secondary-500">
                        <p class="text-sm"><?php echo e(__('ui.no_activity_logs')); ?></p>
                    </div>
                <?php endif; ?>
            </div>

            <div class="mt-6">
                <?php echo e($activityLogs->links()); ?>

            </div>
        </div>
    </div>

    <!-- Script Realtime Log -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (window.Echo) {
                window.Echo.channel('activity-logs')
                    .listen('ActivityLogged', (e) => {
                        console.log('Log received:', e);
                        
                        // 1. Desktop Update
                        const desktopBody = document.getElementById('desktop-logs-body');
                        if (desktopBody) {
                             // Remove 'Tidak ada aktivitas' if exists
                             const emptyRow = desktopBody.querySelector('td[colspan="4"]');
                             if (emptyRow) emptyRow.closest('tr').remove();

                             const newRow = `
                                <tr class="group hover:bg-secondary-50 transition-colors animate-fade-in-down">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="h-8 w-8 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                                <span class="font-bold text-xs">${e.user_name.charAt(0)}</span>
                                            </div>
                                            <div>
                                                <div class="font-medium text-secondary-900">${e.user_name}</div>
                                                <div class="text-xs text-secondary-500 font-mono">${e.user_role}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-secondary-100 text-secondary-800">
                                            ${e.action}
                                        </span>
                                    </td>
                                    <td class="text-sm text-secondary-600">
                                        <div class="max-w-lg whitespace-normal break-words">
                                            ${e.description}
                                        </div>
                                    </td>
                                    <td class="text-sm text-secondary-500 whitespace-nowrap">
                                        ${new Date().toLocaleString('id-ID')}
                                    </td>
                                </tr>
                             `;
                             desktopBody.insertAdjacentHTML('afterbegin', newRow);
                        }

                        // 2. Mobile Update
                        const mobileContainer = document.getElementById('mobile-logs-container');
                        if (mobileContainer) {
                             // Remove empty placeholder
                             const emptyCard = mobileContainer.querySelector('.text-center');
                             if (emptyCard) emptyCard.parentElement.remove();

                             const newCard = `
                                <div class="card p-4 flex flex-col gap-3 animate-fade-in-down">
                                    <div class="flex items-start justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-full bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 font-bold overflow-hidden">
                                                ${e.user_name.charAt(0)}
                                            </div>
                                            <div>
                                                <div class="font-bold text-secondary-900">${e.user_name}</div>
                                                <div class="text-xs text-secondary-500"><?php echo e(__('ui.just_now')); ?></div>
                                            </div>
                                        </div>
                                        <span class="badge badge-secondary text-[10px]">${e.action}</span>
                                    </div>
                                    <div class="text-sm text-secondary-600 bg-secondary-50 p-3 rounded-lg border border-secondary-100">
                                        ${e.description}
                                    </div>
                                    <div class="text-xs text-secondary-400 text-right">
                                        ${new Date().toLocaleString('id-ID')}
                                    </div>
                                </div>
                             `;
                             mobileContainer.insertAdjacentHTML('afterbegin', newCard);
                        }
                    });
            }
        });
    </script>
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
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/reports/activity_logs/index.blade.php ENDPATH**/ ?>