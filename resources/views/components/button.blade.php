@php
$baseClasses = 'inline-flex items-center px-4 py-2 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest focus:outline-none focus:ring-2 focus:ring-offset-2 transition ease-in-out duration-150';

$variantClasses = [
    'primary' => 'bg-primary-600 hover:bg-primary-700 focus:bg-primary-700 active:bg-primary-800 focus:ring-primary-500',
    'secondary' => 'bg-secondary-600 hover:bg-secondary-700 focus:bg-secondary-700 active:bg-secondary-800 focus:ring-secondary-500',
];

$classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
@endphp

<button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
    {{ $slot }}
</button>