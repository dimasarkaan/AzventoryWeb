    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Bulk Actions Logic ---
            const selectAll = document.getElementById('selectAll');
            const floatingBar = document.getElementById('bulk-action-bar');
            const countLabel = document.getElementById('selected-count');
            
            // Function to attach checkbox listeners (needed for initial load AND after AJAX)
            window.attachCheckboxListeners = function() {
                const checkboxes = document.querySelectorAll('.user-checkbox');
                checkboxes.forEach(cb => {
                    // Remove old listener to avoid duplicates if any (though replacement helps)
                    cb.removeEventListener('change', updateFloatingBar);
                    cb.addEventListener('change', updateFloatingBar);
                });
            };

            // Shift-Click Multiple Selection Logic
            let lastCheckedBox = null;
            document.addEventListener('click', function(e) {
                if (e.target.classList.contains('user-checkbox')) {
                    if (e.shiftKey && lastCheckedBox) {
                        const checkboxes = Array.from(document.querySelectorAll('.user-checkbox'));
                        const start = checkboxes.indexOf(lastCheckedBox);
                        const end = checkboxes.indexOf(e.target);
                        
                        if (start !== -1 && end !== -1) {
                            const min = Math.min(start, end);
                            const max = Math.max(start, end);
                            const isChecked = lastCheckedBox.checked;
                            
                            for (let i = min; i <= max; i++) {
                                checkboxes[i].checked = isChecked;
                            }
                        }
                    }
                    lastCheckedBox = e.target;
                    updateFloatingBar();
                }
            });

            window.clearBulkSelection = function() {
                document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
                if (selectAll) selectAll.checked = false;
                updateFloatingBar();
            };

            window.updateFloatingBar = function() {
                if(!floatingBar) return;
                
                const selected = document.querySelectorAll('.user-checkbox:checked');
                const count = selected.length;
                
                if(countLabel) countLabel.textContent = count;
                
                if(count > 0) {
                    floatingBar.classList.remove('translate-y-24', 'opacity-0');
                    floatingBar.classList.add('translate-y-0', 'opacity-100');
                } else {
                    floatingBar.classList.add('translate-y-24', 'opacity-0');
                    floatingBar.classList.remove('translate-y-0', 'opacity-100');
                }
            };

            // Initial Attach
            attachCheckboxListeners();

            if(selectAll) {
                selectAll.addEventListener('change', function() {
                    const checkboxes = document.querySelectorAll('.user-checkbox');
                    checkboxes.forEach(cb => cb.checked = this.checked);
                    updateFloatingBar();
                });
            }

            // Global Submit Functions
            window.submitBulkRestore = function() {
                const selected = document.querySelectorAll('.user-checkbox:checked');
                if(selected.length === 0) return;

                Swal.fire({
                    title: '{{ __('ui.restore_user_title') }}',
                    text: `${selected.length} {{ __('ui.restore_user_confirm') }}`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('ui.yes_restore') }}',
                    cancelButtonText: '{{ __('ui.cancel') }}',
                    reverseButtons: true,
                     customClass: {
                        popup: '!rounded-2xl !font-sans',
                         title: '!text-secondary-900 !text-xl !font-bold',
                        htmlContainer: '!text-secondary-500 !text-sm',
                        confirmButton: 'btn btn-success px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-success-500',
                        cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                    },
                    buttonsStyling: false,
                    width: '24em',
                    iconColor: '#10b981', 
                    padding: '2em',
                    backdrop: `rgba(0,0,0,0.4)`,
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading(),
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            const form = document.getElementById('bulk-restore-form');
                            // Clear previous hidden inputs
                            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                            
                            selected.forEach(cb => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'ids[]';
                                input.value = cb.value;
                                form.appendChild(input);
                            });
                            form.submit();
                            setTimeout(resolve, 3000);
                        });
                    }
                });
            };

            window.submitBulkDelete = function() {
                const selected = document.querySelectorAll('.user-checkbox:checked');
                if(selected.length === 0) return;

                Swal.fire({
                    title: '{{ __('ui.delete_user_title') }}',
                    text: "{{ __('ui.delete_permanent_confirm') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('ui.yes_delete') }}',
                    cancelButtonText: '{{ __('ui.cancel') }}',
                    reverseButtons: true,
                    customClass: {
                        popup: '!rounded-2xl !font-sans',
                        title: '!text-secondary-900 !text-xl !font-bold',
                        htmlContainer: '!text-secondary-500 !text-sm',
                        confirmButton: 'btn btn-danger px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-danger-500',
                        cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                    },
                    buttonsStyling: false,
                    width: '24em',
                    iconColor: '#ef4444',
                    padding: '2em',
                    backdrop: `rgba(0,0,0,0.4)`,
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading(),
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            const form = document.getElementById('bulk-delete-form');
                            // Clear previous hidden inputs
                            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                            
                            selected.forEach(cb => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'ids[]';
                                input.value = cb.value;
                                form.appendChild(input);
                            });
                            form.submit();
                            setTimeout(resolve, 3000);
                        });
                    }
                });
            };

            // Single Row Action Handlers
            window.confirmUserRestore = function(event) {
                event.preventDefault();
                const form = event.target.closest('form');
                Swal.fire({
                    title: '{{ __('ui.restore_user_title') }}',
                    text: "{{ __('ui.restore_user_confirm') }}",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('ui.yes_restore') }}',
                    cancelButtonText: '{{ __('ui.cancel') }}',
                    reverseButtons: true,
                    customClass: {
                        popup: '!rounded-2xl !font-sans',
                        title: '!text-secondary-900 !text-xl !font-bold',
                        htmlContainer: '!text-secondary-500 !text-sm',
                        confirmButton: 'btn btn-success px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-success-500',
                        cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                    },
                    buttonsStyling: false,
                    width: '24em',
                    iconColor: '#10b981', 
                    padding: '2em',
                    backdrop: `rgba(0,0,0,0.4)`,
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading(),
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            form.submit();
                            setTimeout(resolve, 3000);
                        });
                    }
                });
            };

            window.confirmUserForceDelete = function(event) {
                event.preventDefault();
                const form = event.target.closest('form');
                Swal.fire({
                    title: '{{ __('ui.delete_user_title') }}',
                    text: "{{ __('ui.delete_permanent_confirm') }}",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: '{{ __('ui.yes_delete') }}',
                    cancelButtonText: '{{ __('ui.cancel') }}',
                    reverseButtons: true,
                    customClass: {
                        popup: '!rounded-2xl !font-sans',
                        title: '!text-secondary-900 !text-xl !font-bold',
                        htmlContainer: '!text-secondary-500 !text-sm',
                        confirmButton: 'btn btn-danger px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-danger-500',
                        cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                    },
                    buttonsStyling: false,
                    width: '24em',
                    iconColor: '#ef4444',
                    padding: '2em',
                    backdrop: `rgba(0,0,0,0.4)`,
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading(),
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            form.submit();
                            setTimeout(resolve, 3000);
                        });
                    }
                });
            };


            // --- Filter & Pagination Logic ---
            const filterForm = document.querySelector('form[action="{{ route('users.index') }}"]');
            
            const realBody = document.querySelector('tbody:not(#skeleton-body)');
            const skeletonBody = document.getElementById('skeleton-body');
            const paginationContainer = document.querySelector('.mt-6');
            const tableContainer = document.querySelector('.table-modern')?.parentNode;
            const resetBtn = document.getElementById('reset-filters');

            if (filterForm) {
                // Prevent default form submission and use AJAX
                filterForm.addEventListener('submit', function(e) {
                    e.preventDefault();
                    fetchData(new FormData(filterForm));
                });

                // Handle Input Changes
                const inputs = filterForm.querySelectorAll('input, select');
                let debounceTimer;
                inputs.forEach(input => {
                    input.addEventListener('change', function() {
                        if(input.name === 'search') return; // Search input handled by 'input' event
                        fetchData(new FormData(filterForm));
                    });
                    
                    if(input.name === 'search') {
                        input.addEventListener('input', function() {
                            clearTimeout(debounceTimer);
                            debounceTimer = setTimeout(() => {
                                fetchData(new FormData(filterForm));
                            }, 500);
                        });
                    }
                });

                // Handle Reset Button
                if (resetBtn) {
                    resetBtn.addEventListener('click', function(e) {
                        e.preventDefault();
                        const url = this.getAttribute('href'); // This is the base index url
                        
                        // Reset form visually
                        filterForm.reset();
                        
                        // Manually clear values for inputs that might not reset with form.reset()
                         inputs.forEach(input => {
                            if(input.type === 'text' || input.type === 'search') input.value = '';
                            if(input.tagName === 'SELECT') {
                                // For x-select, it might have a hidden input or a custom way to reset
                                // For now, assume default select behavior or rely on fetchData to rebuild URL
                                input.value = ''; // Clear selected value
                                // Trigger change event for x-select to update its display if needed
                                const event = new Event('change');
                                input.dispatchEvent(event);
                            }
                        });
                        
                        fetchData(new FormData(filterForm));
                    });
                }
            }

            // AJAX Fetch Function
            function fetchData(formData) {
                if (realBody && skeletonBody) {
                    realBody.classList.add('hidden');
                    skeletonBody.classList.remove('hidden');
                }

                const params = new URLSearchParams(formData);
                const url = `{{ route('users.index') }}?${params.toString()}`;
                
                window.history.pushState({}, '', url);

                fetch(url, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Replace Table Body
                    const newBody = doc.querySelector('tbody:not(#skeleton-body)');
                    if (newBody && realBody) {
                        realBody.innerHTML = newBody.innerHTML;
                        
                        // Re-attach checkbox listeners!
                        if(typeof attachCheckboxListeners === 'function') {
                            attachCheckboxListeners();
                        }
                        // Uncheck selectAll
                        if(selectAll) selectAll.checked = false;
                        if(typeof updateFloatingBar === 'function') updateFloatingBar();
                    }

                    // Replace Pagination
                    const newPagination = doc.querySelector('.mt-6');
                    if (newPagination && paginationContainer) {
                        paginationContainer.innerHTML = newPagination.innerHTML;
                        // Event delegation handles pagination listeners automatically.
                    } else if (newPagination && !paginationContainer) {
                          if (tableContainer) {
                             tableContainer.insertAdjacentHTML('afterend', newPagination.outerHTML);
                          }
                    } else if (!newPagination && paginationContainer) {
                        paginationContainer.innerHTML = '';
                    }

                })
                .finally(() => {
                    setTimeout(() => {
                        if (realBody && skeletonBody) {
                            skeletonBody.classList.add('hidden');
                            realBody.classList.remove('hidden');
                        }
                    }, 300);
                });
            }

            // --- Pagination Event Delegation ---
            document.addEventListener('click', function(e) {
                const link = e.target.closest('.mt-6 a');
                if (link) {
                    e.preventDefault();
                    const url = new URL(link.href);
                    const page = url.searchParams.get('page');
                    if (page) {
                        const currentFormData = new FormData(filterForm);
                        currentFormData.set('page', page);
                        fetchData(currentFormData);
                    }
                }
            });
        });
    </script>
