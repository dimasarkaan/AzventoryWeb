<div class="block md:hidden space-y-4">
    @forelse ($spareparts as $sparepart)
        <div class="card p-4">
            <!-- Header: Image, Name, Status -->
            <div class="flex items-start gap-4 mb-4">
                <!-- Image -->
                <div class="h-16 w-16 rounded-lg bg-secondary-100 flex items-center justify-center flex-shrink-0 text-secondary-400 overflow-hidden border border-secondary-200">
                    @if($sparepart->image)
                        <img src="{{ asset('storage/' . $sparepart->image) }}" alt="{{ $sparepart->name }}" loading="lazy" class="h-full w-full object-cover">
                    @else
                        <x-icon.image class="w-8 h-8" />
                    @endif
                </div>
                
                <!-- Title & Badge -->
                <div class="flex-1 min-w-0">
                    <div class="flex items-start justify-between gap-2">
                        <div>
                            <div class="flex items-center gap-2">
                                    <h3 class="text-base font-bold text-secondary-900 line-clamp-1">
                                    <a href="{{ route('inventory.show', $sparepart) }}">
                                        {{ $sparepart->name }}
                                    </a>
                                </h3>
                                
                            </div>
                            <p class="text-xs text-secondary-500 font-mono mt-0.5">{{ $sparepart->part_number }}</p>
                        </div>
                        <x-status-badge :status="$sparepart->status" class="flex-shrink-0" />
                    </div>
                </div>
            </div>

            <!-- Info Grid -->
            <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm mb-4 border-t border-b border-secondary-100 py-3">
                <!-- Brand & Category -->
                <div class="col-span-2 flex items-center justify-between">
                    <span class="text-secondary-500">{{ __('ui.brand') }} / {{ __('ui.category') }}</span>
                    <span class="font-medium text-secondary-900 text-right truncate pl-2">
                        {{ $sparepart->brand ?? '-' }} <span class="text-secondary-300 mx-1">|</span> {{ $sparepart->category }}
                    </span>
                </div>

                <!-- Condition -->
                <div class="flex flex-col">
                    <span class="text-xs text-secondary-500 mb-1">{{ __('ui.condition') }}</span>
                    @php
                        $condition = $sparepart->condition ?? '-';
                        $conditionColor = match(strtolower($condition)) {
                            'baik' => 'text-success-700 bg-success-50 border-success-200',
                            'rusak' => 'text-danger-700 bg-danger-50 border-danger-200',
                            'hilang' => 'text-secondary-700 bg-secondary-100 border-secondary-200',
                            default => 'text-secondary-700 bg-secondary-50 border-secondary-200'
                        };
                    @endphp
                    <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium border w-fit {{ $conditionColor }}">
                        {{ ucfirst($condition) }}
                    </span>
                </div>

                <!-- Stock & Location -->
                <div class="flex flex-col items-end text-right">
                    <span class="text-xs text-secondary-500 mb-1">{{ __('ui.stock') }} @ {{ $sparepart->location }}</span>
                    <div class="flex items-center gap-1.5">
                        @php
                            $isLowStock = $sparepart->stock <= $sparepart->minimum_stock && !in_array(strtolower($sparepart->condition), ['rusak', 'hilang']);
                        @endphp
                        <span class="text-lg font-bold {{ $isLowStock ? 'text-danger-600' : 'text-secondary-900' }}">
                            {{ $sparepart->stock }}
                        </span>
                        <span class="text-xs text-secondary-500">{{ $sparepart->unit ?? 'Pcs' }}</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex items-center justify-end gap-2">
                @if(request('trash'))
                    @can('restore', $sparepart)
                    <form action="{{ route('inventory.restore', $sparepart->id) }}" method="POST" class="inline-block w-full sm:w-auto">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-sm btn-success w-full justify-center flex items-center gap-1" onclick="confirmInventoryRestore(event)">
                            <x-icon.restore class="w-4 h-4" />
                            {{ __('ui.restore') }}
                        </button>
                    </form>
                    @endcan
                @else
                    <a href="{{ route('inventory.show', $sparepart) }}" class="btn btn-sm btn-secondary flex-1 justify-center">
                        {{ __('ui.detail') }}
                    </a>
                    @can('update', $sparepart)
                    <a href="{{ route('inventory.edit', $sparepart) }}" class="btn btn-sm btn-secondary flex-1 justify-center border-secondary-300 shadow-sm">
                        {{ __('ui.edit') }}
                    </a>
                    @endcan
                    @can('delete', $sparepart)
                    <form action="{{ route('inventory.destroy', $sparepart) }}" method="POST" class="inline-block flex-1">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn btn-sm btn-danger w-full justify-center" onclick="confirmDelete(event)">
                            {{ __('ui.delete') }}
                        </button>
                    </form>
                    @endcan
                @endif
            </div>
        </div>
    @empty
        <!-- Mobile Empty State -->
        <div class="card p-8 flex flex-col items-center justify-center text-center">
            @php
                $isFiltered = request('search') || request('category') || request('brand') || request('location') || request('color');
            @endphp

            <div class="h-16 w-16 bg-secondary-100 text-secondary-400 rounded-full flex items-center justify-center mb-4 shadow-sm border border-secondary-200">
                    @if(request('trash'))
                    {{-- Trash Icon --}}
                    <x-icon.trash class="w-8 h-8 text-danger-400" />
                @elseif($isFiltered)
                    {{-- Search/Filter Icon --}}
                    <x-icon.search class="w-8 h-8 text-secondary-400" />
                @else
                    {{-- Default Box Icon --}}
                    <x-icon.box class="w-8 h-8 text-secondary-400" />
                @endif
            </div>
            
            <h3 class="text-lg font-medium text-secondary-900">
                @if(request('trash'))
                    {{ __('ui.trash_empty') }}
                @elseif($isFiltered)
                    {{ __('ui.no_results') }}
                @else
                    {{ __('ui.inventory_empty') }}
                @endif
            </h3>
            
            <p class="text-secondary-500 text-sm mt-1 max-w-xs mx-auto">
                @if(request('trash'))
                    {{ __('ui.trash_empty_desc') }}
                @elseif($isFiltered)
                    {{ __('ui.no_results_desc') }}
                @else
                    {{ __('ui.inventory_empty_desc') }}
                @endif
            </p>
        </div>
    @endforelse
    
    <!-- Mobile Pagination -->
    <div class="mt-4">
        {{ $spareparts->links() }}
    </div>
</div>
