<div class="hidden md:block card overflow-hidden">
    <div class="overflow-x-auto">
        <table class="table-modern w-full table-fixed">
            <thead>
                <tr>
                    <?php if(request('trash')): ?>
                        <th class="w-[5%] px-4 py-3 text-center">
                            <input type="checkbox" id="select-all" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
                        </th>
                    <?php endif; ?>
                    <th class="px-4 py-3 text-left text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[20%]"><?php echo e(__('ui.name')); ?></th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]"><?php echo e(__('ui.brand')); ?></th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]"><?php echo e(__('ui.category')); ?></th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]"><?php echo e(__('ui.condition')); ?></th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[10%]"><?php echo e(__('ui.color')); ?></th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[12%]"><?php echo e(__('ui.location')); ?></th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[10%]"><?php echo e(__('ui.stock')); ?></th>
                    <th class="px-4 py-3 text-center text-xs font-semibold text-secondary-500 uppercase tracking-wider w-[14%]"><?php echo e(__('ui.actions')); ?></th>
                </tr>
            </thead>
            <tbody>
                <?php $__empty_1 = true; $__currentLoopData = $spareparts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sparepart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <?php if (isset($component)) { $__componentOriginal964b6f244adbc27a2b6ba9f2fe9c1f4a = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal964b6f244adbc27a2b6ba9f2fe9c1f4a = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.inventory.table-row','data' => ['sparepart' => $sparepart,'trash' => request('trash')]] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('inventory.table-row'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['sparepart' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sparepart),'trash' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute(request('trash'))]); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal964b6f244adbc27a2b6ba9f2fe9c1f4a)): ?>
<?php $attributes = $__attributesOriginal964b6f244adbc27a2b6ba9f2fe9c1f4a; ?>
<?php unset($__attributesOriginal964b6f244adbc27a2b6ba9f2fe9c1f4a); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal964b6f244adbc27a2b6ba9f2fe9c1f4a)): ?>
<?php $component = $__componentOriginal964b6f244adbc27a2b6ba9f2fe9c1f4a; ?>
<?php unset($__componentOriginal964b6f244adbc27a2b6ba9f2fe9c1f4a); ?>
<?php endif; ?>
                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="<?php echo e(request('trash') ? '9' : '8'); ?>" class="px-6 py-12 text-center text-secondary-500">
                            <div class="flex flex-col items-center justify-center w-full">
                                <?php
                                    $isFiltered = request('search') || request('category') || request('brand') || request('location') || request('color');
                                ?>

                                <div class="h-16 w-16 bg-secondary-100 text-secondary-400 rounded-full flex items-center justify-center mb-4 shadow-sm border border-secondary-200">
                                    <?php if(request('trash')): ?>
                                        
                                        <?php if (isset($component)) { $__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.trash','data' => ['class' => 'w-8 h-8 text-danger-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.trash'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 text-danger-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8)): ?>
<?php $attributes = $__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8; ?>
<?php unset($__attributesOriginal1bc295e5c424e8fa8f76ad875cdf51d8); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8)): ?>
<?php $component = $__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8; ?>
<?php unset($__componentOriginal1bc295e5c424e8fa8f76ad875cdf51d8); ?>
<?php endif; ?>
                                    <?php elseif($isFiltered): ?>
                                        
                                        <?php if (isset($component)) { $__componentOriginal60b104b2fde947186a9c15caab3ac427 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal60b104b2fde947186a9c15caab3ac427 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.search','data' => ['class' => 'w-8 h-8 text-secondary-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.search'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 text-secondary-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal60b104b2fde947186a9c15caab3ac427)): ?>
<?php $attributes = $__attributesOriginal60b104b2fde947186a9c15caab3ac427; ?>
<?php unset($__attributesOriginal60b104b2fde947186a9c15caab3ac427); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal60b104b2fde947186a9c15caab3ac427)): ?>
<?php $component = $__componentOriginal60b104b2fde947186a9c15caab3ac427; ?>
<?php unset($__componentOriginal60b104b2fde947186a9c15caab3ac427); ?>
<?php endif; ?>
                                    <?php else: ?>
                                        
                                        <?php if (isset($component)) { $__componentOriginal301cfb102074bf3551f33dfa8e899355 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal301cfb102074bf3551f33dfa8e899355 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.box','data' => ['class' => 'w-8 h-8 text-secondary-400']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.box'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8 text-secondary-400']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal301cfb102074bf3551f33dfa8e899355)): ?>
<?php $attributes = $__attributesOriginal301cfb102074bf3551f33dfa8e899355; ?>
<?php unset($__attributesOriginal301cfb102074bf3551f33dfa8e899355); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal301cfb102074bf3551f33dfa8e899355)): ?>
<?php $component = $__componentOriginal301cfb102074bf3551f33dfa8e899355; ?>
<?php unset($__componentOriginal301cfb102074bf3551f33dfa8e899355); ?>
<?php endif; ?>
                                    <?php endif; ?>
                                </div>

                                <p class="text-lg font-bold text-secondary-900 tracking-tight">
                                    <?php if(request('trash')): ?>
                                        <?php echo e(__('ui.trash_empty')); ?>

                                    <?php elseif($isFiltered): ?>
                                        <?php echo e(__('ui.no_results')); ?>

                                    <?php else: ?>
                                        <?php echo e(__('ui.inventory_empty')); ?>

                                    <?php endif; ?>
                                </p>

                                <p class="text-sm mt-1 max-w-sm mx-auto leading-relaxed text-center text-secondary-500">
                                    <?php if(request('trash')): ?>
                                        <?php echo e(__('ui.trash_empty_desc')); ?>

                                    <?php elseif($isFiltered): ?>
                                        <?php echo e(__('ui.no_results_desc')); ?>

                                    <?php else: ?>
                                        <?php echo e(__('ui.inventory_empty_desc')); ?>

                                    <?php endif; ?>
                                </p>
                            </div>
                        </td>
                    </tr>
                <?php endif; ?>
            </tbody>
            
            <!-- Skeleton Body (Hidden by default) -->
            <!-- High-Quality Skeleton Body -->
            <tbody id="skeleton-body" class="hidden divide-y divide-secondary-100 bg-white">
                <?php for($i = 0; $i < 5; $i++): ?>
                    <tr>
                        <?php if(request('trash')): ?>
                            <td class="px-4 py-4 text-center">
                                <div class="h-4 w-4 bg-secondary-100 rounded animate-pulse mx-auto"></div>
                            </td>
                        <?php endif; ?>
                        <!-- Name & Image Column -->
                        <td class="px-4 py-4">
                            <div class="flex items-center gap-3">
                                <div class="w-1.5 h-1.5 rounded-full bg-secondary-200 animate-pulse flex-shrink-0"></div> <!-- Status Dot -->
                                <div class="h-10 w-10 bg-secondary-100 rounded-lg animate-pulse flex-shrink-0"></div> <!-- Image -->
                                <div class="space-y-2 flex-1 min-w-0">
                                    <div class="h-4 w-32 bg-secondary-100 rounded animate-pulse"></div> <!-- Name -->
                                    <div class="h-3 w-20 bg-secondary-50 rounded animate-pulse"></div>  <!-- Part Number -->
                                </div>
                            </div>
                        </td>
                        <!-- Brand -->
                        <td class="px-4 py-4 text-center">
                            <div class="h-4 w-20 bg-secondary-50 rounded animate-pulse mx-auto"></div>
                        </td>
                        <!-- Category -->
                        <td class="px-4 py-4 text-center">
                            <div class="h-5 w-24 bg-secondary-100 rounded-full animate-pulse mx-auto"></div>
                        </td>
                        <!-- Color -->
                        <td class="px-4 py-4 text-center">
                                <div class="h-4 w-16 bg-secondary-50 rounded animate-pulse mx-auto"></div>
                        </td>
                        <!-- Location -->
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-center justify-center gap-1.5">
                                    <div class="h-4 w-4 bg-secondary-100 rounded-full animate-pulse"></div>
                                    <div class="h-4 w-16 bg-secondary-50 rounded animate-pulse"></div>
                            </div>
                        </td>
                        <!-- Stock -->
                        <td class="px-4 py-4 text-center">
                            <div class="flex items-baseline justify-center gap-1">
                                <div class="h-5 w-8 bg-secondary-100 rounded animate-pulse"></div>
                                <div class="h-3 w-6 bg-secondary-50 rounded animate-pulse"></div>
                            </div>
                        </td>
                        <!-- Actions -->
                        <td class="px-4 py-4">
                            <div class="flex items-center justify-center gap-2">
                                <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                                <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                                <div class="h-8 w-8 bg-secondary-50 rounded-lg animate-pulse"></div>
                            </div>
                        </td>
                    </tr>
                <?php endfor; ?>
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <?php if($spareparts->hasPages()): ?>
        <div class="bg-secondary-50 px-4 py-3 border-t border-secondary-200 sm:px-6">
            <?php echo e($spareparts->appends(request()->query())->links()); ?>

        </div>
    <?php endif; ?>
</div>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/inventory/partials/desktop-table.blade.php ENDPATH**/ ?>