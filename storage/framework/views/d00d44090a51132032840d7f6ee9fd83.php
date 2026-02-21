<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['approval']));

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

foreach (array_filter((['approval']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<div class="card p-4 flex flex-col gap-3 h-full">
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 relative overflow-hidden group">
               <?php if($approval->sparepart->image): ?>
                    <img src="<?php echo e(asset('storage/' . $approval->sparepart->image)); ?>" alt="" class="h-full w-full object-cover rounded-lg">
                <?php else: ?>
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                <?php endif; ?>
            </div>
            <div>
                <div class="font-bold text-secondary-900 line-clamp-1"><?php echo e($approval->sparepart->name); ?></div>
                <div class="text-xs text-secondary-500"><?php echo e(__('ui.by_user', ['name' => $approval->user->name])); ?></div>
            </div>
        </div>
       
         <?php if($approval->type === 'masuk'): ?>
            <span class="badge badge-success text-[10px]"><?php echo e(__('ui.type_in')); ?></span>
        <?php else: ?>
            <span class="badge badge-warning text-[10px]"><?php echo e(__('ui.type_out')); ?></span>
        <?php endif; ?>
    </div>
    
    <div class="grid grid-cols-2 gap-2 text-sm border-t border-b border-secondary-50 py-3">
        <div>
            <span class="text-xs text-secondary-400 block"><?php echo e(__('ui.amount_column')); ?></span>
            <span class="font-bold text-secondary-900"><?php echo e($approval->quantity); ?> <span class="text-xs font-normal text-secondary-500"><?php echo e($approval->sparepart->unit ?? __('ui.unit_pcs')); ?></span></span>
        </div>
        <div class="text-right">
            <span class="text-xs text-secondary-400 block"><?php echo e(__('ui.date_column')); ?></span>
            <span class="text-secondary-700"><?php echo e($approval->created_at->format('d/m/y H:i')); ?></span>
        </div>
        <div class="col-span-2 mt-1">
            <span class="text-xs text-secondary-400 block"><?php echo e(__('ui.reason_label')); ?></span>
            <span class="text-secondary-700 bg-secondary-50 p-2 rounded block w-full text-xs border border-secondary-100"><?php echo e($approval->reason); ?></span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 pt-1 mt-auto">
        <form action="<?php echo e(route('inventory.stock-approvals.update', $approval)); ?>" method="POST" class="w-full">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <input type="hidden" name="status" value="rejected">
            <button type="submit" class="btn btn-outline-danger w-full text-xs justify-center" onclick="confirmReject(event)">
                <?php echo e(__('ui.btn_reject')); ?>

            </button>
        </form>
        <form action="<?php echo e(route('inventory.stock-approvals.update', $approval)); ?>" method="POST" class="w-full">
            <?php echo csrf_field(); ?>
            <?php echo method_field('PATCH'); ?>
            <input type="hidden" name="status" value="approved">
            <button type="submit" class="btn btn-success w-full text-xs justify-center flex items-center py-2">
                <?php echo e(__('ui.btn_approve')); ?>

            </button>
        </form>
    </div>
</div>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/components/approval/card.blade.php ENDPATH**/ ?>