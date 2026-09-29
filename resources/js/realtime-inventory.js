/**
 * Real-time Inventory Updates Handler
 * 
 * Script ini menangani real-time updates untuk inventory via Laravel Echo.
 * Listener untuk:
 * - InventoryUpdated: Create/update/delete barang
 * - StockCritical: Alert stok menipis (<50% minimum)
 * - BorrowingStatusChanged: Approval/reject peminjaman
 */

// Jalankan setelah DOM ready.
document.addEventListener('DOMContentLoaded', function () {
    if (!window.Echo) {
        console.warn('Laravel Echo tidak tersedia. Real-time updates disabled.');
        return;
    }

    // =================================================================
    // INVENTORY UPDATES CHANNEL - Public Channel
    // =================================================================

    // Function untuk refresh data dashboard jika ada di halaman dashboard
    const refreshDashboardData = () => {
        const isDashboard = window.location.pathname.includes('/dashboard');
        if (isDashboard) {
            console.log('🔄 Dashboard detected. Fetching fresh data...');

            fetch(window.location.href, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    console.log('📊 Dashboard data fetched. Dispatching refresh event...');

                    // 1. Dispatch event untuk Alpine.js
                    window.dispatchEvent(new CustomEvent('dashboard-refresh', {
                        detail: data
                    }));

                    // 2. Transisi Highlight (Realtime Feedback)
                    setTimeout(() => {
                        const highlightTargets = document.querySelectorAll(
                            '.stat-card, [data-rt-highlight], #stockMovementChart, .inventory-table tr:not(:first-child)'
                        );

                        highlightTargets.forEach(el => {
                            // Hapus class jika sudah ada agar animasi reset
                            el.classList.remove('animate-rt-highlight');
                            // Force reflow
                            void el.offsetWidth;
                            el.classList.add('animate-rt-highlight');
                        });
                    }, 100);

                    // 3. Fallback untuk chart non-Alpine (jika ada)
                    if (typeof window.updateDashboardCharts === 'function') {
                        window.updateDashboardCharts(data.movementData, data.stockByCategory, data.stockByLocation, data);
                    }
                })
                .catch(error => console.error('❌ Error refreshing dashboard:', error));
        }
    };

    // Function untuk refresh data list inventaris secara parsial (Tanpa Reload)
    const refreshInventoryListData = () => {
        const isInventoryPage = window.location.pathname.includes('/inventory') && !window.location.pathname.includes('/inventory/');
        
        if (isInventoryPage) {
            console.log('📦 Inventory page detected. Refreshing list partials...');
            
            // Tambahkan param table_only=1 untuk mendapatkan partial view
            const url = new URL(window.location.href);
            url.searchParams.set('table_only', '1');

            fetch(url.toString(), {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
                .then(response => response.json())
                .then(data => {
                    console.log('✅ Inventory list data received. Updating DOM...');
                    
                    // 1. Update Desktop Table Body
                    const desktopBody = document.getElementById('inventory-desktop-body');
                    if (desktopBody && data.desktop) {
                        desktopBody.innerHTML = data.desktop;
                    }

                    // 2. Update Mobile List
                    const mobileList = document.getElementById('inventory-mobile-list');
                    if (mobileList && data.mobile) {
                        // Cari kontainer utama di dalam mobileList agar tidak menimpa checkbox 'Select All' jika ada
                        // atau timpa seluruhnya jika data.mobile mengandung struktur lengkap
                        mobileList.innerHTML = data.mobile;
                    }

                    // 3. Update Pagination
                    const desktopPagination = document.getElementById('inventory-pagination-desktop');
                    if (desktopPagination && data.pagination) {
                        desktopPagination.innerHTML = data.pagination;
                    }
                    
                    const mobilePagination = document.getElementById('inventory-pagination-mobile');
                    if (mobilePagination && data.pagination) {
                        mobilePagination.innerHTML = data.pagination;
                    }

                    // 4. Trigger Highlight Animation
                    setTimeout(() => {
                        const rows = document.querySelectorAll('#inventory-desktop-body tr, #inventory-mobile-list .card');
                        rows.forEach(row => {
                            row.classList.add('animate-rt-highlight');
                        });
                    }, 100);

                    // 5. Re-initialize Tooltips or other UI components if necessary
                    if (window.tippy) {
                        window.tippy('[data-tippy-content]');
                    }
                })
                .catch(error => console.error('❌ Error refreshing inventory list:', error));
        }
    };

    Echo.channel('inventory-updates')
        // Event: InventoryUpdated (barang dibuat/diupdate/dihapus)
        .listen('.InventoryUpdated', (e) => {
            console.log('📦 Inventory Updated:', e);

            // Filter out self-notifications to prevent double-toast
            if (window.currentUser && window.currentUser.name && e.user_name === window.currentUser.name) {
                console.log('🚫 Mengabaikan notifikasi realtime dari aksi sendiri (InventoryUpdated).');
                refreshDashboardData();
                refreshInventoryListData();
                return;
            }

            // Show toast notification ke semua user.
            showInventoryToast(e.message, e.action);

            // Refresh dashboard & list
            refreshDashboardData();
            refreshInventoryListData();
        })

        // Event: BorrowingStatusChanged (peminjaman di-approve/reject/return)
        .listen('.BorrowingStatusChanged', (e) => {
            console.log('🔄 Borrowing Status Changed:', e);

            // Tentukan siapa aktornya berdasarkan status (jika return, aktornya borrower, selain itu admin)
            const actorName = e.new_status === 'returned' ? e.borrower_name : e.admin_name;
            if (window.currentUser && window.currentUser.name && actorName === window.currentUser.name) {
                console.log('🚫 Mengabaikan notifikasi realtime dari aksi sendiri (BorrowingStatusChanged).');
                refreshDashboardData();
                return;
            }

            showInventoryToast(e.message, 'borrowing');

            // Refresh dashboard
            refreshDashboardData();
        });

    // =================================================================
    // ACTIVITY LOGS CHANNEL - Public Channel for Global Sync
    // =================================================================
    Echo.channel('activity-logs')
        .listen('.ActivityLogged', (e) => {
            console.log('📝 Activity Log Received:', e);
            
            // Trigger refresh data di dashboard agar angka-angka (stats) update otomatis
            refreshDashboardData();
        });

    // =================================================================
    // STOCK ALERTS CHANNEL - Public Channel untuk Critical Alerts
    // =================================================================

    Echo.channel('stock-alerts')
        // Event: StockCritical (stock < 50% minimum atau habis)
        .listen('.StockCritical', (e) => {
            console.log('⚠️ Stock Critical:', e);

            // Filter out self-notifications to prevent modal flash before page reload
            if (window.currentUser && window.currentUser.name && e.actor_name === window.currentUser.name) {
                console.log('🚫 Mengabaikan peringatan stok kritis dari aksi sendiri.');
                return;
            }

            // Tentukan icon dan title berdasarkan severity.
            const config = getSeverityConfig(e.severity);

            // Show SweetAlert dengan detail stock.
            Swal.fire({
                icon: config.icon,
                title: config.title,
                html: `
                    <div class="text-left mt-2">
                        <p class="font-bold text-xl text-secondary-900 mb-1">${e.name}</p>
                        <p class="text-sm font-mono text-secondary-500 mb-4">${e.part_number}</p>
                        <div class="bg-secondary-50 border border-secondary-100 p-4 rounded-xl space-y-2">
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Stok Saat Ini</span>
                                <strong class="text-lg text-danger-600">${e.current_stock}</strong>
                            </div>
                            <div class="flex justify-between items-center">
                                <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Batas Minimum</span>
                                <strong class="text-sm text-secondary-900">${e.min_stock}</strong>
                            </div>
                            <div class="flex justify-between items-center pt-2 border-t border-secondary-200/60">
                                <span class="text-xs font-bold text-secondary-400 uppercase tracking-wider">Sisa Kapasitas</span>
                                <strong class="text-sm ${e.percentage <= 25 ? 'text-danger-600' : 'text-warning-600'}">${e.percentage}%</strong>
                            </div>
                        </div>
                    </div>
                `,
                customClass: {
                    popup: '!rounded-3xl !font-sans !shadow-2xl border border-secondary-100',
                    title: '!text-secondary-900 !text-2xl !font-bold !mt-4',
                    htmlContainer: '!m-0 !px-6 !pb-2',
                    confirmButton: 'btn btn-danger px-5 py-2.5 rounded-xl shadow-md transform hover:scale-105 transition-all duration-200 ml-3',
                    cancelButton: 'btn btn-secondary px-5 py-2.5 rounded-xl bg-white border border-secondary-200 text-secondary-600 hover:bg-secondary-50 shadow-sm transition-all duration-200'
                },
                buttonsStyling: false,
                width: '28em',
                padding: '2em',
                backdrop: 'rgba(15, 23, 42, 0.5)',
                showConfirmButton: true,
                confirmButtonText: 'Lihat Detail',
                showCancelButton: true,
                cancelButtonText: 'Tutup',
                reverseButtons: true,
                timer: 15000,
                timerProgressBar: true,
            }).then((result) => {
                if (result.isConfirmed && e.url) {
                    window.location.href = e.url;
                }
            });
        });
});

// =================================================================
// HELPER FUNCTIONS
// =================================================================

/**
 * Show toast notification untuk inventory updates.
 * 
 * @param {string} message - Pesan yang ditampilkan
 * @param {string} action - Tipe action (created/updated/deleted/borrowing)
 */
function showInventoryToast(message, action) {
    // Gunakan Native Alpine Toast dari global helper di custom.js
    if (typeof window.showToast === 'function') {
        window.showToast(action, message);
    } else {
        // Fallback native event
        window.dispatchEvent(new CustomEvent('notify', {
            detail: { type: action, message: message }
        }));
    }
}

/**
 * Get config untuk severity level stock.
 * 
 * @param {string} severity - Level severity (critical/warning/depleted)
 * @return {object} Config object dengan icon dan title
 */
function getSeverityConfig(severity) {
    switch (severity) {
        case 'depleted':
            return {
                icon: 'error',
                title: '🚨 STOK HABIS!'
            };
        case 'critical':
            return {
                icon: 'warning',
                title: '⚠️ STOK KRITIS!'
            };
        default:
            return {
                icon: 'warning',
                title: '⚠️ PERINGATAN STOK'
            };
    }
}
