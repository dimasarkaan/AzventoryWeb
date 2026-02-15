@props(['status', 'type' => 'dot'])

@php
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
@endphp

@if($type === 'dot')
    <div {{ $attributes->merge(['class' => "rounded-full flex-shrink-0 $dotClass"]) }} 
         title="{{ ucfirst($status) }}"
         aria-label="Status: {{ ucfirst($status) }}"></div>
@else
    <span {{ $attributes->merge(['class' => "inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium $badgeClass"]) }}>
        {{ ucfirst($status) }}
    </span>
@endif
