@props(['approval'])

<div class="card p-4 flex flex-col gap-3 h-full">
    <div class="flex items-start justify-between">
        <div class="flex items-center gap-3">
            @if($approval->status === 'pending')
                <input type="checkbox" name="ids[]" value="{{ $approval->id }}" class="row-checkbox rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500">
            @else
                <div class="flex-shrink-0">
                    @if($approval->status === 'approved')
                        <svg class="w-5 h-5 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    @else
                        <svg class="w-5 h-5 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    @endif
                </div>
            @endif
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
            <span class="text-secondary-700 bg-secondary-50 p-2 rounded block w-full text-xs border border-secondary-100 font-medium whitespace-pre-wrap break-words text-left">{{ $approval->reason }}</span>
            @if($approval->status === 'rejected')
                <div class="mt-3 flex flex-col bg-danger-50/60 p-2.5 rounded-lg border border-danger-100/80 shadow-sm">
                    <span class="text-[10px] font-bold text-danger-600 uppercase mb-1.5 tracking-wider">{{ __('ui.rejection_reason') }}:</span>
                    <div class="max-h-24 overflow-y-auto custom-scrollbar-slim italic text-xs" title="{{ $approval->rejection_reason }}">
                        <span class="text-danger-700 break-words whitespace-normal leading-relaxed font-medium">{{ $approval->rejection_reason ?: '-' }}</span>
                    </div>
                </div>
            @endif
        </div>
    </div>

    @if($approval->status === 'pending')
        <div class="grid grid-cols-2 gap-3 pt-1 mt-auto">
            <form action="{{ route('inventory.stock-approvals.update', $approval) }}" method="POST" class="w-full reject-form">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <input type="hidden" name="rejection_reason" class="rejection-reason-input">
                <button type="submit" class="btn btn-outline-danger w-full text-xs justify-center" onclick="confirmReject(event)">
                    {{ __('ui.btn_reject') }}
                </button>
            </form>
            <form action="{{ route('inventory.stock-approvals.update', $approval) }}" method="POST" class="w-full">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="btn btn-success w-full text-xs justify-center flex items-center py-2" onclick="confirmApprove(event)">
                    {{ __('ui.btn_approve') }}
                </button>
            </form>
        </div>
    @else
        <div class="flex items-center justify-between text-xs pt-1">
            <span class="text-secondary-400 uppercase tracking-widest font-bold">{{ __('ui.processed_by') }}:</span>
            <span class="font-bold text-secondary-900 bg-secondary-100 px-2 py-0.5 rounded-full">{{ $approval->approver->name ?? 'Sistem' }}</span>
        </div>
    @endif
</div>
