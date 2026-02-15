@props(['sparepart'])

<div class="card p-4 flex flex-col gap-3 group hover:shadow-lg transition-all duration-300 border border-secondary-100 hover:border-primary-200">
    <div class="flex items-start justify-between gap-3">
        <div class="flex items-center gap-3 min-w-0">
            <!-- Status Indicator (Dot) -->
            <x-status-badge :status="$sparepart->status" class="w-2 h-2" />
            
            <div class="h-12 w-12 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 relative overflow-hidden">
                @if($sparepart->image)
                    <img src="{{ asset('storage/' . $sparepart->image) }}" alt="" loading="lazy" decoding="async" class="h-full w-full object-cover rounded-lg group-hover:scale-110 transition-transform duration-500">
                @else
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                @endif
            </div>

            <!-- Vertical Line Indicator -->
            <div class="w-1 self-stretch rounded-full mr-3 shrink-0 {{ $sparepart->type === 'sale' ? 'bg-green-500 shadow-[0_0_8px_rgba(34,197,94,0.4)]' : 'bg-blue-500 shadow-[0_0_8px_rgba(59,130,246,0.4)]' }}"></div>

            <div class="min-w-0">
                <div class="flex items-center gap-1.5 mb-0.5">
                    <a href="{{ route('superadmin.inventory.show', $sparepart) }}" class="font-bold text-secondary-900 line-clamp-1 block leading-tight group-hover:text-primary-600 transition-colors">
                        {{ $sparepart->name }}
                    </a>
                </div>
                <span class="text-xs text-secondary-500 font-mono block mt-0.5">{{ $sparepart->part_number }}</span>
            </div>
        </div>
    </div>
    
    <div class="grid grid-cols-2 gap-y-2 gap-x-4 text-xs border-t border-secondary-50 py-2.5">
        <!-- Row 1 -->
        <div>
            <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">{{ __('ui.category') }}</span>
            <span class="font-medium text-secondary-700 block truncate">{{ $sparepart->category }}</span>
        </div>
        <div class="text-right">
            <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">{{ __('ui.condition') }}</span>
            @php
                $condition = $sparepart->condition ?? '-';
                $age = $sparepart->age === 'Pernah Dipakai (Bekas)' ? 'Bekas' : ($sparepart->age ?? '-');
                
                $conditionColor = match(strtolower($condition)) {
                    'baik' => 'text-success-800 bg-success-50',
                    'rusak' => 'text-danger-800 bg-danger-50',
                    'hilang' => 'text-secondary-800 bg-secondary-100',
                    default => 'text-secondary-800 bg-secondary-50'
                };
            @endphp
            <div class="flex flex-col items-end gap-0.5">
                <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-bold {{ $conditionColor }}">
                    {{ ucfirst($condition) }}
                </span>
                <span class="text-[10px] text-secondary-500">{{ $age }}</span>
            </div>
        </div>

        <!-- Row 2: Lokasi Full Width -->
        <div class="col-span-2 flex justify-between items-center bg-secondary-50/50 rounded px-2 py-1 mt-1">
            <span class="text-secondary-400 text-[10px] uppercase tracking-wider">{{ __('ui.location') }}</span>
            <div class="font-medium text-secondary-700 flex items-center gap-1">
                <svg class="w-3 h-3 text-secondary-400 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                <span class="truncate">{{ $sparepart->location }}</span>
            </div>
        </div>

        <!-- Row 3 -->
        <div class="mt-1">
            <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">{{ __('ui.color') }}</span>
            <span class="font-medium text-secondary-700 block truncate">{{ $sparepart->color ?? '-' }}</span>
        </div>
        <div class="text-right mt-1">
            <span class="text-secondary-400 block mb-0.5 text-[10px] uppercase tracking-wider">{{ __('ui.stock') }}</span>
            <div class="flex items-center justify-end gap-1.5">
                @php
                    $isLowStockMobile = $sparepart->stock <= $sparepart->minimum_stock && !in_array(strtolower($sparepart->condition), ['rusak', 'hilang']);
                @endphp
                
                @if($isLowStockMobile)
                     <svg class="w-3 h-3 text-danger-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                @endif
                <span class="font-bold {{ $isLowStockMobile ? 'text-danger-600' : 'text-secondary-900' }}">{{ $sparepart->stock }}</span>
                <span class="text-[10px] text-secondary-500">{{ $sparepart->unit ?? 'Pcs' }}</span>
            </div>
        </div>
    </div>

    <div class="flex items-center justify-end gap-2 pt-1 border-t border-secondary-100">
         <a href="{{ route('superadmin.inventory.show', $sparepart) }}" class="btn btn-ghost text-xs p-2 h-8 text-secondary-600 font-medium hover:bg-secondary-50 rounded-lg transition-colors">{{ __('ui.detail') }}</a>
         <a href="{{ route('superadmin.inventory.edit', $sparepart) }}" class="btn btn-white text-xs p-2 h-8 border border-secondary-200 text-secondary-600 font-medium hover:bg-secondary-50 rounded-lg transition-all">{{ __('ui.edit') }}</a>
         <form action="{{ route('superadmin.inventory.destroy', $sparepart) }}" method="POST" class="inline-block">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-danger text-xs p-2 h-8 bg-danger-50 text-danger-600 hover:bg-danger-600 hover:text-white border-transparent transition-all" onclick="confirmDelete(event)">{{ __('ui.delete') }}</button>
        </form>
    </div>
</div>
