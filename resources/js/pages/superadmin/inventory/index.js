import Swal from 'sweetalert2';
window.Swal = Swal;

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#inventory-filter-form');
    const tableContainer = document.querySelector('.table-modern')?.parentNode; // The overflow-x-auto div
    const paginationContainer = document.querySelector('.bg-secondary-50'); // Container for pagination
    const realBody = document.querySelector('tbody:not(#skeleton-body)');
    const skeletonBody = document.getElementById('skeleton-body');

    if (form) {
        // Prevent default form submission and use AJAX
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            fetchData(new FormData(form));
        });

        // Handle Input Changes (Debounced)
        const inputs = form.querySelectorAll('input, select');
        let debounceTimer;
        inputs.forEach(input => {
            input.addEventListener('change', function () {
                if (input.name === 'search') return; // Let the form submit or specialized handler for search
                fetchData(new FormData(form));
            });

            // Special handler for search input typing
            if (input.name === 'search') {
                input.addEventListener('input', function () {
                    clearTimeout(debounceTimer);
                    debounceTimer = setTimeout(() => {
                        fetchData(new FormData(form));
                    }, 500);
                });
            }
        });

        // Handle Reset Button
        const resetButton = document.getElementById('reset-filters');
        if (resetButton) {
            resetButton.addEventListener('click', function (e) {
                e.preventDefault();

                // 1. Reset Form Inputs
                form.reset();

                // 2. Clear visual state of x-select (via custom event)
                window.dispatchEvent(new CustomEvent('reset-filters'));

                // 3. Clear search input explicitly if needed
                const searchInput = form.querySelector('input[name="search"]');
                if (searchInput) searchInput.value = '';

                // 4. Reset URL History
                // We want the base route, which is in the reset button href
                const url = this.href;
                window.history.pushState({}, '', url);

                // 5. Fetch clean data data (empty query)
                fetchData(new FormData());
            });
        }
    }

    // Function to handle fetching data
    function fetchData(formData) {
        // 1. Show Skeleton
        if (realBody && skeletonBody) {
            realBody.classList.add('hidden');
            skeletonBody.classList.remove('hidden');
        }

        // 2. Build Query String
        const params = new URLSearchParams(formData);
        // Use form.action to get the URL instead of blade syntax
        const baseUrl = form.getAttribute('action');
        const url = `${baseUrl}?${params.toString()}`;

        // 3. Update Browser URL (for history)
        window.history.pushState({}, '', url);

        // 4. Fetch Data
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
            .then(response => response.text())
            .then(html => {
                // 5. Parse Response
                const parser = new DOMParser();
                const doc = parser.parseFromString(html, 'text/html');

                // 6. Replace Table Body
                const newBody = doc.querySelector('tbody:not(#skeleton-body)');
                if (newBody && realBody) {
                    realBody.innerHTML = newBody.innerHTML;
                }

                // 7. Replace Pagination
                const newPagination = doc.querySelector('.bg-secondary-50');
                if (newPagination && paginationContainer) {
                    paginationContainer.innerHTML = newPagination.innerHTML;
                } else if (newPagination && !paginationContainer) {
                    // If pagination didn't exist but now does
                    // Ensure tableContainer exists
                    if (tableContainer) {
                        tableContainer.parentNode.insertAdjacentHTML('beforeend', newPagination.outerHTML);
                    }
                } else if (!newPagination && paginationContainer) {
                    // If pagination existed but now doesn't
                    paginationContainer.innerHTML = '';
                }

                // 8. Re-attach Pagination Listeners
                attachPaginationListeners();

                // 9. Re-initialize Bulk Actions (if needed)
                const newSelectAll = doc.getElementById('select-all');
                if (document.getElementById('select-all') && newSelectAll) {
                    document.getElementById('select-all').checked = false; // Reset select all
                }
                initBulkActions();

            })
            .catch(error => {
                console.error('Error fetching data:', error);
            })
            .finally(() => {
                // 10. Hide Skeleton
                setTimeout(() => { // Small delay to ensure smooth transition
                    if (realBody && skeletonBody) {
                        skeletonBody.classList.add('hidden');
                        realBody.classList.remove('hidden');
                    }
                }, 300);
            });
    }

    // Function to attach listeners to dynamic pagination links
    function attachPaginationListeners() {
        const paginationLinks = document.querySelectorAll('.pagination a, .page-link, .bg-secondary-50 a');
        paginationLinks.forEach(link => {
            link.addEventListener('click', function (e) {
                e.preventDefault();
                const url = new URL(this.href);
                // Get current form data to keep filters
                const formData = new FormData(form);
                // Update page param
                formData.set('page', url.searchParams.get('page'));
                fetchData(formData);
            });
        });
    }

    // Initial attachment
    attachPaginationListeners();

    // --- Bulk Action Logic (Encapsulated) ---
    function initBulkActions() {
        const selectAllCheckbox = document.getElementById('select-all');
        const bulkCheckboxes = document.querySelectorAll('.bulk-checkbox'); // Re-query these
        const bulkActionBar = document.getElementById('bulk-action-bar');
        const selectedCountSpan = document.getElementById('selected-count');
        const bulkRestoreInputs = document.getElementById('bulk-restore-inputs');
        const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');

        function updateBulkActionBar() {
            const selectedCheckboxes = document.querySelectorAll('.bulk-checkbox:checked');
            const count = selectedCheckboxes.length;

            if (selectedCountSpan) selectedCountSpan.textContent = count;

            if (bulkRestoreInputs) bulkRestoreInputs.innerHTML = '';
            if (bulkDeleteInputs) bulkDeleteInputs.innerHTML = '';

            selectedCheckboxes.forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'ids[]';
                input.value = cb.value;
                if (bulkRestoreInputs) bulkRestoreInputs.appendChild(input.cloneNode());
                if (bulkDeleteInputs) bulkDeleteInputs.appendChild(input.cloneNode());
            });

            if (bulkActionBar) {
                if (count > 0) {
                    bulkActionBar.classList.remove('hidden');
                    bulkActionBar.classList.add('flex');
                } else {
                    bulkActionBar.classList.add('hidden');
                    bulkActionBar.classList.remove('flex');
                }
            }
        }

        if (selectAllCheckbox) {
            const newSelectAll = selectAllCheckbox.cloneNode(true);
            selectAllCheckbox.parentNode.replaceChild(newSelectAll, selectAllCheckbox);

            newSelectAll.addEventListener('change', function () {
                const isChecked = this.checked;
                const currentBulkCheckboxes = document.querySelectorAll('.bulk-checkbox');
                currentBulkCheckboxes.forEach(cb => {
                    cb.checked = isChecked;
                });
                updateBulkActionBar();
            });
        }

        const currentBulkCheckboxes = document.querySelectorAll('.bulk-checkbox');
        if (currentBulkCheckboxes.length > 0) {
            currentBulkCheckboxes.forEach(cb => {
                cb.addEventListener('change', function () {
                    const allChecked = Array.from(document.querySelectorAll('.bulk-checkbox')).every(c => c.checked);
                    const sa = document.getElementById('select-all');
                    if (sa) sa.checked = allChecked;
                    updateBulkActionBar();
                });
            });
        }
    }

    // Initial call
    initBulkActions();
});

// Global Bulk Action Handlers (Inventory)
window.submitInventoryBulkRestore = function () {
    const selected = document.querySelectorAll('.bulk-checkbox:checked');
    if (selected.length === 0) return;

    Swal.fire({
        title: 'Pulihkan Item?',
        text: `${selected.length} item akan dipulihkan.`,
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Pulihkan!',
        cancelButtonText: 'Batal',
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
        backdrop: `rgba(0,0,0,0.4)`
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('bulk-restore-form').submit();
        }
    });
};

window.submitInventoryBulkDelete = function () {
    const selected = document.querySelectorAll('.bulk-checkbox:checked');
    if (selected.length === 0) return;

    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak akan bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
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
        backdrop: `rgba(0,0,0,0.4)`
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById('bulk-delete-form').submit();
        }
    });
};

// Single Row Action Handlers
window.confirmInventoryRestore = function (event) {
    event.preventDefault();
    const form = event.target.closest('form');
    Swal.fire({
        title: 'Pulihkan Item Ini?',
        text: "Item akan dipulihkan ke daftar aktif.",
        icon: 'question',
        showCancelButton: true,
        confirmButtonText: 'Ya, Pulihkan!',
        cancelButtonText: 'Batal',
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
        backdrop: `rgba(0,0,0,0.4)`
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
};

window.confirmInventoryForceDelete = function (event) {
    event.preventDefault();
    const form = event.target.closest('form');
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak akan bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
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
        backdrop: `rgba(0,0,0,0.4)`
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
};

window.confirmDelete = function (event) {
    event.preventDefault();
    const form = event.target.closest('form');
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Data yang dihapus tidak akan bisa dikembalikan!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, Hapus!',
        cancelButtonText: 'Batal',
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
        backdrop: `rgba(0,0,0,0.4)`
    }).then((result) => {
        if (result.isConfirmed) {
            form.submit();
        }
    });
};
