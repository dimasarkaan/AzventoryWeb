@props(['variant' => 'primary', 'type' => 'submit', 'href' => null])

@php
$baseClasses = 'inline-flex items-center border rounded-md font-semibold transition ease-in-out duration-150 focus:outline-none focus:ring-4 focus:ring-offset-2 active:scale-95';

$variantClasses = [
    'primary' => 'bg-primary-600 text-white border-transparent hover:bg-primary-800 focus:bg-primary-700 active:bg-primary-900 focus:ring-primary-500 shadow-md hover:shadow-lg',
    'secondary' => 'bg-white text-secondary-900 border-secondary-300 hover:bg-secondary-100 hover:border-secondary-400 shadow-sm focus:ring-secondary-400',
    'danger' => 'bg-danger-600 text-white border-transparent hover:bg-danger-800 focus:bg-danger-700 active:bg-danger-900 focus:ring-danger-500 shadow-md hover:shadow-lg',
];

$classes = $baseClasses . ' ' . ($variantClasses[$variant] ?? $variantClasses['primary']);
@endphp

@if($href)
    <a href="{{ $href }}" {{ $attributes->merge(['class' => $classes]) }}>
        {{ $slot }}
    </a>
@else
    <button {{ $attributes->merge(['type' => $type, 'class' => $classes]) }}>
        {{ $slot }}
    </button>
@endif