@php
    $activeFilters = [];
    $filterLabels = [
        'category' => __('ui.category'),
        'brand' => __('ui.brand'),
        'location' => __('ui.location'),
        'color' => __('ui.color'),
        'condition' => __('ui.condition'),
        'type' => __('ui.type')
    ];

    foreach (['category', 'brand', 'location', 'color', 'condition', 'type'] as $key) {
        if (request()->filled($key)) {
            $activeFilters[$key] = [
                'label' => $filterLabels[$key],
                'value' => request($key),
                // Return URL excluding this specific filter
                'remove_url' => route('inventory.index', array_merge(request()->except([$key, 'page'])))
            ];
        }
    }
@endphp

@if(count($activeFilters) > 0)
    <div class="flex flex-wrap items-center gap-2 mb-4">
        <span class="text-xs font-medium text-secondary-500 mr-1">{{ __('ui.active_filters') }}:</span>
        @foreach($activeFilters as $key => $filter)
            <a href="{{ $filter['remove_url'] }}" class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-medium bg-primary-50 text-primary-700 border border-primary-200 hover:bg-primary-100 hover:border-primary-300 transition-colors group">
                <span><span class="opacity-75">{{ $filter['label'] }}:</span> {{ $filter['value'] === 'sale' ? __('ui.type_sale') : ($filter['value'] === 'asset' ? __('ui.type_asset') : $filter['value']) }}</span>
                <svg class="w-3.5 h-3.5 text-primary-400 group-hover:text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </a>
        @endforeach
        <a href="{{ route('inventory.index', request()->only(['trash', 'filter'])) }}" class="text-xs font-medium text-secondary-400 hover:text-danger-500 ml-2 transition-colors">
            {{ __('ui.clear_all') }}
        </a>
    </div>
@endif