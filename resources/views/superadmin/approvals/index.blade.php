<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-4 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.approvals_title') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">{{ __('ui.approvals_desc') }}</p>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="block md:hidden space-y-4">
                @forelse ($pendingApprovals as $approval)
                    <div class="card p-4">
                        <!-- Header: Image, Name, Part Number -->
                        <div class="flex items-start gap-4 mb-4">
                            <!-- Image -->
                            <div class="h-16 w-16 rounded-lg bg-secondary-100 flex items-center justify-center flex-shrink-0 text-secondary-400 overflow-hidden border border-secondary-200">
                                @if($approval->sparepart->image)
                                    <img src="{{ asset('storage/' . $approval->sparepart->image) }}" alt="{{ $approval->sparepart->name }}" loading="lazy" class="h-full w-full object-cover">
                                @else
                                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                @endif
                            </div>
                            
                            <!-- Title & Info -->
                            <div class="flex-1 min-w-0">
                                <h3 class="text-base font-bold text-secondary-900 line-clamp-1">
                                    {{ $approval->sparepart->name }}
                                </h3>
                                <div class="text-xs text-secondary-500 font-mono mt-0.5 mb-2">{{ $approval->sparepart->part_number ?? '-' }}</div>
                                
                                <div class="flex items-center gap-2">
                                     @if($approval->type === 'masuk')
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-success-100 text-success-700">{{ __('ui.type_in') }}</span>
                                    @else
                                        <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-warning-100 text-warning-700">{{ __('ui.type_out') }}</span>
                                    @endif
                                    <span class="text-xs text-secondary-400">•</span>
                                    <span class="text-xs text-secondary-500">{{ $approval->created_at->format('d M Y') }}</span>
                                </div>
                            </div>
                        </div>

                        <!-- Info Grid -->
                        <div class="grid grid-cols-2 gap-y-3 gap-x-4 text-sm mb-4 border-t border-b border-secondary-100 py-3">
                            <!-- Applicant -->
                            <div class="flex flex-col">
                                <span class="text-xs text-secondary-500 mb-1">{{ __('ui.applicant_column') }}</span>
                                <span class="font-medium text-secondary-900 truncate">{{ $approval->user->name }}</span>
                            </div>

                            <!-- Amount -->
                             <div class="flex flex-col items-end text-right">
                                <span class="text-xs text-secondary-500 mb-1">{{ __('ui.amount_column') }}</span>
                                <span class="font-bold text-secondary-900">
                                    {{ $approval->quantity }} <span class="text-xs font-normal text-secondary-500">{{ $approval->sparepart->unit ?? __('ui.unit_pcs') }}</span>
                                </span>
                            </div>

                            <!-- Reason (Full Width) -->
                             <div class="col-span-2 flex flex-col pt-1">
                                <span class="text-xs text-secondary-500 mb-1">{{ __('ui.reason_column') }}</span>
                                <p class="text-secondary-700 text-sm leading-relaxed line-clamp-2">{{ $approval->reason }}</p>
                            </div>
                        </div>

                        <!-- Actions -->
                        <div class="flex items-center gap-2">
                            <form action="{{ route('stock-approvals.update', $approval) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-success w-full justify-center flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                    {{ __('ui.btn_approve') }}
                                </button>
                            </form>
                            <form action="{{ route('stock-approvals.update', $approval) }}" method="POST" class="flex-1">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-danger w-full justify-center flex items-center gap-2" onclick="confirmReject(event)">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    {{ __('ui.btn_reject') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <!-- Mobile Empty State -->
                    <div class="card p-8 flex flex-col items-center justify-center text-center">
                        <div class="h-16 w-16 bg-success-50 text-success-500 rounded-full flex items-center justify-center mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <p class="text-secondary-500">{{ __('ui.no_pending_approvals') }}</p>
                    </div>
                @endforelse
            </div>

            <!-- Desktop Table View -->
            <div class="hidden md:block card overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="table-modern w-full">
                        <thead>
                            <tr>
                                <th>{{ __('ui.item_column') }}</th>
                                <th>{{ __('ui.applicant_column') }}</th>
                                <th>{{ __('ui.type_column') }}</th>
                                <th>{{ __('ui.amount_column') }}</th>
                                <th>{{ __('ui.reason_column') }}</th>
                                <th>{{ __('ui.date_column') }}</th>
                                <th class="text-right">{{ __('ui.action_column') }}</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($pendingApprovals as $approval)
                                <tr class="group hover:bg-secondary-50 transition-colors">
                                    <td>
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                                @if($approval->sparepart->image)
                                                    <img src="{{ asset('storage/' . $approval->sparepart->image) }}" alt="" class="h-full w-full object-cover rounded-lg">
                                                @else
                                                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                                @endif
                                            </div>
                                            <div>
                                                <div class="font-medium text-secondary-900">{{ $approval->sparepart->name }}</div>
                                                <div class="text-xs text-secondary-500 font-mono">{{ $approval->sparepart->part_number ?? '-' }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="font-medium text-secondary-900">{{ $approval->user->name }}</div>
                                    </td>
                                    <td>
                                        @if($approval->type === 'masuk')
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-success-100 text-success-700">{{ __('ui.type_in') }}</span>
                                        @else
                                            <span class="inline-flex px-2 py-0.5 rounded text-xs font-medium bg-warning-100 text-warning-700">{{ __('ui.type_out') }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="font-bold text-secondary-900">{{ $approval->quantity }} <span class="text-xs font-normal text-secondary-500">{{ $approval->sparepart->unit ?? __('ui.unit_pcs') }}</span></div>
                                    </td>
                                    <td class="text-sm text-secondary-600 max-w-xs truncate" title="{{ $approval->reason }}">
                                        {{ $approval->reason }}
                                    </td>
                                    <td class="text-sm text-secondary-500 whitespace-nowrap">
                                        {{ $approval->created_at->format('d M Y H:i') }}
                                    </td>
                                    <td class="text-right">
                                        <div class="flex items-center justify-end gap-2">
                                            <form action="{{ route('stock-approvals.update', $approval) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="approved">
                                                <button type="submit" class="btn btn-success flex items-center gap-1 text-xs py-1.5 px-3">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                                    {{ __('ui.btn_approve') }}
                                                </button>
                                            </form>
                                            <form action="{{ route('stock-approvals.update', $approval) }}" method="POST">
                                                @csrf
                                                @method('PATCH')
                                                <input type="hidden" name="status" value="rejected">
                                                <button type="submit" class="btn btn-danger flex items-center gap-1 text-xs py-1.5 px-3" onclick="confirmReject(event)">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                    {{ __('ui.btn_reject') }}
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="p-8 text-center text-secondary-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-success-100 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p>{{ __('ui.no_pending_approvals') }}</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Mobile Card View -->
            <div class="md:hidden space-y-4">
                @forelse ($pendingApprovals as $approval)
                    <div class="card p-4 flex flex-col gap-3">
                        <div class="flex items-start justify-between">
                            <div class="flex items-center gap-3">
                                <div class="h-10 w-10 rounded-lg bg-secondary-100 flex items-center justify-center text-secondary-500 flex-shrink-0">
                                   @if($approval->sparepart->image)
                                        <img src="{{ asset('storage/' . $approval->sparepart->image) }}" alt="" class="h-full w-full object-cover rounded-lg">
                                    @else
                                         <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    @endif
                                </div>
                                <div>
                                    <div class="font-bold text-secondary-900">{{ $approval->sparepart->name }}</div>
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

                        <div class="grid grid-cols-2 gap-3 pt-1">
                            <form action="{{ route('stock-approvals.update', $approval) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="rejected">
                                <button type="submit" class="btn btn-outline-danger w-full text-xs justify-center" onclick="confirmReject(event)">
                                    {{ __('ui.btn_reject') }}
                                </button>
                            </form>
                            <form action="{{ route('stock-approvals.update', $approval) }}" method="POST" class="w-full">
                                @csrf
                                @method('PATCH')
                                <input type="hidden" name="status" value="approved">
                                <button type="submit" class="btn btn-success w-full text-xs justify-center">
                                    {{ __('ui.btn_approve') }}
                                </button>
                            </form>
                        </div>
                    </div>
                @empty
                    <div class="card p-8 text-center text-secondary-500">
                        <p class="text-sm">{{ __('ui.no_pending') }}</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-6">
                {{ $pendingApprovals->links() }}
            </div>
        </div>
    </div>
    @push('scripts')
    <script>
        function confirmReject(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            Swal.fire({
                title: '{{ __('ui.confirm_reject_title') }}',
                text: "{{ __('ui.confirm_reject_text') }}",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('ui.btn_yes_reject') }}',
                cancelButtonText: '{{ __('ui.btn_cancel') }}',
                reverseButtons: true,
                customClass: {
                    popup: '!rounded-2xl !font-sans',
                    title: '!text-secondary-900 !text-xl !font-bold',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    confirmButton: 'btn btn-danger px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200',
                    cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                },
                buttonsStyling: false,
                iconColor: '#ef4444',
                padding: '2em',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    form.submit();
                }
            })
        }
    </script>
    @endpush
</x-app-layout>
