<div class="block md:hidden space-y-4">
    <?php if(request('trash') && $spareparts->count() > 0): ?>
        <div class="flex items-center gap-3 p-3 bg-white rounded-lg border border-secondary-200 shadow-sm">
            <input type="checkbox" id="mobile-select-all" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 w-5 h-5 transition-colors duration-200">
            <label for="mobile-select-all" class="text-sm font-semibold text-secondary-700 select-none cursor-pointer"><?php echo e(__('ui.select_all')); ?></label>
        </div>
    <?php endif; ?>
    <?php $__empty_1 = true; $__currentLoopData = $spareparts; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $sparepart): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
        <div class="card p-4">
            <!-- Header: Image, Name, Status -->
            <div class="flex items-start gap-3 mb-4">
                 <?php if(request('trash')): ?>
                    <div class="flex items-center self-center">
                        <input type="checkbox" value="<?php echo e($sparepart->id); ?>" class="bulk-checkbox rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50 w-5 h-5">
                    </div>
                <?php endif; ?>
                <!-- Type Indicator -->
                <div class="w-1 self-stretch rounded-full shrink-0 <?php echo e($sparepart->type === 'sale' ? 'bg-green-500 shadow-[0_0_6px_rgba(34,197,94,0.3)]' : 'bg-blue-500 shadow-[0_0_6px_rgba(59,130,246,0.3)]'); ?>" title="<?php echo e($sparepart->type === 'sale' ? 'Barang Dijual' : 'Aset Kantor'); ?>"></div>
                
                <!-- Image -->
                <div class="h-16 w-16 rounded-lg bg-secondary-100 flex items-center justify-center flex-shrink-0 text-secondary-400 overflow-hidden border border-secondary-200">
                    <?php if($sparepart->image): ?>
                        <img src="<?php echo e(asset('storage/' . $sparepart->image)); ?>" alt="<?php echo e($sparepart->name); ?>" loading="lazy" class="h-full w-full object-cover">
                    <?php else: ?>
                        <?php if (isset($component)) { $__componentOriginal6ad5678796219ea0d4d5fbde11a6790e = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal6ad5678796219ea0d4d5fbde11a6790e = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.image','data' => ['class' => 'w-8 h-8']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.image'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-8 h-8']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal6ad5678796219ea0d4d5fbde11a6790e)): ?>
<?php $attributes = $__attributesOriginal6ad5678796219ea0d4d5fbde11a6790e; ?>
<?php unset($__attributesOriginal6ad5678796219ea0d4d5fbde11a6790e); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal6ad5678796219ea0d4d5fbde11a6790e)): ?>
<?php $component = $__componentOriginal6ad5678796219ea0d4d5fbde11a6790e; ?>
<?php unset($__componentOriginal6ad5678796219ea0d4d5fbde11a6790e); ?>
<?php endif; ?>
                    <?php endif; ?>
                </div>
                
                <!-- Title & Badge -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-secondary-900 line-clamp-1">
                                    <a href="<?php echo e(route('inventory.show', $sparepart)); ?>">
                                        <?php echo e($sparepart->name); ?>

                                    </a>
                                </h3>
                                
                            </div>
                            <p class="text-xs text-secondary-500 font-mono mt-0.5"><?php echo e($sparepart->part_number); ?></p>
                        </div>
                        <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $sparepart->status,'class' => 'flex-shrink-0']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sparepart->status),'class' => 'flex-shrink-0']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $attributes = $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__attributesOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
<?php if (isset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62)): ?>
<?php $component = $__componentOriginal8c81617a70e11bcf247c4db924ab1b62; ?>
<?php unset($__componentOriginal8c81617a70e11bcf247c4db924ab1b62); ?>
<?php endif; ?>
                    </div>
                </div>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm mb-4 border-t border-b border-secondary-100 py-3">
                <!-- Brand & Category -->
                <div class="col-span-2 flex items-center justify-between">
                    <span class="text-secondary-500"><?php echo e(__('ui.brand')); ?> / <?php echo e(__('ui.category')); ?></span>
                    <span class="font-medium text-secondary-900 text-right truncate pl-2">
                        <?php echo e($sparepart->brand ?? '-'); ?> <span class="text-secondary-300 mx-1">|</span> <?php echo e($sparepart->category); ?>

                    </span>
                </div>

                <!-- Condition -->
                <div class="flex flex-col">
                    <span class="text-xs text-secondary-500 mb-1"><?php echo e(__('ui.condition')); ?></span>
                    <?php
                        $condition = $sparepart->condition ?? '-';
                        $conditionColor = match(strtolower($condition)) {
                            'baik' => 'text-success-700 bg-success-50 border-success-200',
                            'rusak' => 'text-danger-700 bg-danger-50 border-danger-200',
                            'hilang' => 'text-secondary-700 bg-secondary-100 border-secondary-200',
                            default => 'text-secondary-700 bg-secondary-50 border-secondary-200'
                        };
                    ?>
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border w-fit <?php echo e($conditionColor); ?>">
                        <?php echo e(ucfirst($condition)); ?>

                    </span>
                </div>

                <!-- Stock & Location -->
                <div class="flex flex-col items-end text-right">
                    <span class="text-xs text-secondary-500 mb-1"><?php echo e(__('ui.stock')); ?> di <?php echo e($sparepart->location); ?></span>
                    <div class="flex items-center gap-1.5">
                        <?php
                            $isLowStock = $sparepart->stock <= $sparepart->minimum_stock && !in_array(strtolower($sparepart->condition), ['rusak', 'hilang']);
                        ?>
                        <span class="text-lg font-bold <?php echo e($isLowStock ? 'text-danger-600' : 'text-secondary-900'); ?>">
                            <?php echo e($sparepart->stock); ?>

                        </span>
                        <span class="text-xs text-secondary-500"><?php echo e($sparepart->unit ?? 'Pcs'); ?></span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2">
                <?php if(request('trash')): ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('restore', $sparepart)): ?>
                    <form action="<?php echo e(route('inventory.restore', $sparepart->id)); ?>" method="POST" class="inline-block w-full sm:w-auto">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="btn btn-sm btn-success w-full justify-center flex items-center gap-1" onclick="confirmInventoryRestore(event)">
                            <?php if (isset($component)) { $__componentOriginald4bd00a03a971114f20525c4c2f7903f = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginald4bd00a03a971114f20525c4c2f7903f = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.icon.restore','data' => ['class' => 'w-4 h-4']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('icon.restore'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['class' => 'w-4 h-4']); ?>
<?php echo $__env->renderComponent(); ?>
<?php endif; ?>
<?php if (isset($__attributesOriginald4bd00a03a971114f20525c4c2f7903f)): ?>
<?php $attributes = $__attributesOriginald4bd00a03a971114f20525c4c2f7903f; ?>
<?php unset($__attributesOriginald4bd00a03a971114f20525c4c2f7903f); ?>
<?php endif; ?>
<?php if (isset($__componentOriginald4bd00a03a971114f20525c4c2f7903f)): ?>
<?php $component = $__componentOriginald4bd00a03a971114f20525c4c2f7903f; ?>
<?php unset($__componentOriginald4bd00a03a971114f20525c4c2f7903f); ?>
<?php endif; ?>
                            <?php echo e(__('ui.restore')); ?>

                        </button>
                    </form>
                    <?php endif; ?>
                <?php else: ?>
                    <a href="<?php echo e(route('inventory.show', $sparepart)); ?>" class="btn btn-sm btn-secondary flex-1 justify-center">
                        <?php echo e(__('ui.detail')); ?>

                    </a>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $sparepart)): ?>
                    <a href="<?php echo e(route('inventory.edit', $sparepart)); ?>" class="btn btn-sm btn-secondary flex-1 justify-center border-secondary-300 shadow-sm">
                        <?php echo e(__('ui.edit')); ?>

                    </a>
                    <?php endif; ?>
                    <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $sparepart)): ?>
                    <form action="<?php echo e(route('inventory.destroy', $sparepart)); ?>" method="POST" class="inline-block flex-1">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-sm btn-danger w-full justify-center" onclick="confirmDelete(event)">
                            <?php echo e(__('ui.delete')); ?>

                        </button>
                    </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
        <!-- Mobile Empty State -->
        <div class="card p-8 flex flex-col items-center justify-center text-center">
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
            
            <h3 class="text-lg font-medium text-secondary-900">
                <?php if(request('trash')): ?>
                    <?php echo e(__('ui.trash_empty')); ?>

                <?php elseif($isFiltered): ?>
                    <?php echo e(__('ui.no_results')); ?>

                <?php else: ?>
                    <?php echo e(__('ui.inventory_empty')); ?>

                <?php endif; ?>
            </h3>
            
            <p class="text-secondary-500 text-sm mt-1 max-w-xs mx-auto">
                <?php if(request('trash')): ?>
                    <?php echo e(__('ui.trash_empty_desc')); ?>

                <?php elseif($isFiltered): ?>
                    <?php echo e(__('ui.no_results_desc')); ?>

                <?php else: ?>
                    <?php echo e(__('ui.inventory_empty_desc')); ?>

                <?php endif; ?>
            </p>
        </div>
    <?php endif; ?>
    
    <!-- Mobile Pagination -->
    <div class="mt-4">
        <?php echo e($spareparts->links()); ?>

    </div>
</div>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/inventory/partials/mobile-list.blade.php ENDPATH**/ ?>