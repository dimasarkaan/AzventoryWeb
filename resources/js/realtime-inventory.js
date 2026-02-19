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

    Echo.channel('inventory-updates')
        // Event: InventoryUpdated (barang dibuat/diupdate/dihapus)
        .listen('InventoryUpdated', (e) => {
            console.log('📦 Inventory Updated:', e);

            // Show toast notification ke semua user.
            showInventoryToast(e.message, e.action);

            // Auto-refresh inventory list jika sedang di halaman inventory.
            if (window.location.pathname.includes('/inventory')) {
                setTimeout(() => {
                    location.reload();
                }, 2000); // Delay 2 detik agar user baca toast dulu.
            }
        })

        // Event: BorrowingStatusChanged (peminjaman di-approve/reject/return)
        .listen('BorrowingStatusChanged', (e) => {
            console.log('🔄 Borrowing Status Changed:', e);

            showInventoryToast(e.message, 'borrowing');
        });

    // =================================================================
    // STOCK ALERTS CHANNEL - Public Channel untuk Critical Alerts
    // =================================================================

    Echo.channel('stock-alerts')
        // Event: StockCritical (stock < 50% minimum atau habis)
        .listen('StockCritical', (e) => {
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
    const Toast = Swal.mixin({
        toast: true,
        position: 'top-end',
        showConfirmButton: false,
        timer: 4000,
        timerProgressBar: true,
        didOpen: (toast) => {
            toast.addEventListener('mouseenter', Swal.stopTimer);
            toast.addEventListener('mouseleave', Swal.resumeTimer);
        }
    });

    // Tentukan icon berdasarkan action.
    const getIcon = (action) => {
        switch (action) {
            case 'created': return 'success';
            case 'updated': return 'info';
            case 'deleted': return 'warning';
            case 'borrowing': return 'info';
            default: return 'info';
        }
    };

    Toast.fire({
        icon: getIcon(action),
        title: message
    });
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
