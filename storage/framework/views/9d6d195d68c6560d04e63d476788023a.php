<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['user', 'trash' => false]));

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

foreach (array_filter((['user', 'trash' => false]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
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
            <input type="checkbox" name="ids[]" value="<?php echo e($user->id); ?>" class="user-checkbox rounded border-secondary-300 text-primary-600 shadow-sm focus:border-primary-300 focus:ring focus:ring-primary-200 focus:ring-opacity-50">
        </td>
    <?php endif; ?>
    <td class="px-4 py-3">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-full bg-secondary-100 flex-shrink-0 overflow-hidden border border-secondary-200 group-hover:border-primary-200 transition-colors">
                <img src="<?php echo e($user->avatar_url); ?>" alt="<?php echo e($user->name); ?>" class="h-full w-full object-cover">
            </div>
            <div>
                <div class="font-medium text-secondary-900 group-hover:text-primary-600 transition-colors"><?php echo e($user->name); ?></div>
                <div class="text-xs text-secondary-500 font-mono">@ <?php echo e($user->username); ?></div>
            </div>
        </div>
    </td>
    <td class="px-4 py-3">
        <div class="text-sm text-secondary-900"><?php echo e($user->email); ?></div>
        <?php if($user->phone): ?>
            <a href="https://wa.me/<?php echo e(preg_replace('/^0/', '62', preg_replace('/[^0-9]/', '', $user->phone))); ?>" target="_blank" class="text-xs text-success-600 hover:text-success-700 flex items-center gap-1 mt-0.5">
                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413Z"/></svg>
                <?php echo e($user->phone); ?>

            </a>
        <?php endif; ?>
    </td>
    <td class="px-4 py-3 text-sm text-secondary-600"><?php echo e($user->jabatan ?? '-'); ?></td>
    <td class="px-4 py-3">
        <?php
            $roleColors = [
                \App\Enums\UserRole::SUPERADMIN->value => 'text-purple-700 bg-purple-50 border-purple-100',
                \App\Enums\UserRole::ADMIN->value => 'text-blue-700 bg-blue-50 border-blue-100',
                \App\Enums\UserRole::OPERATOR->value => 'text-secondary-700 bg-secondary-50 border-secondary-100'
            ];
        ?>
        <span class="inline-flex px-2.5 py-0.5 rounded text-xs font-medium border <?php echo e($roleColors[$user->role->value] ?? 'text-secondary-700 bg-secondary-50 border-secondary-100'); ?>">
            <?php echo e($user->role->label()); ?>

        </span>
    </td>
    <td class="px-4 py-3">
        <?php if($user->status === 'active'): ?>
            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-success-100 text-success-700 tracking-wide uppercase"><?php echo e(__('ui.active')); ?></span>
        <?php else: ?>
            <span class="inline-flex px-2 py-0.5 rounded-full text-[10px] font-bold bg-secondary-100 text-secondary-600 tracking-wide uppercase"><?php echo e(__('ui.inactive')); ?></span>
        <?php endif; ?>
    </td>
    <td class="px-4 py-3">
        <div class="flex items-center justify-end gap-2">
            <?php if($trash): ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('restore', $user)): ?>
                    <form action="<?php echo e(route('users.restore', $user->id)); ?>" method="POST" class="inline-block">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('PATCH'); ?>
                        <button type="submit" class="btn btn-ghost p-2 text-success-600 hover:text-success-700 bg-success-50 hover:bg-success-100 rounded-lg transition-all" title="<?php echo e(__('ui.restore')); ?>" onclick="confirmUserRestore(event)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        </button>
                    </form>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('forceDelete', $user)): ?>
                    <form action="<?php echo e(route('users.force-delete', $user->id)); ?>" method="POST" class="inline-block">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="submit" class="btn btn-ghost p-2 text-danger-600 hover:text-danger-700 bg-danger-50 hover:bg-danger-100 rounded-lg transition-all" title="<?php echo e(__('ui.force_delete')); ?>" onclick="confirmUserForceDelete(event)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                <?php endif; ?>
            <?php else: ?>
                <a href="<?php echo e(route('users.show', $user)); ?>" class="btn btn-ghost p-2 text-secondary-600 hover:text-primary-600 hover:bg-primary-50 rounded-lg transition-all" title="<?php echo e(__('ui.detail')); ?>">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                </a>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('update', $user)): ?>
                    <a href="<?php echo e(route('users.edit', $user)); ?>" class="btn btn-ghost p-2 text-warning-600 hover:text-warning-700 hover:bg-warning-50 rounded-lg transition-all" title="<?php echo e(__('ui.edit')); ?>">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                    </a>
                <?php endif; ?>
                <?php if (app(\Illuminate\Contracts\Auth\Access\Gate::class)->check('delete', $user)): ?>
                    <form action="<?php echo e(route('users.destroy', $user)); ?>" method="POST" class="inline-block">
                        <?php echo csrf_field(); ?>
                        <?php echo method_field('DELETE'); ?>
                        <button type="button" class="btn btn-ghost p-2 text-danger-600 hover:text-danger-700 hover:bg-danger-50 rounded-lg transition-all" title="<?php echo e(__('ui.delete_soft_tooltip')); ?>" onclick="confirmDelete(event)">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                <?php endif; ?>
            <?php endif; ?>
        </div>
    </td>
</tr>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/components/user/table-row.blade.php ENDPATH**/ ?>