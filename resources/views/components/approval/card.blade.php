@props(['approval'])

<div class="card p-4 flex flex-col gap-3 h-full">
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 relative overflow-hidden group">
               @if($approval->sparepart->image)
                    <img src="{{ asset('storage/' . $approval->sparepart->image) }}" alt="" class="h-full w-full object-cover rounded-lg">
                @else
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                @endif
            </div>
            <div>
                <div class="font-bold text-secondary-900 line-clamp-1">{{ $approval->sparepart->name }}</div>
                <div class="text-xs text-secondary-500">{{ __('ui.by_user', ['name' => $approval->user->name]) }}</div>
            </div>
        </div>
       
         @if($approval->type === 'masuk')
            <span class="badge badge-success text-[10px]">{{ __('ui.type_in') }}</span>
        @else
            <span class="badge badge-warning text-[10px]">{{ __('ui.type_out') }}</span>
        @endif
    </div>
    
    <div class="grid grid-cols-2 gap-2 text-sm border-t border-b border-secondary-50 py-3">
        <div>
            <span class="text-xs text-secondary-400 block">{{ __('ui.amount_column') }}</span>
            <span class="font-bold text-secondary-900">{{ $approval->quantity }} <span class="text-xs font-normal text-secondary-500">{{ $approval->sparepart->unit ?? __('ui.unit_pcs') }}</span></span>
        </div>
        <div class="text-right">
            <span class="text-xs text-secondary-400 block">{{ __('ui.date_column') }}</span>
            <span class="text-secondary-700">{{ $approval->created_at->format('d/m/y H:i') }}</span>
        </div>
        <div class="col-span-2 mt-1">
            <span class="text-xs text-secondary-400 block">{{ __('ui.reason_label') }}</span>
            <span class="text-secondary-700 bg-secondary-50 p-2 rounded block w-full text-xs border border-secondary-100">{{ $approval->reason }}</span>
        </div>
    </div>

    <div class="grid grid-cols-2 gap-3 pt-1 mt-auto">
        <form action="{{ route('inventory.stock-approvals.update', $approval) }}" method="POST" class="w-full">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="rejected">
            <button type="submit" class="btn btn-outline-danger w-full text-xs justify-center" onclick="confirmReject(event)">
                {{ __('ui.btn_reject') }}
            </button>
        </form>
        <form action="{{ route('inventory.stock-approvals.update', $approval) }}" method="POST" class="w-full">
            @csrf
            @method('PATCH')
            <input type="hidden" name="status" value="approved">
            <button type="submit" class="btn btn-success w-full text-xs justify-center flex items-center py-2">
                {{ __('ui.btn_approve') }}
            </button>
        </form>
    </div>
</div>
