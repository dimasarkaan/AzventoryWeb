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

            window.clearBulkSelection = function() {
                document.querySelectorAll('.row-checkbox').forEach(cb => cb.checked = false);
                if (selectAll) selectAll.checked = false;
                if (selectAllMobile) selectAllMobile.checked = false;
                updateBulkUI();
            };

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
                        form.submit();
                        setTimeout(resolve, 3000);
                    });
                };

                Swal.fire(swalConfig);
            }
        }
    </script>
    @endpush
