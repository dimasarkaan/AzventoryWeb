<?php $attributes ??= new \Illuminate\View\ComponentAttributeBag;

$__newAttributes = [];
$__propNames = \Illuminate\View\ComponentAttributeBag::extractPropNames((['variant' => 'default']));

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

foreach (array_filter((['variant' => 'default']), 'is_string', ARRAY_FILTER_USE_KEY) as $__key => $__value) {
    $$__key = $$__key ?? $__value;
}

$__defined_vars = get_defined_vars();

foreach ($attributes->all() as $__key => $__value) {
    if (array_key_exists($__key, $__defined_vars)) unset($$__key);
}

unset($__defined_vars, $__key, $__value); ?>

<svg viewBox="0 0 808.41 897.25" <?php echo e($attributes); ?>>
  <?php if($variant === 'white'): ?>
    <path fill="white" d="M223.57,683.58h584.6v213.67H0v-106.77c0-211.45,82.4-410.15,231.98-559.48,5.81-5.81,11.75-11.51,17.69-17.07H.49V.25h776.25c1.24-.12,2.6-.12,3.83-.12,3.71,0,7.43,0,11.26-.12l16.58.25-.37,213.67h-31.67c-148.59,3.46-287.78,62.98-393.44,168.39-83.52,83.39-138.32,187.69-159.36,301.27Z"/>
    <path fill="white" fill-opacity="0.9" d="M776.74.25c-79.31,2.97-296.32,29.07-527.07,213.67H.49V.25h776.25Z"/>
    <rect fill="white" x="431.94" y="212.09" width="539.04" height="213.92" transform="translate(1020.5 -382.41) rotate(90)"/>
  <?php else: ?>
    <path fill="#3b82f6" d="M223.57,683.58h584.6v213.67H0v-106.77c0-211.45,82.4-410.15,231.98-559.48,5.81-5.81,11.75-11.51,17.69-17.07H.49V.25h776.25c1.24-.12,2.6-.12,3.83-.12,3.71,0,7.43,0,11.26-.12l16.58.25-.37,213.67h-31.67c-148.59,3.46-287.78,62.98-393.44,168.39-83.52,83.39-138.32,187.69-159.36,301.27Z"/>
    <path fill="#55b2ff" d="M776.74.25c-79.31,2.97-296.32,29.07-527.07,213.67H.49V.25h776.25Z"/>
    <rect fill="#3b82f6" x="431.94" y="212.09" width="539.04" height="213.92" transform="translate(1020.5 -382.41) rotate(90)"/>
  <?php endif; ?>
</svg>
<?php /**PATH E:\KULI AH\Semester\Semester 8\Tugas Akhir\Web 2\AzventoryWeb\resources\views/components/icon/logo.blade.php ENDPATH**/ ?>