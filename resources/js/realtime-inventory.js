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

    Echo.channel('inventory-updates')
        // Event: InventoryUpdated (barang dibuat/diupdate/dihapus)
        .listen('.InventoryUpdated', (e) => {
            console.log('📦 Inventory Updated:', e);

            // Filter out self-notifications to prevent double-toast
            if (window.currentUser && window.currentUser.name && e.user_name === window.currentUser.name) {
                console.log('🚫 Mengabaikan notifikasi realtime dari aksi sendiri (InventoryUpdated).');
                refreshDashboardData();
                return;
            }

            // Show toast notification ke semua user.
            showInventoryToast(e.message, e.action);

            // Refresh dashboard
            refreshDashboardData();

            // Auto-refresh inventory list jika sedang di halaman inventory.
            if (window.location.pathname.includes('/inventory')) {
                setTimeout(() => {
                    location.reload();
                }, 2000); // Delay 2 detik agar user baca toast dulu.
            }
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

            // Tentukan icon dan title berdasarkan severity.
            const config = getSeverityConfig(e.severity);

            // Show SweetAlert dengan detail stock.
            Swal.fire({
                icon: config.icon,
                title: config.title,
                html: `
                    <div class="text-left">
                        <p class="font-semibold text-lg mb-2">${e.name}</p>
                        <p class="text-sm text-gray-600 mb-3">Part Number: ${e.part_number}</p>
                        <div class="bg-gray-50 p-3 rounded">
                            <p class="text-sm">Stok Saat Ini: <strong class="text-danger-600">${e.current_stock}</strong></p>
                            <p class="text-sm">Minimum Stock: <strong>${e.min_stock}</strong></p>
                            <p class="text-sm">Persentase: <strong>${e.percentage}%</strong></p>
                        </div>
                    </div>
                `,
                showConfirmButton: true,
                confirmButtonText: 'Lihat Detail',
                confirmButtonColor: '#dc2626',
                showCancelButton: true,
                cancelButtonText: 'Tutup',
                timer: 10000,
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
