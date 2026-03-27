<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Header -->
            <div class="mb-6 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
                <div>
                    <h2 class="text-3xl font-bold text-secondary-900 tracking-tight">
                        {{ __('ui.approvals_title') }}
                    </h2>
                    <p class="mt-1 text-sm text-secondary-500">{{ __('ui.approvals_desc') }}</p>
                </div>
            </div>

            <!-- Modern Search & Filter Bar -->
            <div class="mb-6 card p-4 overflow-visible">
                <form method="GET" action="{{ route('inventory.stock-approvals.index') }}" id="approval-filter-form">
                    <div class="flex flex-col lg:flex-row gap-4 items-center">
                        <div class="relative flex-1 w-full">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                <x-icon.search class="w-5 h-5 text-secondary-400" />
                            </div>
                            <input
                                type="text"
                                name="search"
                                value="{{ request('search') }}"
                                placeholder="{{ __('ui.approvals_search_placeholder') }}"
                                class="input-field pl-10 w-full"
                                onchange="this.form.submit()"
                            >
                        </div>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-3 w-full lg:w-auto">
                            <div class="w-full sm:w-64">
                                @php
                                    $typeOptions = [
                                        'all' => __('ui.type_all'),
                                        'masuk' => __('ui.type_in_label'),
                                        'keluar' => __('ui.type_out_label'),
                                    ];
                                @endphp
                                <label for="type-filter" class="sr-only">{{ __('ui.type_all') }}</label>
                                <x-select 
                                    name="filter_type" 
                                    id="type-filter"
                                    :options="$typeOptions" 
                                    :selected="request('filter_type', 'all')" 
                                    placeholder="{{ __('ui.type_all') }}" 
                                    :submitOnChange="true" 
                                    width="w-full" 
                                    :allowClear="false"
                                />
                            </div>

                            <div class="w-full sm:w-64">
                                @php
                                    $statusOptions = [
                                        'all' => __('ui.status_all'),
                                        'pending' => __('ui.status_pending_label'),
                                        'approved' => __('ui.status_approved_label'),
                                        'rejected' => __('ui.status_rejected_label'),
                                    ];
                                @endphp
                                <label for="status-filter" class="sr-only">{{ __('ui.status_all') }}</label>
                                <x-select 
                                    name="status" 
                                    id="status-filter"
                                    :options="$statusOptions" 
                                    :selected="request('status', 'pending')" 
                                    placeholder="{{ __('ui.status_all') }}" 
                                    :submitOnChange="true" 
                                    width="w-full" 
                                    :allowClear="false"
                                />
                            </div>

                            @if(request('search') || request('filter_type') || request('status'))
                                <a href="{{ route('inventory.stock-approvals.index') }}" class="btn btn-secondary flex items-center justify-center p-2.5 h-[42px] w-[42px] flex-shrink-0" title="Reset Filter">
                                    <x-icon.restore class="h-5 w-5" />
                                </a>
                            @endif
                        </div>
                    </div>
                </form>
            </div>

            <!-- Session Alerts -->
            @if(session('errors_list'))
                <div class="mb-6 p-4 bg-red-50 border-l-4 border-red-400 text-red-700 rounded-r-xl shadow-sm animate-shake">
                    <p class="font-bold flex items-center gap-2 mb-2">
                        <x-icon.warning class="w-5 h-5 text-red-500" />
                        Beberapa pengajuan gagal diproses:
                    </p>
                    <ul class="list-disc list-inside text-sm space-y-1 ml-6">
                        @foreach(session('errors_list') as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div id="approvals-list-container">
                <!-- Mobile Card View -->
                <div class="md:hidden mb-4 flex items-center justify-between px-1">
                    <label class="flex items-center gap-2.5 cursor-pointer group bg-white border border-secondary-100 rounded-xl px-4 py-2.5 shadow-sm active:scale-95 transition-all">
                        <input type="checkbox" id="select-all-mobile" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500 transition-all">
                        <span class="text-sm font-bold text-secondary-600 group-hover:text-primary-600 transition-colors">Pilih Semua</span>
                    </label>
                </div>
                <div class="md:hidden space-y-4" id="mobile-approvals-list">
                    @forelse ($pendingApprovals as $approval)
                        <x-approval.card :approval="$approval" />
                    @empty
                        <div class="card p-12 text-center text-secondary-500 rounded-xl" id="empty-state-mobile">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-secondary-50 rounded-full flex items-center justify-center mb-4">
                                     <svg class="w-8 h-8 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                                <p class="text-sm font-bold text-secondary-900 mb-1">
                                    @php
                                        $status = request('status', 'pending');
                                        $type = request('filter_type', 'all');
                                        
                                        $titleKey = 'ui.no_data_found';
                                        $descKey = 'ui.no_data_criteria';

                                        if ($status === 'pending') {
                                            if ($type === 'masuk') { $titleKey = 'ui.no_pending_in'; $descKey = 'ui.all_processed_in'; }
                                            elseif ($type === 'keluar') { $titleKey = 'ui.no_pending_out'; $descKey = 'ui.all_processed_out'; }
                                            else { $titleKey = 'ui.no_pending'; $descKey = 'ui.all_processed'; }
                                        } elseif ($status === 'approved') {
                                            if ($type === 'masuk') { $titleKey = 'ui.no_data_in_approved'; }
                                            elseif ($type === 'keluar') { $titleKey = 'ui.no_data_out_approved'; }
                                        } elseif ($status === 'rejected') {
                                            if ($type === 'masuk') { $titleKey = 'ui.no_data_in_rejected'; }
                                            elseif ($type === 'keluar') { $titleKey = 'ui.no_data_out_rejected'; }
                                        }
                                    @endphp
                                    {{ __($titleKey) }}
                                </p>
                                <p class="text-xs text-secondary-500">
                                    {{ __($descKey) }}
                                </p>
                            </div>
                        </div>
                    @endforelse
                </div>

                {{-- Standalone Bulk Form (Outside table to avoid nesting) --}}
                <form id="bulk-approval-form" action="{{ route('inventory.stock-approvals.bulk-approve') }}" method="POST" class="hidden">
                    @csrf
                    <input type="hidden" name="status" id="bulk-status" value="approved">
                    <input type="hidden" name="rejection_reason" id="bulk-rejection-reason" value="">
                    <div id="bulk-ids-container"></div>
                </form>

                <div class="hidden md:block card overflow-hidden border border-secondary-100 shadow-sm rounded-xl">
                    <div class="overflow-x-auto">
                        <table class="table-modern w-full">
                            <thead>
                                <tr class="bg-secondary-50/50">
                                    <th class="w-10 px-2 py-4">
                                        <input type="checkbox" id="select-all" class="rounded border-secondary-300 text-primary-600 shadow-sm focus:ring-primary-500 transition-all">
                                    </th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.item_column') }}</th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.applicant_column') }}</th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.type_column') }}</th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.amount_column') }}</th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.reason_column') }}</th>
                                    <th class="px-2 py-4 text-left text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.date_column') }}</th>
                                    <th class="px-2 py-4 text-right text-[10px] font-bold text-secondary-500 uppercase tracking-wider">{{ __('ui.action_column') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary-100" id="desktop-approvals-list">
                                @forelse ($pendingApprovals as $approval)
                                    <x-approval.table-row :approval="$approval" />
                                @empty
                                    <tr id="empty-state-desktop">
                                        <td colspan="8" class="p-16 text-center text-secondary-500">
                                            <div class="flex flex-col items-center justify-center">
                                                <div class="w-20 h-20 bg-secondary-50 rounded-full flex items-center justify-center mb-4">
                                                    <svg class="w-10 h-10 text-secondary-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                                @php
                                                    $status = request('status', 'pending');
                                                    $type = request('filter_type', 'all');
                                                    
                                                    $titleKey = 'ui.no_data_found';
                                                    $descKey = 'ui.no_data_criteria';

                                                    if ($status === 'pending') {
                                                        if ($type === 'masuk') { $titleKey = 'ui.no_pending_in'; $descKey = 'ui.all_processed_in'; }
                                                        elseif ($type === 'keluar') { $titleKey = 'ui.no_pending_out'; $descKey = 'ui.all_processed_out'; }
                                                        else { $titleKey = 'ui.no_pending'; $descKey = 'ui.all_processed'; }
                                                    } elseif ($status === 'approved') {
                                                        if ($type === 'masuk') { $titleKey = 'ui.no_data_in_approved'; }
                                                        elseif ($type === 'keluar') { $titleKey = 'ui.no_data_out_approved'; }
                                                    } elseif ($status === 'rejected') {
                                                        if ($type === 'masuk') { $titleKey = 'ui.no_data_in_rejected'; }
                                                        elseif ($type === 'keluar') { $titleKey = 'ui.no_data_out_rejected'; }
                                                    }
                                                @endphp
                                                <p class="text-base font-bold text-secondary-900 mb-1">{{ __($titleKey) }}</p>
                                                <p class="text-sm text-secondary-500">{{ __($descKey) }}</p>
                                            </div>
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>

                <div class="mt-6" id="approvals-pagination">
                    {{ $pendingApprovals->links() }}
                </div>
            </div>

        </div>
    </div>

    <!-- Bulk Actions — Sticky bottom bar -->
    @if($pendingApprovals->isNotEmpty() && request('status', 'pending') === 'pending')
    <div id="bulk-actions-container" class="hidden fixed bottom-6 left-1/2 transform -translate-x-1/2 z-50 bg-white/95 backdrop-blur-sm rounded-2xl shadow-2xl border border-secondary-200 p-2.5 sm:px-6 sm:py-4 animate-fade-in-up items-center justify-between sm:justify-start gap-3 sm:gap-6 w-[calc(100%-2rem)] sm:w-auto">
        <div class="flex items-center gap-2 sm:gap-3 sm:border-r border-secondary-200 sm:pr-6 pl-1 sm:pl-0">
            <span class="text-xl sm:text-2xl font-black text-primary-600 tabular-nums" id="selected-count">0</span>
            <div class="flex flex-col leading-tight">
                <span class="text-[10px] sm:text-xs font-bold text-secondary-500 uppercase tracking-wider">Item</span>
                <span class="text-[10px] sm:text-xs font-bold text-secondary-400 uppercase tracking-widest">Dipilih</span>
            </div>
        </div>
        <div class="flex items-center gap-2 sm:gap-3 flex-1 sm:flex-initial">
            <button type="button" onclick="submitBulk('approved')" class="flex-1 sm:flex-none btn btn-success flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all" id="bulk-approve-btn">
                <svg class="w-4 h-4 sm:w-5 sm:h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                <span class="font-bold text-[11px] sm:text-sm whitespace-nowrap">Setujui <span class="hidden sm:inline">Semua</span></span>
            </button>
            <button type="button" onclick="submitBulk('rejected')" class="flex-1 sm:flex-none btn btn-danger flex items-center justify-center gap-1.5 sm:gap-2 px-3 sm:px-5 py-2 sm:py-2.5 rounded-xl shadow-md hover:shadow-lg transition-all" id="bulk-reject-btn">
                <x-icon.close class="w-4 h-4 sm:w-5 sm:h-5 text-white" />
                <span class="font-bold text-[11px] sm:text-sm whitespace-nowrap">Tolak <span class="hidden sm:inline">Semua</span></span>
            </button>
        </div>
    </div>
    @endif

    @push('scripts')
    <script>
        // JS scripts remain largely the same, but with updated selectors if needed
        function confirmReject(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            const button = event.target.closest('button');

            Swal.fire({
                title: '{{ __('ui.confirm_reject_title') }}',
                html: '<p class="text-sm text-secondary-500">{{ __('ui.confirm_reject_text') }}</p>',
                input: 'textarea',
                inputLabel: '{{ __('ui.rejection_reason') }}',
                inputPlaceholder: '{{ __('ui.rejection_reason') }}... ({{ __('ui.required') ?? 'wajib diisi' }})',
                inputAttributes: { 'aria-label': 'Alasan Penolakan', maxlength: 500 },
                inputValidator: (value) => {
                    if (!value || value.trim() === '') {
                        return 'Mohon cantumkan alasan penolakan yang jelas.';
                    }
                },
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: '{{ __('ui.btn_yes_reject') }}',
                cancelButtonText: '{{ __('ui.btn_cancel') }}',
                reverseButtons: true,
                customClass: {
                    popup: '!rounded-2xl !font-sans !p-8 !flex !flex-col',
                    icon: '!mb-4 !mx-auto',
                    title: '!text-secondary-900 !text-xl !font-bold !m-0 !mb-2 !text-center',
                    htmlContainer: '!text-secondary-500 !text-sm !m-0 !mb-8 !text-center',
                    inputLabel: '!text-secondary-700 !text-sm !font-semibold !m-0 !mb-3 !text-center !block !w-full',
                    input: '!m-0 !mb-6 !w-full !rounded-xl !border-secondary-200 !p-4 !text-sm focus:!ring-primary-500 focus:!border-primary-500 !shadow-sm transition-all',
                    validationMessage: '!bg-danger-50 !text-danger-600 !border !border-danger-100 !rounded-xl !p-3 !text-xs !mt-[-1rem] !mb-6 !flex !items-center !justify-center !gap-2 !w-full !font-medium !shadow-sm !mx-0',
                    actions: '!flex !justify-end !gap-3 !m-0 !mt-2 !w-full',
                    confirmButton: 'btn btn-danger !px-8 !py-2.5 rounded-xl shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-danger-500 !m-0',
                    cancelButton: 'btn btn-secondary !px-8 !py-2.5 rounded-xl bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm !m-0'
                },
                buttonsStyling: false,
                iconColor: '#ef4444',
                padding: '2em',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    const reasonInput = form.querySelector('.rejection-reason-input');
                    if (reasonInput) reasonInput.value = result.value;
                    button.disabled = true;
                    button.style.opacity = '0.6';
                    form.submit();
                }
            });
        }

        function confirmApprove(event) {
            event.preventDefault();
            const form = event.target.closest('form');
            const button = event.target.closest('button');

            Swal.fire({
                title: '{{ __('ui.confirm_approve_title') ?? 'Konfirmasi Persetujuan' }}',
                text: "{{ __('ui.confirm_approve_text') ?? 'Apakah Anda yakin ingin menyetujui pengajuan stok ini?' }}",
                icon: 'question',
                showCancelButton: true,
                confirmButtonText: '{{ __('ui.btn_yes_approve') ?? 'Ya, Setujui' }}',
                cancelButtonText: '{{ __('ui.btn_cancel') }}',
                reverseButtons: true,
                customClass: {
                    popup: '!rounded-2xl !font-sans !p-8 !flex !flex-col',
                    icon: '!mb-4 !mx-auto',
                    title: '!text-secondary-900 !text-xl !font-bold !m-0 !mb-2 !text-center',
                    htmlContainer: '!text-secondary-500 !text-sm !m-0 !mb-6 !text-center',
                    actions: '!flex !justify-end !gap-3 !m-0 !w-full',
                    confirmButton: 'btn btn-success !px-8 !py-2.5 rounded-xl shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-success-500 !m-0',
                    cancelButton: 'btn btn-secondary !px-8 !py-2.5 rounded-xl bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm !m-0'
                },
                buttonsStyling: false,
                iconColor: '#10b981',
                padding: '2em',
                backdrop: `rgba(0,0,0,0.4)`
            }).then((result) => {
                if (result.isConfirmed) {
                    button.disabled = true;
                    button.style.opacity = '0.6';
                    form.submit();
                }
            });
        }

        document.addEventListener('DOMContentLoaded', function() {
            const selectAll = document.getElementById('select-all');
            const selectAllMobile = document.getElementById('select-all-mobile');
            const bulkContainer = document.getElementById('bulk-actions-container');
            const selectedCountDisplay = document.getElementById('selected-count');

            function updateBulkUI() {
                const checkboxesByClass = document.querySelectorAll('.row-checkbox');
                const checkedCount = Array.from(checkboxesByClass).filter(cb => cb.checked).length;
                
                if (checkedCount > 0) {
                    bulkContainer.classList.remove('hidden');
                    bulkContainer.classList.add('flex');
                    selectedCountDisplay.textContent = checkedCount;
                } else {
                    bulkContainer.classList.add('hidden');
                    bulkContainer.classList.remove('flex');
                }

                // Sync select all status
                const allChecked = checkedCount > 0 && checkedCount === checkboxesByClass.length;
                if (selectAll) selectAll.checked = allChecked;
                if (selectAllMobile) selectAllMobile.checked = allChecked;
            }

            if (selectAll) {
                selectAll.addEventListener('change', function() {
                    document.querySelectorAll('.row-checkbox').forEach(cb => {
                        cb.checked = selectAll.checked;
                    });
                    if (selectAllMobile) selectAllMobile.checked = selectAll.checked;
                    updateBulkUI();
                });
            }

            if (selectAllMobile) {
                selectAllMobile.addEventListener('change', function() {
                    document.querySelectorAll('.row-checkbox').forEach(cb => {
                        cb.checked = selectAllMobile.checked;
                    });
                    if (selectAll) selectAll.checked = selectAllMobile.checked;
                    updateBulkUI();
                });
            }

            document.body.addEventListener('change', function(e) {
                if(e.target.classList.contains('row-checkbox')) {
                    updateBulkUI();
                }
            });

            window.rebindBulkEvents = function() {
                const newSelectAll = document.getElementById('select-all');
                const newSelectAllMobile = document.getElementById('select-all-mobile');
                
                if (newSelectAll) {
                    newSelectAll.addEventListener('change', function() {
                        document.querySelectorAll('.row-checkbox').forEach(cb => {
                            cb.checked = newSelectAll.checked;
                        });
                        if (newSelectAllMobile) newSelectAllMobile.checked = newSelectAll.checked;
                        updateBulkUI();
                    });
                }

                if (newSelectAllMobile) {
                    newSelectAllMobile.addEventListener('change', function() {
                        document.querySelectorAll('.row-checkbox').forEach(cb => {
                            cb.checked = newSelectAllMobile.checked;
                        });
                        if (newSelectAll) newSelectAll.checked = newSelectAllMobile.checked;
                        updateBulkUI();
                    });
                }
            };
            
            if (window.Echo) {
                window.Echo.private('stock-approvals')
                    .listen('.StockApprovalUpdated', (e) => {
                        const checkedIds = Array.from(document.querySelectorAll('.row-checkbox:checked')).map(cb => cb.value);
                        fetch(window.location.href)
                            .then(res => res.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newContainer = doc.getElementById('approvals-list-container');
                                if (newContainer) {
                                    document.getElementById('approvals-list-container').innerHTML = newContainer.innerHTML;
                                    checkedIds.forEach(id => {
                                        const cb = document.querySelector(`.row-checkbox[value="${id}"]`);
                                        if (cb) cb.checked = true;
                                    });
                                    window.rebindBulkEvents();
                                    updateBulkUI();
                                }
                            });
                    });
            }
        });

        function submitBulk(status) {
            const form = document.getElementById('bulk-approval-form');
            const statusInput = document.getElementById('bulk-status');
            const rejectionReasonInput = document.getElementById('bulk-rejection-reason');
            const idsContainer = document.getElementById('bulk-ids-container');
            const selectedCheckBoxes = document.querySelectorAll('.row-checkbox:checked');
            const checkedCount = selectedCheckBoxes.length;
            const approveBtn = document.getElementById('bulk-approve-btn');
            const rejectBtn = document.getElementById('bulk-reject-btn');

            if (checkedCount === 0) return;

            idsContainer.innerHTML = '';
            selectedCheckBoxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                idsContainer.appendChild(input);
            });

            statusInput.value = status;
            const icon = status === 'approved' ? 'question' : 'warning';
            const iconColor = status === 'approved' ? '#10b981' : '#ef4444';
            const btnClass = status === 'approved' ? 'btn btn-success' : 'btn btn-danger';
            const ringColor = status === 'approved' ? 'ring-success-500' : 'ring-danger-500';

            const swalConfig = {
                title: `{{ __('ui.bulk_title') ?? 'Konfirmasi Bulk' }} ${status === 'approved' ? 'Approve' : 'Reject'}`,
                icon: icon,
                showCancelButton: true,
                confirmButtonText: '{{ __('ui.btn_yes_process') ?? 'Ya, Lanjutkan' }}',
                cancelButtonText: '{{ __('ui.btn_cancel') }}',
                reverseButtons: true,
                customClass: {
                    popup: '!rounded-2xl !font-sans !p-8 !flex !flex-col',
                    icon: '!mb-4 !mx-auto',
                    title: '!text-secondary-900 !text-xl !font-bold !m-0 !mb-2 !text-center',
                    htmlContainer: '!text-secondary-500 !text-sm !m-0 !mb-8 !text-center',
                    inputLabel: '!text-secondary-700 !text-sm !font-semibold !m-0 !mb-3 !text-center !block !w-full',
                    input: '!m-0 !mb-6 !w-full !rounded-xl !border-secondary-200 !p-4 !text-sm focus:!ring-primary-500 focus:!border-primary-500 !shadow-sm transition-all',
                    validationMessage: '!bg-danger-50 !text-danger-600 !border !border-danger-100 !rounded-xl !p-3 !text-xs !mt-[-1rem] !mb-6 !flex !items-center !justify-center !gap-2 !w-full !font-medium !shadow-sm !mx-0',
                    actions: '!flex !justify-end !gap-3 !m-0 !mt-2 !w-full',
                    confirmButton: `${btnClass} !px-8 !py-2.5 rounded-xl shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ${ringColor} !m-0`,
                    cancelButton: 'btn btn-secondary !px-8 !py-2.5 rounded-xl bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm !m-0'
                },
                buttonsStyling: false,
                iconColor: iconColor,
                backdrop: `rgba(0,0,0,0.4)`
            };

            if (status === 'approved') {
                swalConfig.html = `<p class="text-sm text-secondary-500">Anda akan menyetujui <strong>${checkedCount}</strong> pengajuan sekaligus. Lanjutkan?</p>`;
                Swal.fire(swalConfig).then((result) => {
                    if (result.isConfirmed) {
                        rejectionReasonInput.value = '';
                        if (approveBtn) { approveBtn.disabled = true; approveBtn.style.opacity = '0.6'; }
                        if (rejectBtn) { rejectBtn.disabled = true; rejectBtn.style.opacity = '0.6'; }
                        form.submit();
                    }
                });
            } else {
                swalConfig.html = `<p class="text-sm text-secondary-500">Anda akan menolak <strong>${checkedCount}</strong> pengajuan sekaligus.</p>`;
                swalConfig.input = 'textarea';
                swalConfig.inputLabel = '{{ __('ui.rejection_reason') }}';
                swalConfig.inputPlaceholder = '{{ __('ui.rejection_reason') }}... ({{ __('ui.required') ?? 'wajib diisi' }})';
                swalConfig.inputAttributes = { maxlength: 500 };
                swalConfig.inputValidator = (value) => {
                    if (!value || value.trim() === '') return 'Mohon cantumkan alasan penolakan yang jelas.';
                };

                Swal.fire(swalConfig).then((result) => {
                    if (result.isConfirmed) {
                        rejectionReasonInput.value = result.value;
                        if (approveBtn) { approveBtn.disabled = true; approveBtn.style.opacity = '0.6'; }
                        if (rejectBtn) { rejectBtn.disabled = true; rejectBtn.style.opacity = '0.6'; }
                        form.submit();
                    }
                });
            }
        }
    </script>
    @endpush
</x-app-layout>
