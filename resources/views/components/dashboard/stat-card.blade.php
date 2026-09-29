@props([
    'title',
    'value',
    'icon',
    'badgeText',
    'color' => 'primary',
    'onClick' => null,
    'loading' => false
])

@php
    $colors = [
        'primary' => [
            'bg' => 'bg-primary-100',
            'hover_bg' => 'group-hover:bg-primary-200',
            'text' => 'text-primary-600',
            'badge_bg' => 'bg-primary-50',
            'badge_text' => 'text-primary-700',
            'border' => 'hover:border-primary-100',
        ],
        'success' => [
            'bg' => 'bg-success-100',
            'hover_bg' => 'group-hover:bg-success-200',
            'text' => 'text-success-600',
            'badge_bg' => 'bg-success-50',
            'badge_text' => 'text-success-700',
            'border' => 'hover:border-success-100',
        ],
        'warning' => [
            'bg' => 'bg-warning-100',
            'hover_bg' => 'group-hover:bg-warning-200',
            'text' => 'text-warning-600',
            'badge_bg' => 'bg-warning-50',
            'badge_text' => 'text-warning-700',
            'border' => 'hover:border-warning-100',
        ],
        'info' => [
            'bg' => 'bg-info-100',
            'hover_bg' => 'group-hover:bg-info-200',
            'text' => 'text-info-600',
            'badge_bg' => 'bg-info-50',
            'badge_text' => 'text-info-700',
            'border' => 'hover:border-info-100',
        ],
        'secondary' => [
            'bg' => 'bg-secondary-100',
            'hover_bg' => 'group-hover:bg-secondary-200',
            'text' => 'text-secondary-600',
            'badge_bg' => 'bg-secondary-50',
            'badge_text' => 'text-secondary-700',
            'border' => 'hover:border-secondary-100',
        ],
        'danger' => [
            'bg' => 'bg-danger-100',
            'hover_bg' => 'group-hover:bg-danger-200',
            'text' => 'text-danger-600',
            'badge_bg' => 'bg-danger-50',
            'badge_text' => 'text-danger-700',
            'border' => 'hover:border-danger-100',
        ],
        'purple' => [
            'bg' => 'bg-purple-100',
            'hover_bg' => 'group-hover:bg-purple-200',
            'text' => 'text-purple-600',
            'badge_bg' => 'bg-purple-50',
            'badge_text' => 'text-purple-700',
            'border' => 'hover:border-purple-100',
        ],
        'fuchsia' => [
            'bg' => 'bg-fuchsia-100',
            'hover_bg' => 'group-hover:bg-fuchsia-200',
            'text' => 'text-fuchsia-600',
            'badge_bg' => 'bg-fuchsia-50',
            'badge_text' => 'text-fuchsia-700',
            'border' => 'hover:border-fuchsia-100',
        ],
        'pink' => [
            'bg' => 'bg-pink-100',
            'hover_bg' => 'group-hover:bg-pink-200',
            'text' => 'text-pink-600',
            'badge_bg' => 'bg-pink-50',
            'badge_text' => 'text-pink-700',
            'border' => 'hover:border-pink-100',
        ],
    ];

    $c = $colors[$color] ?? $colors['primary'];
@endphp

<div {!! $onClick && !$loading ? '@click="' . $onClick . '" role="button" tabindex="0" @keydown.enter="' . $onClick . '"' : '' !!}
     class="card p-6 flex flex-col justify-between relative overflow-hidden group transition-all duration-300 shadow-md {{ $onClick && !$loading ? 'cursor-pointer hover:scale-[1.02] hover:shadow-lg border-2 border-transparent ' . $c['border'] : 'border-2 border-transparent' }}">
    
    <div class="absolute right-0 top-0 h-24 w-24 {{ $c['bg'] }} rounded-bl-full -mr-4 -mt-4 transition-colors {{ $onClick && !$loading ? $c['hover_bg'] : '' }}"></div>
    
    <!-- Option A: Action Indicator Hover -->
    @if($onClick && !$loading)
        <div class="absolute top-3 right-3 z-20 {{ $c['text'] }} opacity-0 transform translate-x-2 translate-y-2 group-hover:opacity-100 group-hover:translate-x-0 group-hover:translate-y-0 transition-all duration-300">
            <svg class="w-5 h-5 drop-shadow-sm" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
        </div>
    @endif

    @if($loading)
        <!-- Option B: Skeleton Loading State -->
        <div class="animate-pulse flex flex-col h-full justify-between z-10 relative">
            <div>
                <div class="h-4 bg-secondary-200 rounded w-1/2 mb-4"></div>
                <div class="h-8 bg-secondary-200 rounded w-1/3"></div>
            </div>
            <div class="mt-4 flex items-center">
                <div class="h-10 w-10 bg-secondary-200 rounded-lg"></div>
                <div class="ml-2 h-4 bg-secondary-200 rounded w-16"></div>
            </div>
        </div>
    @else
        <!-- Normal State -->
        <div class="z-10 relative flex flex-col h-full justify-between">
            <div>
                <p class="text-sm font-medium text-secondary-500 leading-tight">{!! $title !!}</p>
                <h3 class="text-3xl font-bold text-secondary-900 mt-2">{{ $value }}</h3>
            </div>
            <div class="mt-4 flex items-center {{ $c['text'] }}">
                <div class="p-2 {{ $c['bg'] }} rounded-lg {{ $onClick ? 'group-hover:bg-white group-hover:shadow-sm' : '' }} transition-all">
                    {{ $icon }}
                </div>
                @if($badgeText)
                    <span class="ml-2 text-xs font-semibold {{ $c['badge_bg'] }} {{ $c['badge_text'] }} px-2 py-0.5 rounded-full">{{ $badgeText }}</span>
                @endif
            </div>
            {{ $slot }}
        </div>
    @endif
</div>
