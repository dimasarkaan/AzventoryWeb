@props(['approval'])

<tr class="group hover:bg-secondary-50/60 transition-colors border-b border-secondary-50 last:border-b-0">
    <td class="px-3 py-3 w-8">
        @if($approval->status === 'pending')
            <input type="checkbox" name="ids[]" value="{{ $approval->id }}" class="row-checkbox rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500">
        @else
            <div class="flex justify-center">
                @if($approval->status === 'approved')
                    <svg class="w-4 h-4 text-success-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                @else
                    <svg class="w-4 h-4 text-danger-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                @endif
            </div>
        @endif
    </td>
    <td class="px-2 py-3 max-w-[200px]">
        <div class="flex items-center gap-2">
            <div class="h-8 w-8 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 relative overflow-hidden group-hover:border-primary-200 transition-colors border border-transparent">
                @if($approval->sparepart->image)
                    <img src="{{ asset('storage/' . $approval->sparepart->image) }}" alt="" class="h-full w-full object-cover rounded-lg">
                @else
                     <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                @endif
            </div>
            <div class="min-w-0">
                <div class="font-medium text-secondary-900 group-hover:text-primary-600 transition-colors truncate text-sm" title="{{ $approval->sparepart->name }}">{{ $approval->sparepart->name }}</div>
                <div class="text-[10px] text-secondary-500 font-mono truncate">{{ $approval->sparepart->part_number ?? '-' }}</div>
            </div>
        </div>
    </td>
    <td class="px-2 py-3 max-w-[100px]">
        <div class="font-medium text-secondary-900 truncate text-sm" title="{{ $approval->user->name }}">{{ $approval->user->name }}</div>
    </td>
    <td class="px-2 py-3">
        @if($approval->type === 'masuk')
            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-success-100 text-success-700">{{ __('ui.type_in') }}</span>
        @else
            <span class="inline-flex px-1.5 py-0.5 rounded text-[10px] font-bold bg-warning-100 text-warning-700">{{ __('ui.type_out') }}</span>
        @endif
    </td>
    <td class="px-2 py-3 whitespace-nowrap">
        <div class="font-bold text-secondary-900 text-sm">{{ $approval->quantity }} <span class="text-[10px] font-normal text-secondary-500 uppercase">{{ $approval->sparepart->unit ?? __('ui.unit_pcs') }}</span></div>
    </td>
    <td class="px-2 py-3 text-sm text-secondary-600 max-w-[220px]" title="{{ $approval->reason }}">
        <div class="line-clamp-2 font-medium text-xs break-words whitespace-normal leading-relaxed">{{ $approval->reason }}</div>
        @if($approval->status === 'rejected')
            <div class="mt-1.5 flex flex-col bg-danger-50/60 p-1.5 rounded border border-danger-100/80 shadow-sm relative overflow-visible h-fit">
                <span class="text-[9px] font-bold text-danger-600 uppercase mb-0.5 tracking-tighter">{{ __('ui.rejection_reason') }}:</span>
                <div class="max-h-16 overflow-y-auto custom-scrollbar-slim italic text-[9px] leading-relaxed" title="{{ $approval->rejection_reason }}">
                    <span class="text-danger-700 break-words whitespace-normal">{{ $approval->rejection_reason }}</span>
                </div>
            </div>
        @endif
    </td>
    <td class="px-2 py-3 text-[10px] text-secondary-500 leading-[1.1] w-20">
        <div class="whitespace-nowrap">{{ $approval->created_at->format('d M y') }}</div>
        <div class="font-medium text-secondary-400">{{ $approval->created_at->format('H:i') }}</div>
    </td>
    <td class="px-2 py-3 text-right w-24">
        @if($approval->status === 'pending')
            <div class="flex items-center justify-end gap-1.5">
                <form action="{{ route('inventory.stock-approvals.update', $approval) }}" method="POST">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="approved">
                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-success-50 text-success-600 hover:bg-success-600 hover:text-white transition-all shadow-sm ring-1 ring-success-200" onclick="confirmApprove(event)" title="{{ __('ui.btn_approve') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </button>
                </form>
                <form action="{{ route('inventory.stock-approvals.update', $approval) }}" method="POST" class="reject-form">
                    @csrf
                    @method('PATCH')
                    <input type="hidden" name="status" value="rejected">
                    <input type="hidden" name="rejection_reason" class="rejection-reason-input">
                    <button type="submit" class="w-8 h-8 flex items-center justify-center rounded-lg bg-danger-50 text-danger-600 hover:bg-danger-600 hover:text-white transition-all shadow-sm ring-1 ring-danger-200" onclick="confirmReject(event)" title="{{ __('ui.btn_reject') }}">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </form>
            </div>
        @else
            <div class="flex flex-col items-end gap-0">
                <div class="flex items-center gap-1.5 text-secondary-400 group-hover:text-secondary-500 transition-colors">
                    <span class="text-[9px] font-bold uppercase tracking-tighter">{{ __('ui.processed_by') }}</span>
                    <svg class="w-2.5 h-2.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                </div>
                <div class="text-[11px] font-bold text-secondary-700 truncate max-w-[100px]" title="{{ $approval->approver->name ?? 'Sistem' }}">
                    {{ $approval->approver->name ?? 'Sistem' }}
                </div>
            </div>
        @endif
    </td>
</tr>
