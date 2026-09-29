    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Bulk Actions Logic ---
            const selectAll = document.getElementById('selectAll');
            const floatingBar = document.getElementById('bulk-action-bar');
            const countLabel = document.getElementById('selected-count');
            
            // Persistent storage for full page reloads
            const STORAGE_KEY = 'azventory_user_bulk_selections';
            
            window.getSelectedUserIds = function() {
                try {
                    return new Set(JSON.parse(sessionStorage.getItem(STORAGE_KEY)) || []);
                } catch(e) {
                    return new Set();
                }
            };
            
            window.saveSelectedUserIds = function(set) {
                sessionStorage.setItem(STORAGE_KEY, JSON.stringify(Array.from(set)));
            };

            // Function to attach checkbox listeners (needed for initial load AND after AJAX)
            window.attachCheckboxListeners = function() {
                const checkboxes = document.querySelectorAll('.user-checkbox');
                const selectedIds = getSelectedUserIds();
                
                checkboxes.forEach(cb => {
                    // Restore checked state from storage
                    cb.checked = selectedIds.has(cb.value);
                    
                    cb.removeEventListener('change', handleCheckboxChange);
                    cb.addEventListener('change', handleCheckboxChange);
                });
                
                // Sync select all state
                syncSelectAllState();
                updateFloatingBar();
            };
            
            function handleCheckboxChange(e) {
                const selectedIds = getSelectedUserIds();
                if (e.target.checked) {
                    selectedIds.add(e.target.value);
                } else {
                    selectedIds.delete(e.target.value);
                }
                saveSelectedUserIds(selectedIds);
                syncSelectAllState();
                updateFloatingBar();
            }
            
            function syncSelectAllState() {
                const checkboxes = document.querySelectorAll('.user-checkbox');
                const allChecked = checkboxes.length > 0 && Array.from(checkboxes).every(c => c.checked);
                
                if (selectAll) selectAll.checked = allChecked;
                const mobileSelectAll = document.getElementById('mobile-select-all');
                if (mobileSelectAll) mobileSelectAll.checked = allChecked;
            }

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
                            
                            const selectedIds = getSelectedUserIds();
                            for (let i = min; i <= max; i++) {
                                checkboxes[i].checked = isChecked;
                                if (isChecked) {
                                    selectedIds.add(checkboxes[i].value);
                                } else {
                                    selectedIds.delete(checkboxes[i].value);
                                }
                            }
                            saveSelectedUserIds(selectedIds);
                        }
                    }
                    lastCheckedBox = e.target;
                    syncSelectAllState();
                    updateFloatingBar();
                }
            });

            window.clearBulkSelection = function() {
                sessionStorage.removeItem(STORAGE_KEY);
                document.querySelectorAll('.user-checkbox').forEach(cb => cb.checked = false);
                if (selectAll) selectAll.checked = false;
                const mobileSelectAll = document.getElementById('mobile-select-all');
                if (mobileSelectAll) mobileSelectAll.checked = false;
                updateFloatingBar();
            };

            window.updateFloatingBar = function() {
                if(!floatingBar) return;
                
                const selectedIds = getSelectedUserIds();
                const count = selectedIds.size;
                
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
                    const isChecked = this.checked;
                    const selectedIds = getSelectedUserIds();
                    
                    document.querySelectorAll('.user-checkbox').forEach(cb => {
                        cb.checked = isChecked;
                        if (isChecked) {
                            selectedIds.add(cb.value);
                        } else {
                            selectedIds.delete(cb.value);
                        }
                    });
                    
                    saveSelectedUserIds(selectedIds);
                    
                    const mobileSelectAll = document.getElementById('mobile-select-all');
                    if(mobileSelectAll) mobileSelectAll.checked = isChecked;
                    updateFloatingBar();
                });
            }

            const mobileSelectAll = document.getElementById('mobile-select-all');
            if(mobileSelectAll) {
                mobileSelectAll.addEventListener('change', function() {
                    const isChecked = this.checked;
                    const selectedIds = getSelectedUserIds();
                    
                    document.querySelectorAll('.user-checkbox').forEach(cb => {
                        cb.checked = isChecked;
                        if (isChecked) {
                            selectedIds.add(cb.value);
                        } else {
                            selectedIds.delete(cb.value);
                        }
                    });
                    
                    saveSelectedUserIds(selectedIds);
                    
                    if(selectAll) selectAll.checked = isChecked;
                    updateFloatingBar();
                });
            }

            // Global Submit Functions
            window.submitBulkRestore = function() {
                const selectedIds = Array.from(getSelectedUserIds());
                if(selectedIds.length === 0) return;

                Swal.fire({
                    title: '{{ __('ui.restore_user_title') }}',
                    text: `${selectedIds.length} {{ __('ui.restore_user_confirm') }}`,
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
                            
                            selectedIds.forEach(id => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'ids[]';
                                input.value = id;
                                form.appendChild(input);
                            });
                            sessionStorage.removeItem(STORAGE_KEY);
                            form.submit();
                            setTimeout(resolve, 3000);
                        });
                    }
                });
            };

            window.submitBulkDelete = function() {
                const selectedIds = Array.from(getSelectedUserIds());
                if(selectedIds.length === 0) return;

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
                        confirmButton: 'btn border-0 bg-rose-600 hover:bg-rose-800 text-white px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-rose-500',
                        cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                    },
                    buttonsStyling: false,
                    width: '24em',
                    iconColor: '#f43f5e',
                    padding: '2em',
                    backdrop: `rgba(0,0,0,0.4)`,
                    showLoaderOnConfirm: true,
                    allowOutsideClick: () => !Swal.isLoading(),
                    preConfirm: () => {
                        return new Promise((resolve) => {
                            const form = document.getElementById('bulk-delete-form');
                            // Clear previous hidden inputs
                            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                            
                            selectedIds.forEach(id => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'ids[]';
                                input.value = id;
                                form.appendChild(input);
                            });
                            sessionStorage.removeItem(STORAGE_KEY);
                            form.submit();
                            setTimeout(resolve, 3000);
                        });
                    }
                });
            };

            window.submitBulkDestroy = function() {
                const selectedIds = Array.from(getSelectedUserIds());
                if(selectedIds.length === 0) return;

                Swal.fire({
                    title: '{{ __('ui.delete_user_title') }}',
                    text: `${selectedIds.length} data pengguna akan dihapus. Anda dapat memulihkannya nanti di tempat sampah.`,
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
                            const form = document.getElementById('bulk-destroy-form');
                            // Clear previous hidden inputs
                            form.querySelectorAll('input[name="ids[]"]').forEach(el => el.remove());
                            
                            selectedIds.forEach(id => {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = 'ids[]';
                                input.value = id;
                                form.appendChild(input);
                            });
                            sessionStorage.removeItem(STORAGE_KEY);
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
                        confirmButton: 'btn border-0 bg-rose-600 hover:bg-rose-800 text-white px-6 py-2.5 rounded-lg ml-3 shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-rose-500',
                        cancelButton: 'btn btn-secondary px-6 py-2.5 rounded-lg bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm'
                    },
                    buttonsStyling: false,
                    width: '24em',
                    iconColor: '#f43f5e',
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
