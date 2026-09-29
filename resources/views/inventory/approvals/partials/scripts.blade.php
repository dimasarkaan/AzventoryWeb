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
                    popup: '!rounded-3xl !shadow-2xl !border !border-secondary-100',
                    title: '!text-secondary-900 !text-xl !font-bold !mt-2',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    inputLabel: '!text-secondary-700 !text-sm !font-bold !mt-4 !mb-2 !text-left !block',
                    input: '!rounded-xl !border-secondary-300 !p-3 !text-sm focus:!ring-primary-500 focus:!border-primary-500 !shadow-sm transition-all',
                    validationMessage: '!bg-danger-50 !text-danger-600 !border !border-danger-100 !rounded-xl !p-3 !text-xs !mt-2 !mb-0 !flex !items-center !justify-center !gap-2 !w-full !font-medium',
                    actions: '!flex !justify-center !gap-3 !w-full !mt-6',
                    confirmButton: 'btn btn-danger !px-6 !py-2.5 !m-0 !rounded-xl',
                    cancelButton: 'btn btn-secondary !px-6 !py-2.5 !m-0 !rounded-xl bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                },
                buttonsStyling: false,
                iconColor: '#ef4444',
                width: '28em',
                backdrop: `rgba(15, 23, 42, 0.5)`,
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: (value) => {
                    return new Promise((resolve) => {
                        const reasonInput = form.querySelector('.rejection-reason-input');
                        if (reasonInput) reasonInput.value = value;
                        button.disabled = true;
                        button.style.opacity = '0.6';
                        form.submit();
                        setTimeout(resolve, 3000);
                    });
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
                    popup: '!rounded-3xl !shadow-2xl !border !border-secondary-100',
                    title: '!text-secondary-900 !text-xl !font-bold !mt-2',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    actions: '!flex !justify-center !gap-3 !w-full !mt-6',
                    confirmButton: 'btn btn-success !px-6 !py-2.5 !m-0 !rounded-xl',
                    cancelButton: 'btn btn-secondary !px-6 !py-2.5 !m-0 !rounded-xl bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                },
                buttonsStyling: false,
                iconColor: '#10b981',
                width: '26em',
                backdrop: `rgba(15, 23, 42, 0.5)`,
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading(),
                preConfirm: () => {
                    return new Promise((resolve) => {
                        button.disabled = true;
                        button.style.opacity = '0.6';
                        form.submit();
                        setTimeout(resolve, 3000);
                    });
                }
            });
        }

        const STORAGE_KEY = 'az_approval_bulk_selections';
        let lastCheckedBox = null;

        window.getApprovalBulkIds = function() {
            try {
                return new Set(JSON.parse(sessionStorage.getItem(STORAGE_KEY)) || []);
            } catch(e) {
                return new Set();
            }
        };

        window.saveApprovalBulkIds = function(set) {
            sessionStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(set)));
        };

        function updateBulkUI() {
            const bulkContainer = document.getElementById('bulk-actions-container');
            const selectedCountDisplay = document.getElementById('selected-count');
            const selectAll = document.getElementById('select-all');
            const selectAllMobile = document.getElementById('select-all-mobile');

            const selectedIds = window.getApprovalBulkIds();
            const checkedCount = selectedIds.size;
            
            if (checkedCount > 0) {
                if (bulkContainer) {
                    bulkContainer.classList.remove('hidden');
                    bulkContainer.classList.add('flex');
                }
                if (selectedCountDisplay) selectedCountDisplay.textContent = checkedCount;
            } else {
                if (bulkContainer) {
                    bulkContainer.classList.add('hidden');
                    bulkContainer.classList.remove('flex');
                }
            }

            // Sync select all status (if all checkboxes on current page are checked and > 0)
            const checkboxesByClass = document.querySelectorAll('.row-checkbox');
            const allChecked = checkboxesByClass.length > 0 && Array.from(checkboxesByClass).every(cb => cb.checked);
            if (selectAll) selectAll.checked = allChecked;
            if (selectAllMobile) selectAllMobile.checked = allChecked;
        }

        window.clearBulkSelection = function() {
            sessionStorage.removeItem(STORAGE_KEY);
            document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
            const selectAll = document.getElementById('select-all');
            const selectAllMobile = document.getElementById('select-all-mobile');
            if (selectAll) selectAll.checked = false;
            if (selectAllMobile) selectAllMobile.checked = false;
            updateBulkUI();
        };

        window.restoreApprovalBulkState = function() {
            const selectedIds = window.getApprovalBulkIds();
            document.querySelectorAll('.row-checkbox').forEach(cb => {
                cb.checked = selectedIds.has(cb.value);
            });
            updateBulkUI();
        };

        document.addEventListener('DOMContentLoaded', function() {
            window.restoreApprovalBulkState();

            // Event Delegation for Checkboxes
            document.body.addEventListener('change', function(e) {
                // Select All Logic
                if (e.target.id === 'select-all' || e.target.id === 'select-all-mobile') {
                    const isChecked = e.target.checked;
                    const selectedIds = window.getApprovalBulkIds();
                    
                    document.querySelectorAll('.row-checkbox').forEach(cb => {
                        cb.checked = isChecked;
                        if (isChecked) {
                            selectedIds.add(cb.value);
                        } else {
                            selectedIds.delete(cb.value);
                        }
                    });
                    
                    window.saveApprovalBulkIds(selectedIds);
                    
                    const selectAll = document.getElementById('select-all');
                    const selectAllMobile = document.getElementById('select-all-mobile');
                    if (selectAll) selectAll.checked = isChecked;
                    if (selectAllMobile) selectAllMobile.checked = isChecked;
                    
                    updateBulkUI();
                    return;
                }

                // Individual Checkbox Logic
                if (e.target.classList.contains('row-checkbox')) {
                    const selectedIds = window.getApprovalBulkIds();
                    if (e.target.checked) {
                        selectedIds.add(e.target.value);
                    } else {
                        selectedIds.delete(e.target.value);
                    }
                    window.saveApprovalBulkIds(selectedIds);
                    updateBulkUI();
                }
            });

            // Shift-Click Logic
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('row-checkbox')) {
                    if (e.shiftKey && lastCheckedBox) {
                        const checkboxes = Array.from(document.querySelectorAll('.row-checkbox'));
                        const start = checkboxes.indexOf(lastCheckedBox);
                        const end = checkboxes.indexOf(e.target);
                        
                        if (start !== -1 && end !== -1) {
                            const min = Math.min(start, end);
                            const max = Math.max(start, end);
                            
                            const selectedIds = window.getApprovalBulkIds();
                            for (let i = min; i <= max; i++) {
                                if (checkboxes[i] !== e.target) {
                                    checkboxes[i].checked = e.target.checked;
                                    if (e.target.checked) {
                                        selectedIds.add(checkboxes[i].value);
                                    } else {
                                        selectedIds.delete(checkboxes[i].value);
                                    }
                                }
                            }
                            window.saveApprovalBulkIds(selectedIds);
                            updateBulkUI();
                        }
                    }
                    lastCheckedBox = e.target;
                }
            });
            
            if (window.Echo) {
                window.Echo.private('stock-approvals')
                    .listen('.StockApprovalUpdated', (e) => {
                        fetch(window.location.href)
                            .then(res => res.text())
                            .then(html => {
                                const parser = new DOMParser();
                                const doc = parser.parseFromString(html, 'text/html');
                                const newContainer = doc.getElementById('approvals-list-container');
                                if (newContainer) {
                                    document.getElementById('approvals-list-container').innerHTML = newContainer.innerHTML;
                                    window.restoreApprovalBulkState();
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
            const selectedIds = window.getApprovalBulkIds();
            const checkedCount = selectedIds.size;
            const approveBtn = document.getElementById('bulk-approve-btn');
            const rejectBtn = document.getElementById('bulk-reject-btn');

            if (checkedCount === 0) return;

            idsContainer.innerHTML = '';
            selectedIds.forEach(id => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = id;
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
                    popup: '!rounded-3xl !shadow-2xl !border !border-secondary-100',
                    title: '!text-secondary-900 !text-xl !font-bold !mt-2',
                    htmlContainer: '!text-secondary-500 !text-sm',
                    inputLabel: '!text-secondary-700 !text-sm !font-bold !mt-4 !mb-2 !text-left !block',
                    input: '!rounded-xl !border-secondary-300 !p-3 !text-sm focus:!ring-primary-500 focus:!border-primary-500 !shadow-sm transition-all',
                    validationMessage: '!bg-danger-50 !text-danger-600 !border !border-danger-100 !rounded-xl !p-3 !text-xs !mt-2 !mb-0 !flex !items-center !justify-center !gap-2 !w-full !font-medium',
                    actions: '!flex !justify-center !gap-3 !w-full !mt-6',
                    confirmButton: `${btnClass} !px-6 !py-2.5 !m-0 !rounded-xl`,
                    cancelButton: 'btn btn-secondary !px-6 !py-2.5 !m-0 !rounded-xl bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                },
                buttonsStyling: false,
                iconColor: iconColor,
                width: '28em',
                backdrop: `rgba(15, 23, 42, 0.5)`,
                showLoaderOnConfirm: true,
                allowOutsideClick: () => !Swal.isLoading()
            };

            if (status === 'approved') {
                swalConfig.html = `<p class="text-sm text-secondary-500">Anda akan menyetujui <strong>${checkedCount}</strong> pengajuan sekaligus. Lanjutkan?</p>`;
                swalConfig.preConfirm = () => {
                    return new Promise((resolve) => {
                        rejectionReasonInput.value = '';
                        if (approveBtn) { approveBtn.disabled = true; approveBtn.style.opacity = '0.6'; }
                        if (rejectBtn) { rejectBtn.disabled = true; rejectBtn.style.opacity = '0.6'; }
                        sessionStorage.removeItem(STORAGE_KEY);
                        form.submit();
                        setTimeout(resolve, 3000);
                    });
                };
                Swal.fire(swalConfig);
            } else {
                swalConfig.html = `<p class="text-sm text-secondary-500">Anda akan menolak <strong>${checkedCount}</strong> pengajuan sekaligus.</p>`;
                swalConfig.input = 'textarea';
                swalConfig.inputLabel = '{{ __('ui.rejection_reason') }}';
                swalConfig.inputPlaceholder = '{{ __('ui.rejection_reason') }}... ({{ __('ui.required') ?? 'wajib diisi' }})';
                swalConfig.inputAttributes = { maxlength: 500 };
                swalConfig.inputValidator = (value) => {
                    if (!value || value.trim() === '') return 'Mohon cantumkan alasan penolakan yang jelas.';
                };
                swalConfig.preConfirm = (value) => {
                    return new Promise((resolve) => {
                        rejectionReasonInput.value = value;
                        if (approveBtn) { approveBtn.disabled = true; approveBtn.style.opacity = '0.6'; }
                        if (rejectBtn) { rejectBtn.disabled = true; rejectBtn.style.opacity = '0.6'; }
                        sessionStorage.removeItem(STORAGE_KEY);
                        form.submit();
                        setTimeout(resolve, 3000);
                    });
                };

                Swal.fire(swalConfig);
            }
        }
    </script>
    @endpush
