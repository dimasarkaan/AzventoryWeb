<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['variant' => 'primary', 'type' => 'submit', 'href' => null]));

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

foreach (array_filter((['variant' => 'primary', 'type' => 'submit', 'href' => null]), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<?php
$baseClasses = 'inline-flex items-center border rounded-md font-semibold transition ease-in-out duration-150 focus:outline-none focus:ring-4 focus:ring-offset-2 active:scale-95';

$variantClasses = [
    'primary' => 'bg-primary-600 text-white border-transparent hover:bg-primary-800 focus:bg-primary-700 active:bg-primary-900 focus:ring-primary-500 shadow-md hover:shadow-lg',
    'secondary' => 'bg-white text-secondary-900 border-secondary-300 hover:bg-secondary-100 hover:border-secondary-400 shadow-sm focus:ring-secondary-400',
    'danger' => 'bg-danger-600 text-white border-transparent hover:bg-danger-800 focus:bg-danger-700 active:bg-danger-900 focus:ring-danger-500 shadow-md hover:shadow-lg',
];

$classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
?>

<?php if($href): ?>
    <a href="<?php echo e($href); ?>" <?php echo e($attributes->merge(['class' => $classes])); ?>>
        <?php echo e($slot); ?>

    </a>
<?php else: ?>
    <button <?php echo e($attributes->merge(['type' => $type, 'class' => $classes])); ?>>
        <?php echo e($slot); ?>

    </button>
<?php endif; ?><?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/components/button.blade.php ENDPATH**/ ?>