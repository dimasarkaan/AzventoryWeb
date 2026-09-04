@props(['value', 'color', 'title', 'description'])

@php
    $colors = [
        'primary' => [
            'border' => 'hover:border-primary-400 peer-checked:border-primary-600 peer-checked:bg-primary-50',
            'icon_bg' => 'bg-primary-100 text-primary-600 group-hover:bg-primary-200',
            'peer_border' => 'border-primary-600'
        ],
        'warning' => [
            'border' => 'hover:border-warning-400 peer-checked:border-warning-600 peer-checked:bg-warning-50',
            'icon_bg' => 'bg-warning-100 text-warning-600 group-hover:bg-warning-200',
            'peer_border' => 'border-warning-600'
        ],
        'sky' => [
            'border' => 'hover:border-sky-400 peer-checked:border-sky-600 peer-checked:bg-sky-50',
            'icon_bg' => 'bg-sky-100 text-sky-600 group-hover:bg-sky-200',
            'peer_border' => 'border-sky-600'
        ],
        'danger' => [
            'border' => 'hover:border-danger-400 peer-checked:border-danger-600 peer-checked:bg-danger-50',
            'icon_bg' => 'bg-danger-100 text-danger-600 group-hover:bg-danger-200',
            'peer_border' => 'border-danger-600'
        ],
    ];
    $c = $colors[$color] ?? $colors['primary'];
@endphp

<label class="cursor-pointer relative group">
    <input type="radio" name="report_type" value="{{ $value }}" x-model="reportType" class="peer sr-only">
    <div class="p-5 rounded-xl border-2 border-secondary-100 {{ $c['border'] }} transition-all h-full flex flex-col items-center text-center">
        <div class="w-12 h-12 rounded-full {{ $c['icon_bg'] }} flex items-center justify-center mb-3 transition-colors">
            {{ $icon }}
        </div>
        <span class="font-bold text-secondary-900 block mb-1">{{ $title }}</span>
        <span class="text-xs text-secondary-500 leading-tight">{{ $description }}</span>
    </div>
    <div class="absolute inset-0 border-2 {{ $c['peer_border'] }} rounded-xl opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
</label>
