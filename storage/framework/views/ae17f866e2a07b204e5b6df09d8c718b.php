<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['sparepart', 'trash' => false]));

foreach ($attributes->all() as $__key => $__value) {
    if (in_array($__key, $__propNames)) {
        $$__key = $$__key ?? $__value;
    } else {
        $__newAttributes[$__key] = $__value;
    }
}

$attributes = new \Illuminate\View\ComponentAttributeBag($__newAttributes);

unset($__propNames);
unset($__newAttributes);

foreach (array_filter((['sparepart', 'trash' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<tr class="group hover:bg-secondary-50/60 transition-colors border-b border-secondary-50 last:border-b-0">
    <?php if($trash): ?>
        <td class="px-4 py-3 text-center">
            <input type="checkbox" name="ids[]" value="<?php echo e($sparepart->id); ?>" class="bulk-checkbox rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
        </td>
    <?php endif; ?>
    <td class="px-4 py-3">
        <div class="flex items-center gap-3">
            <?php if (isset($component)) { $__componentOriginal8c81617a70e11bcf247c4db924ab1b62 = $component; } ?>
<?php if (isset($attributes)) { $__attributesOriginal8c81617a70e11bcf247c4db924ab1b62 = $attributes; } ?>
<?php $component = Illuminate\View\AnonymousComponent::resolve(['view' => 'components.status-badge','data' => ['status' => $sparepart->status,'class' => 'w-1.5 h-1.5']] + (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag ? $attributes->all() : [])); ?>
<?php $component->withName('status-badge'); ?>
<?php if ($component->shouldRender()): ?>
<?php $__env->startComponent($component->resolveView(), $component->data()); ?>
<?php if (isset($attributes) && $attributes instanceof Illuminate\View\ComponentAttributeBag): ?>
<?php $attributes = $attributes->except(\Illuminate\View\AnonymousComponent::ignoredParameterNames()); ?>
<?php endif; ?>
<?php $component->withAttributes(['status' => \Illuminate\View\Compilers\BladeCompiler::sanitizeComponentAttribute($sparepart->status),'class' => 'w-1.5 h-1.5']); ?>
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
            
            <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 relative overflow-hidden group-hover:ring-2 ring-white transition-all">
                <?php if($sparepart->image): ?>
                    <img src="<?php echo e(asset('storage/' . $sparepart->image)); ?>" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover rounded-lg group-hover:scale-110 transition-transform duration-500">
                <?php else: ?>
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <?php endif; ?>
            </div>
            
            <!-- Vertical Line Indicator -->
            <div class="hidden sm:block w-1 self-stretch rounded-full mr-3 shrink-0 <?php echo e($sparepart->type === 'sale' ? 'bg-green-500 shadow-[0_0_6px_rgba(34,197,94,0.3)]' : 'bg-blue-500 shadow-[0_0_6px_rgba(59,130,246,0.3)]'); ?>" title="<?php echo e($sparepart->type === 'sale' ? 'Barang Dijual' : 'Aset Kantor'); ?>"></div>

            <div class="min-w-0">
                <div class="flex items-center gap-1.5 mb-0.5">
                    <a href="<?php echo e(route('inventory.show', $sparepart)); ?>" class="font-medium text-secondary-900 group-hover:text-primary-600 transition-colors block truncate" title="<?php echo e($sparepart->name); ?>">
                        <?php echo e($sparepart->name); ?>

                    </a>
                </div>
                <span class="text-xs text-secondary-500 font-mono truncate block"><?php echo e($sparepart->part_number); ?></span>
            </div>
        </div>
    </td>
    <td class="px-4 py-3 text-center">
        <span class="text-sm text-secondary-700 font-medium truncate block">
            <?php echo e($sparepart->brand ?? '-'); ?>

        </span>
    </td>
    <td class="px-4 py-3 text-center">
        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-secondary-100 text-secondary-600 truncate border border-secondary-200">
            <?php echo e($sparepart->category); ?>

        </span>
    </td>
    <!-- Condition Column -->
    <td class="px-4 py-3 text-center">
        <div class="flex flex-col items-center justify-center gap-0.5">
            <?php
                $condition = $sparepart->condition ?? '-';
                $age = $sparepart->age === 'Pernah Dipakai (Bekas)' ? 'Bekas' : ($sparepart->age ?? '-');
                
                $conditionColor = match(strtolower($condition)) {
                    'baik' => 'text-success-600 bg-success-50/50 border-success-100',
                    'rusak' => 'text-danger-600 bg-danger-50/50 border-danger-100',
                    'hilang' => 'text-secondary-600 bg-secondary-100 border-secondary-200',
                    default => 'text-secondary-600 bg-secondary-50 border-secondary-100'
                };
            ?>
            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold border <?php echo e($conditionColor); ?>">
                <?php echo e(ucfirst($condition)); ?>

            </span>
            <span class="text-[10px] text-secondary-400 font-medium">
                <?php echo e($age); ?>

            </span>
        </div>
    </td>
    <td class="px-4 py-3 text-center">
        <span class="text-sm text-secondary-700 truncate block"><?php echo e($sparepart->color ?? '-'); ?></span>
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center justify-center gap-1.5 text-secondary-600 text-sm truncate bg-secondary-50/50 px-2 py-1 rounded transition-colors">
            <svg class="w-4 h-4 text-secondary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
            <span class="truncate"><?php echo e($sparepart->location); ?></span>
        </div>
    </td>
    <td class="px-4 py-3">
        <div class="flex items-baseline justify-center gap-1">
            <?php
                $isLowStock = $sparepart->stock <= $sparepart->minimum_stock && !in_array(strtolower($sparepart->condition), ['rusak', 'hilang']);
            ?>
            
            <?php if($isLowStock): ?>
                <div class="relative group self-center" title="<?php echo e(__('ui.low_stock')); ?>">
                    <svg class="w-4 h-4 text-danger-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                </div>
            <?php endif; ?>
            <span class="text-base font-bold <?php echo e($isLowStock ? 'text-danger-600' : 'text-secondary-900'); ?>">
                <?php echo e($sparepart->stock); ?>

            </span>
            <span class="text-xs text-secondary-500"><?php echo e($sparepart->unit ?? 'Pcs'); ?></span>
        </div>
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center justify-center gap-2">
            <?php if($trash): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('restore', $sparepart)): ?>
                <form action="<?php echo e(route('inventory.restore', $sparepart->id)); ?>" method="POST" class="inline-block">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('PATCH'); ?>
                    <button type="submit" class="btn btn-ghost p-2 text-success-600 hover:text-success-700 bg-success-50 hover:bg-success-100 rounded-lg transition-all" title="<?php echo e(__('ui.restore')); ?>" onclick="confirmInventoryRestore(event)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                    </button>
                </form>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('forceDelete', $sparepart)): ?>
                <form action="<?php echo e(route('inventory.force-delete', $sparepart->id)); ?>" method="POST" class="inline-block">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-ghost p-2 text-danger-600 hover:text-danger-700 bg-danger-50 hover:bg-danger-100 rounded-lg transition-all" title="<?php echo e(__('ui.force_delete')); ?>" onclick="confirmInventoryForceDelete(event)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?php echo e(route('inventory.show', $sparepart)); ?>" class="btn btn-ghost p-2 text-secondary-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all" title="<?php echo e(__('ui.detail')); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </a>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $sparepart)): ?>
                <a href="<?php echo e(route('inventory.edit', $sparepart)); ?>" class="btn btn-ghost p-2 text-warning-600 hover:text-warning-700 hover:bg-warning-50 rounded-lg transition-all" title="<?php echo e(__('ui.edit')); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                </a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $sparepart)): ?>
                <form action="<?php echo e(route('inventory.destroy', $sparepart)); ?>" method="POST" class="inline-block">
                    <?php echo csrf_field(); ?>
                    <?php echo method_field('DELETE'); ?>
                    <button type="submit" class="btn btn-ghost p-2 text-danger-600 hover:text-danger-700 hover:bg-danger-50 rounded-lg transition-all" title="<?php echo e(__('ui.delete')); ?>" onclick="confirmDelete(event)">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                    </button>
                </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/components/inventory/table-row.blade.php ENDPATH**/ ?>