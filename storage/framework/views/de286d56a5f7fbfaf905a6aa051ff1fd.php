<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['status', 'type' => 'dot']));

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

foreach (array_filter((['status', 'type' => 'dot']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
    $status = strtolower($status);
    
    // Dot Colors (Background only)
    $dotColors = [
        'aktif' => 'bg-success-500',
        'active' => 'bg-success-500',
        'non-aktif' => 'bg-danger-500',
        'nonaktif' => 'bg-danger-500', // Added for consistency
        'inactive' => 'bg-danger-500',
        'rusak' => 'bg-danger-500',
        'dijual' => 'bg-info-500',
    ];
    
    // Badge/Pill Colors (Background + Text)
    $badgeColors = [
        'aktif' => 'bg-success-100 text-success-800',
        'active' => 'bg-success-100 text-success-800',
        'non-aktif' => 'bg-danger-100 text-danger-800',
        'nonaktif' => 'bg-danger-100 text-danger-800', // Added for consistency
        'inactive' => 'bg-danger-100 text-danger-800',
        'rusak' => 'bg-danger-100 text-danger-800',
        'dijual' => 'bg-info-100 text-info-800',
    ];
    
    $dotClass = $dotColors[$status] ?? 'bg-secondary-400';
    $badgeClass = $badgeColors[$status] ?? 'bg-secondary-100 text-secondary-800';
?>

<?php if($type === 'dot'): ?>
    <div <?php echo e($attributes->merge(['class' => "rounded-full flex-shrink-0 $dotClass"])); ?> 
         title="<?php echo e(ucfirst($status)); ?>"
         aria-label="Status: <?php echo e(ucfirst($status)); ?>"></div>
<?php else: ?>
    <span <?php echo e($attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $badgeClass"])); ?>>
        <?php echo e(ucfirst($status)); ?>

    </span>
<?php endif; ?>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/components/status-badge.blade.php ENDPATH**/ ?>