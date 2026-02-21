@props(['approval'])

<tr class="group hover:bg-secondary-50/60 transition-colors border-b border-secondary-50 last:border-b-0">
    <td class="px-4 py-3">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0 relative overflow-hidden group-hover:border-primary-200 transition-colors border border-transparent">
                @if($approval->sparepart->image)
                    <img src="{{ asset('storage/' . $approval->sparepart->image) }}" alt="" class="h-full w-full object-cover rounded-lg">
                @else
                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                @endif
            </div>
            <div>
                <div class="font-medium text-secondary-900 group-hover:text-primary-600 transition-colors">{{ $approval->sparepart->name }}</div>
                <div class="text-xs text-secondary-500 font-mono">{{ $approval->sparepart->part_number ?? '-' }}</div>
            </div>
        </div>
    </td>
    <td class="px-4 py-3">
        <div class="font-medium text-secondary-900">{{ $approval->user->name }}</div>
    </td>
    <td class="px-4 py-3">
        @if($approval->type === 'masuk')
            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-success-100 text-success-700">{{ __('ui.type_in') }}</span>
        @else
            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-warning-100 text-warning-700">{{ __('ui.type_out') }}</span>
        @endif
    </td>
    <td class="px-4 py-3">
        <div class="font-bold text-secondary-900">{{ $approval->quantity }} <span class="text-xs font-normal text-secondary-500">{{ $approval->sparepart->unit ?? __('ui.unit_pcs') }}</span></div>
    </td>
    <td class="px-4 py-3 text-sm text-secondary-600 max-w-xs truncate" title="{{ $approval->reason }}">
        {{ $approval->reason }}
    </td>
    <td class="px-4 py-3 text-sm text-secondary-500 whitespace-nowrap">
        {{ $approval->created_at->format('d M Y H:i') }}
    </td>
    <td class="px-4 py-3 text-right">
        <div class="flex items-center justify-end gap-2">
            <form action="{{ route('inventory.stock-approvals.update', $approval) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="approved">
                <button type="submit" class="btn btn-success flex items-center gap-1.5 text-xs py-1.5 px-3 transform hover:scale-105 transition-transform" title="{{ __('ui.btn_approve') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span class="hidden sm:inline">{{ __('ui.btn_approve') }}</span>
                </button>
            </form>
            <form action="{{ route('inventory.stock-approvals.update', $approval) }}" method="POST">
                @csrf
                @method('PATCH')
                <input type="hidden" name="status" value="rejected">
                <button type="submit" class="btn btn-danger flex items-center gap-1 text-xs py-1.5 px-3 transform hover:scale-105 transition-transform" onclick="confirmReject(event)" title="{{ __('ui.btn_reject') }}">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                     <span class="hidden sm:inline">{{ __('ui.btn_reject') }}</span>
                </button>
            </form>
        </div>
    </td>
</tr>
