import Swal from 'sweetalert2';
window.Swal = Swal;

document.addEventListener('DOMContentLoaded', function () {
    const form = document.querySelector('#inventory-filter-form');
    const realBody = document.querySelector('#inventory-desktop-body');
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

        // 4. Fetch Data (server returns JSON with rendered HTML partials)
        fetch(url, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'Accept': 'application/json'
            }
        })
            .then(response => response.json())
            .then(data => {
                // 5. Inject Desktop Table
                const desktopContainer = document.getElementById('inventory-desktop-container');
                if (desktopContainer && data.desktop) {
                    const parser = new DOMParser();
                    const desktopDoc = parser.parseFromString(data.desktop, 'text/html');
                    const newDesktop = desktopDoc.getElementById('inventory-desktop-container');
                    if (newDesktop) {
                        desktopContainer.innerHTML = newDesktop.innerHTML;
                    }
                }

                // 6. Inject Mobile List
                const mobileContainer = document.getElementById('inventory-mobile-list');
                if (mobileContainer && data.mobile) {
                    const parser = new DOMParser();
                    const mobileDoc = parser.parseFromString(data.mobile, 'text/html');
                    const newMobile = mobileDoc.getElementById('inventory-mobile-list');
                    if (newMobile) {
                        mobileContainer.innerHTML = newMobile.innerHTML;
                    }
                }

                // 7. Inject Pagination
                if (data.pagination) {
                    const desktopPaginationContainer = document.querySelector('.inventory-pagination-desktop');
                    if (desktopPaginationContainer) {
                        desktopPaginationContainer.innerHTML = data.pagination;
                    }
                    const mobilePaginationContainer = document.querySelector('.inventory-pagination-mobile');
                    if (mobilePaginationContainer) {
                        mobilePaginationContainer.innerHTML = data.pagination;
                    }
                }

                // 8. Re-attach Pagination Listeners
                attachPaginationListeners();

                // 9. Re-initialize Bulk Actions
                if (window.resetBulkActions) {
                    window.resetBulkActions();
                } else {
                    const desktopSelectAll = document.getElementById('select-all');
                    if (desktopSelectAll) desktopSelectAll.checked = false;
                }
            })
            .catch(error => {
                console.error('Error fetching data:', error);
            })
            .finally(() => {
                // 10. Hide Skeleton
                setTimeout(() => {
                    if (skeletonBody) {
                        skeletonBody.classList.add('hidden');
                    }
                    const freshBody = document.getElementById('inventory-desktop-body');
                    if (freshBody) freshBody.classList.remove('hidden');
                }, 300);
            });

    }

    // Function to attach listeners to dynamic pagination links
    function attachPaginationListeners() {
        const paginationLinks = document.querySelectorAll('.inventory-pagination-desktop a, .inventory-pagination-mobile a, .pagination a, .page-link');
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

    // --- Bulk Action Logic (Event Delegation) ---
    function updateBulkActionBar() {
        const bulkActionBar = document.getElementById('bulk-action-bar');
        const selectedCountSpan = document.getElementById('selected-count');
        const bulkRestoreInputs = document.getElementById('bulk-restore-inputs');
        const bulkDeleteInputs = document.getElementById('bulk-delete-inputs');

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
                bulkActionBar.classList.remove('translate-y-24', 'opacity-0');
                bulkActionBar.classList.add('translate-y-0', 'opacity-100');
            } else {
                bulkActionBar.classList.add('translate-y-24', 'opacity-0');
                bulkActionBar.classList.remove('translate-y-0', 'opacity-100');
            }
        }
    }

    // Event Delegation for Checkboxes
    document.addEventListener('change', function (e) {
        // Desktop Select All
        if (e.target.id === 'select-all') {
            const isChecked = e.target.checked;
            document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = isChecked);
            const mobileSelect = document.getElementById('mobile-select-all');
            if (mobileSelect) mobileSelect.checked = isChecked;
            updateBulkActionBar();
        }

        // Mobile Select All
        if (e.target.id === 'mobile-select-all') {
            const isChecked = e.target.checked;
            document.querySelectorAll('.bulk-checkbox').forEach(cb => cb.checked = isChecked);
            const desktopSelect = document.getElementById('select-all');
            if (desktopSelect) desktopSelect.checked = isChecked;
            updateBulkActionBar();
        }

        // Individual Checkbox
        if (e.target.classList.contains('bulk-checkbox')) {
            updateBulkActionBar();

            // Sync Select All Checkboxes
            const allCheckboxes = document.querySelectorAll('.bulk-checkbox');
            const allChecked = allCheckboxes.length > 0 && Array.from(allCheckboxes).every(c => c.checked);

            const desktopSelect = document.getElementById('select-all');
            const mobileSelect = document.getElementById('mobile-select-all');

            if (desktopSelect) desktopSelect.checked = allChecked;
            if (mobileSelect) mobileSelect.checked = allChecked;
        }
    });

    // Helper to reset bulk actions after fetch
    window.resetBulkActions = function () {
        const desktopSelect = document.getElementById('select-all');
        const mobileSelect = document.getElementById('mobile-select-all');
        if (desktopSelect) desktopSelect.checked = false;
        if (mobileSelect) mobileSelect.checked = false;
        updateBulkActionBar();
    };

    // Initial check on load (in case browser preserved state)
    updateBulkActionBar();
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

window.submitInventoryBulkPrint = function () {
    const selected = document.querySelectorAll('.bulk-checkbox:checked');
    if (selected.length === 0) return;

    const bulkActionBar = document.getElementById('bulk-action-bar');
    const route = bulkActionBar ? bulkActionBar.getAttribute('data-bulk-print-route') : '/inventory/qr-code/bulk-print';

    const ids = Array.from(selected).map(cb => cb.value);
    const url = new URL(route, window.location.origin);
    
    // Append IDs as query params
    ids.forEach(id => url.searchParams.append('ids[]', id));

    window.open(url.toString(), '_blank');
};

window.submitInventoryBulkDestroy = function () {
    const selected = document.querySelectorAll('.bulk-checkbox:checked');
    if (selected.length === 0) return;

    const bulkActionBar = document.getElementById('bulk-action-bar');
    const route = bulkActionBar ? bulkActionBar.getAttribute('data-bulk-destroy-route') : '/inventory/bulk-destroy';

    Swal.fire({
        title: 'Hapus Massal?',
        text: `${selected.length} item akan dipindahkan ke tempat sampah.`,
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
            const ids = Array.from(selected).map(cb => cb.value);
            
            fetch(route, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify({ ids: ids })
            })
            .then(response => response.json())
            .then(data => {
                Swal.fire({
                    icon: 'success',
                    title: 'Berhasil',
                    text: data.message,
                    timer: 2000,
                    showConfirmButton: false,
                    customClass: {
                        popup: '!rounded-2xl !font-sans',
                        title: '!text-secondary-900 !text-xl !font-bold',
                        htmlContainer: '!text-secondary-500 !text-sm',
                    },
                    width: '24em',
                    padding: '2em',
                    iconColor: '#10b981',
                    backdrop: `rgba(0,0,0,0.4)`
                }).then(() => {
                    window.location.reload();
                });
            })
            .catch(error => {
                console.error('Error:', error);
                Swal.fire({
                    icon: 'error',
                    title: 'Kesalahan',
                    text: 'Terjadi kesalahan saat menghapus data.',
                    customClass: {
                        popup: '!rounded-2xl !font-sans',
                        title: '!text-secondary-900 !text-xl !font-bold',
                        htmlContainer: '!text-secondary-500 !text-sm',
                        confirmButton: 'btn btn-danger px-6 py-2.5 rounded-lg shadow-md transform hover:scale-105 transition-transform duration-200 ring-2 ring-offset-2 ring-danger-500',
                    },
                    buttonsStyling: false,
                    width: '24em',
                    padding: '2em',
                    iconColor: '#ef4444',
                    backdrop: `rgba(0,0,0,0.4)`
                });
            });
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

/**
 * confirmDelete — Soft-delete dengan undo countdown 5 detik.
 * Item baris disembunyikan langsung, lalu toast tampil dengan tombol "Batalkan".
 * Jika dibatalkan → baris muncul kembali, form TIDAK dikirim.
 * Jika 5 detik berlalu → form di-submit ke server (soft-delete).
 */
window.confirmDelete = function (event) {
    event.preventDefault();
    const form = event.target.closest('form');
    if (!form) return;

    // Optimistic hide row
    const row = form.closest('tr') || form.closest('[data-row]');
    if (row) {
        row.style.transition = 'all 0.4s cubic-bezier(0.4, 0, 0.2, 1)';
        row.style.opacity = '0.3';
        row.style.filter = 'grayscale(100%) blur(1px)';
    }

    let undoClicked = false;
    let submitTimer = null;
    const seconds = 5;

    const UndoToast = Swal.mixin({
        toast: true,
        position: 'bottom-end',
        showConfirmButton: true,
        confirmButtonText: 'URUNGKAN',
        showCancelButton: false,
        timer: seconds * 1000,
        timerProgressBar: true,
        customClass: {
            popup: 'undo-toast-popup animated slideInRight',
            title: 'undo-toast-title',
            htmlContainer: 'undo-toast-content',
            confirmButton: 'undo-toast-undo-btn',
            timerProgressBar: 'undo-toast-progress-bar'
        },
        buttonsStyling: false,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        },
        willClose: () => {
            if (!undoClicked) {
                submitTimer = setTimeout(() => form.submit(), 50);
            }
        }
    });

    UndoToast.fire({
        icon: 'warning',
        html: `
            <div style="display:flex; flex-direction:column; gap:1px;">
                <span style="font-size:14px; font-weight:700; color:#0f172a;">Item Terhapus</span>
                <span style="font-size:12px; color:#475569;">Urungkan dalam <b id="undo-countdown">${seconds}</b> detik?</span>
            </div>
        `,
        didOpen: (toast) => {
            const countdownEl = toast.querySelector('#undo-countdown');
            let timeLeft = seconds;
            const interval = setInterval(() => {
                if (Swal.isPaused()) return;
                timeLeft--;
                if (countdownEl) countdownEl.textContent = timeLeft;
                if (timeLeft <= 0) clearInterval(interval);
            }, 1000);
            
            // Standard didOpen logic for timer
            toast.addEventListener('mouseenter', () => Swal.stopTimer());
            toast.addEventListener('mouseleave', () => Swal.resumeTimer());
        }
    }).then((result) => {
        if (result.isConfirmed) {
            undoClicked = true;
            clearTimeout(submitTimer);
            if (row) {
                row.style.opacity = '1';
                row.style.filter = 'none';
            }
            if (window.showToast) {
                window.showToast('success', 'Penghapusan dibatalkan.');
            }
        }
    });
};
