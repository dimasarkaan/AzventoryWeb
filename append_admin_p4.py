part4 = r"""
    @push('scripts')
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // Default Chart
        Chart.defaults.font.family = "'Inter', sans-serif";
        Chart.defaults.color = '#64748b';
        Chart.defaults.scale.grid.color = '#f1f5f9';

        // =====================================================================
        // Helper: Update KPI Summary Badges dari data movement
        // =====================================================================
        function updateMovementKPI(movementData) {
            const totalMasuk = (movementData.masuk || []).reduce((a, b) => a + b, 0);
            const totalKeluar = (movementData.keluar || []).reduce((a, b) => a + b, 0);
            const net = totalMasuk - totalKeluar;

            const elMasuk = document.getElementById('kpi-masuk');
            const elKeluar = document.getElementById('kpi-keluar');
            const elNet = document.getElementById('kpi-net');
            const elNetDot = document.getElementById('kpi-net-dot');
            const elNetLabel = document.getElementById('kpi-net-label');
            const elNetBadge = document.getElementById('kpi-net-badge');

            if (elMasuk) elMasuk.textContent = totalMasuk.toLocaleString('id-ID');
            if (elKeluar) elKeluar.textContent = totalKeluar.toLocaleString('id-ID');
            if (elNet && elNetDot && elNetLabel && elNetBadge) {
                const prefix = net >= 0 ? '+' : '';
                elNet.textContent = prefix + net.toLocaleString('id-ID');
                if (net > 0) {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-emerald-50 border border-emerald-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-emerald-500 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-emerald-700 font-medium whitespace-nowrap';
                } else if (net < 0) {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-red-50 border border-red-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-red-500 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-red-700 font-medium whitespace-nowrap';
                } else {
                    elNetBadge.className = 'flex items-center gap-1.5 bg-blue-50 border border-blue-200 rounded-lg px-3 py-1.5';
                    elNetDot.className = 'w-2 h-2 rounded-full bg-blue-400 flex-shrink-0';
                    elNetLabel.className = 'text-xs text-blue-700 font-medium whitespace-nowrap';
                }
            }
        }

        function exportDashboardPDF() {
            document.title = 'Dashboard Admin Azventory - ' + new Date().toLocaleDateString('id-ID');
            window.print();
        }

        function exportDashboardPNG() {
            const toast = document.createElement('div');
            toast.className = 'fixed bottom-6 right-6 z-[9999] bg-secondary-900 text-white text-sm px-4 py-3 rounded-xl shadow-xl flex items-center gap-2';
            toast.innerHTML = '<svg class="w-4 h-4 animate-spin" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"></path></svg> Menyiapkan gambar...';
            document.body.appendChild(toast);
            if (typeof html2canvas === 'undefined') {
                const s = document.createElement('script');
                s.src = 'https://cdn.jsdelivr.net/npm/html2canvas@1.4.1/dist/html2canvas.min.js';
                s.onload = () => doCapture(toast);
                document.head.appendChild(s);
            } else {
                doCapture(toast);
            }
        }

        function doCapture(toastEl) {
            const target = document.querySelector('[x-data]') || document.body;
            html2canvas(target, { scale: 1.5, useCORS: true, backgroundColor: '#f8fafc', logging: false })
                .then(canvas => {
                    const link = document.createElement('a');
                    link.download = `dashboard-admin-azventory-${new Date().toISOString().slice(0,10)}.png`;
                    link.href = canvas.toDataURL('image/png');
                    link.click();
                    if (toastEl) toastEl.remove();
                }).catch(() => {
                    if (toastEl) toastEl.remove();
                    alert('Gagal mengambil screenshot. Gunakan opsi Cetak/PDF.');
                });
        }

        const movementDataKey = @json($movementData);
        updateMovementKPI(movementDataKey);

        function makeGradient(ctx, color1, color2) {
            const gradient = ctx.createLinearGradient(0, 0, 0, 300);
            gradient.addColorStop(0, color1);
            gradient.addColorStop(1, color2);
            return gradient;
        }

        // =====================================================================
        // Chart Pergerakan Stok (Line Chart)
        // =====================================================================
        const movCtx = document.getElementById('stockMovementChart').getContext('2d');
        const gradMasuk = makeGradient(movCtx, 'rgba(16,185,129,0.85)', 'rgba(16,185,129,0.15)');
        const gradKeluar = makeGradient(movCtx, 'rgba(239,68,68,0.85)', 'rgba(239,68,68,0.15)');

        const movLabels = movementDataKey.labels.length > 0 ? movementDataKey.labels : ['Tidak ada data'];
        const movMasuk  = movementDataKey.masuk.length  > 0 ? movementDataKey.masuk  : [0];
        const movKeluar = movementDataKey.keluar.length > 0 ? movementDataKey.keluar : [0];

        let movementChart = new Chart(movCtx, {
            type: 'line',
            data: {
                labels: movLabels,
                datasets: [
                    {
                        label: '📦 Barang Masuk',
                        data: movMasuk,
                        backgroundColor: gradMasuk,
                        borderColor: '#10b981',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#10b981',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    },
                    {
                        label: '📤 Barang Keluar',
                        data: movKeluar,
                        backgroundColor: gradKeluar,
                        borderColor: '#ef4444',
                        borderWidth: 3,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 0,
                        pointHoverRadius: 6,
                        pointBackgroundColor: '#ef4444',
                        pointBorderColor: '#fff',
                        pointBorderWidth: 2,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: {
                        position: 'top',
                        labels: { usePointStyle: true, pointStyle: 'rectRounded', padding: 16, font: { size: 12, weight: '500' } }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            title(ctx) { return '🗓 ' + ctx[0].label; },
                            label(ctx) { return `  ${ctx.dataset.label}: ${ctx.parsed.y.toLocaleString('id-ID')} unit`; },
                            afterBody(ctx) {
                                if (ctx.length < 2) return '';
                                const masuk  = ctx.find(c => c.datasetIndex === 0)?.parsed.y ?? 0;
                                const keluar = ctx.find(c => c.datasetIndex === 1)?.parsed.y ?? 0;
                                const net = masuk - keluar;
                                return [`  ─────────────────`, `  🔄 Net Stok: ${net >= 0 ? '+' : ''}${net.toLocaleString('id-ID')} unit`];
                            }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: { color: 'rgba(148,163,184,0.15)', borderDash: [4,4] },
                        ticks: { callback: val => val.toLocaleString('id-ID') }
                    },
                    x: { grid: { display: false }, ticks: { maxRotation: 45, autoSkipPadding: 8 } }
                }
            }
        });

        // =====================================================================
        // Chart Donut: Stok berdasarkan Kategori
        // =====================================================================
        const stockByCategoryData = @json($stockByCategory);
        const catCtx = document.getElementById('stockByCategoryChart').getContext('2d');
        const baseColors = ['#3b82f6','#8b5cf6','#ec4899','#06b6d4','#6366f1','#14b8a6'];
        const chartColors = baseColors.map(c => {
            const grd = catCtx.createLinearGradient(0, 0, 0, 300);
            grd.addColorStop(0, c);
            grd.addColorStop(1, c + '90');
            return grd;
        });
        const catTotal = Object.values(stockByCategoryData).reduce((a, b) => a + b, 0);
        const isSmallScreen = window.innerWidth < 640;

        const outerDashedRing = {
            id: 'outerDashedRing',
            beforeDraw(chart) {
                const {ctx, chartArea: {top, bottom, left, right}} = chart;
                const centerX = (left + right) / 2;
                const centerY = (top + bottom) / 2;
                const meta = chart.getDatasetMeta(0);
                if (meta.data.length > 0) {
                    const ringRadius = meta.data[0].outerRadius + 15;
                    ctx.save();
                    ctx.beginPath();
                    ctx.arc(centerX, centerY, ringRadius, 0, 2 * Math.PI);
                    ctx.lineWidth = 2;
                    ctx.strokeStyle = '#e0e7ff';
                    ctx.setLineDash([6, 6]);
                    ctx.stroke();
                    ctx.restore();
                }
            }
        };

        let stockCategoryChart = new Chart(document.getElementById('stockByCategoryChart'), {
            type: 'doughnut',
            data: {
                labels: Object.keys(stockByCategoryData),
                datasets: [{ label: 'Total Stok', data: Object.values(stockByCategoryData), backgroundColor: chartColors, borderColor: '#ffffff', borderWidth: 0, hoverOffset: 8 }]
            },
            plugins: [outerDashedRing],
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                layout: { padding: 20 },
                elements: { arc: { borderWidth: 0, borderColor: '#ffffff', borderRadius: 5, hoverOffset: 10 } },
                plugins: {
                    legend: {
                        position: isSmallScreen ? 'bottom' : 'right',
                        labels: { usePointStyle: true, pointStyle: 'circle', padding: 20, font: { size: 12 }, boxWidth: 8 }
                    },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: {
                            label(ctx) {
                                const val = ctx.parsed;
                                const pct = catTotal > 0 ? ((val / catTotal) * 100).toFixed(1) : 0;
                                return `  ${ctx.label}: ${val.toLocaleString('id-ID')} unit (${pct}%)`;
                            }
                        }
                    }
                }
            }
        });

        // =====================================================================
        // Chart Bar: Stok berdasarkan Lokasi
        // =====================================================================
        const stockByLocationData = @json($stockByLocation);
        const locCtx = document.getElementById('stockByLocationChart').getContext('2d');
        const gradLoc = locCtx.createLinearGradient(0, 0, 0, 280);
        gradLoc.addColorStop(0, 'rgba(59,130,246,0.9)');
        gradLoc.addColorStop(1, 'rgba(59,130,246,0.2)');

        let stockLocationChart = new Chart(locCtx, {
            type: 'bar',
            data: {
                labels: Object.keys(stockByLocationData),
                datasets: [{ label: 'Total Stok', data: Object.values(stockByLocationData), backgroundColor: gradLoc, borderColor: '#3b82f6', borderWidth: 1.5, borderRadius: 6, borderSkipped: false, barPercentage: 0.65 }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                interaction: { mode: 'index', intersect: false },
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(15,23,42,0.92)',
                        titleColor: '#e2e8f0',
                        bodyColor: '#cbd5e1',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        cornerRadius: 8,
                        callbacks: { label(ctx) { return `  📦 Stok: ${ctx.parsed.y.toLocaleString('id-ID')} unit`; } }
                    }
                },
                scales: {
                    y: { beginAtZero: true, grid: { color: 'rgba(148,163,184,0.15)', borderDash: [4,4] }, ticks: { callback: val => val.toLocaleString('id-ID') } },
                    x: { grid: { display: false }, ticks: { maxRotation: 35, autoSkipPadding: 6 } }
                }
            }
        });

        // =====================================================================
        // Global Function untuk Update Chart (Real-time Safe)
        // =====================================================================
        window.updateDashboardCharts = function(movementData, stockByCategory, stockByLocation) {
            if (movementData && movementChart) {
                movementChart.data.labels = movementData.labels.length > 0 ? movementData.labels : ['Tidak ada data'];
                movementChart.data.datasets[0].data = movementData.masuk.length  > 0 ? movementData.masuk  : [0];
                movementChart.data.datasets[1].data = movementData.keluar.length > 0 ? movementData.keluar : [0];
                movementChart.update();
                updateMovementKPI(movementData);
            }
            if (stockByCategory && stockCategoryChart) {
                stockCategoryChart.data.labels = Object.keys(stockByCategory);
                stockCategoryChart.data.datasets[0].data = Object.values(stockByCategory);
                stockCategoryChart.update();
            }
            if (stockByLocation && stockLocationChart) {
                stockLocationChart.data.labels = Object.keys(stockByLocation);
                stockLocationChart.data.datasets[0].data = Object.values(stockByLocation);
                stockLocationChart.update();
            }
        };

        // =====================================================================
        // Alpine Component: dashboardData()
        // =====================================================================
        function dashboardData() {
            return {
                // Widget visibility — persisted di localStorage
                showStats:       localStorage.getItem('admin_dashboard_showStats')       !== 'false',
                showCharts:      localStorage.getItem('admin_dashboard_showCharts')      !== 'false',
                showMovement:    localStorage.getItem('admin_dashboard_showMovement')    !== 'false',
                showTopItems:    localStorage.getItem('admin_dashboard_showTopItems')    !== 'false',
                showLowStock:    localStorage.getItem('admin_dashboard_showLowStock')    !== 'false',
                showRecent:      localStorage.getItem('admin_dashboard_showRecent')      !== 'false',
                showDeadStock:   localStorage.getItem('admin_dashboard_showDeadStock')   !== 'false',
                showLeaderboard: localStorage.getItem('admin_dashboard_showLeaderboard') !== 'false',
                showBorrowings:  localStorage.getItem('admin_dashboard_showBorrowings')  !== 'false',
                showOverdue:     localStorage.getItem('admin_dashboard_showOverdue')     !== 'false',

                isLoading: true,

                // Data Statis
                totalSpareparts: @json($totalSpareparts),
                totalStock:      @json($totalStock),
                totalCategories: @json($totalCategories),
                totalLocations:  @json($totalLocations),
                pendingApprovalsCount: @json($pendingApprovalsCount),
                activeBorrowingsCount: @json($activeBorrowingsCount),

                // Arrays untuk x-for
                recentActivities:      @json($recentActivities),
                topExited:             @json($topExited),
                topEntered:            @json($topEntered),
                deadStockItems:        @json($deadStockItems),
                activeUsers:           @json($activeUsers),
                activeBorrowingsList:  @json($activeBorrowingsList),
                overdueBorrowingsList: @json($overdueBorrowingsList),
                lowStockItems:         @json($lowStockItems),

                // Charts Data
                movementData:    @json($movementData),
                stockByCategory: @json($stockByCategory),
                stockByLocation: @json($stockByLocation),

                init() {
                    setTimeout(() => { this.isLoading = false; }, 800);

                    // Real-time listener (jika Echo tersedia)
                    if (window.Echo) {
                        window.Echo.channel('inventory-updates')
                            .listen('.InventoryUpdated', (e) => {
                                this.refreshData();
                                if (window.showToast) window.showToast('info', e.message);
                            });
                    }
                },

                async refreshData() {
                    try {
                        const response = await fetch('{{ route("dashboard.admin") }}', {
                            headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                        });
                        if (!response.ok) throw new Error('Network response was not ok');
                        const data = await response.json();

                        // Update single values
                        this.totalSpareparts       = data.totalSpareparts;
                        this.totalStock            = data.totalStock;
                        this.totalCategories       = data.totalCategories;
                        this.totalLocations        = data.totalLocations;
                        this.pendingApprovalsCount = data.pendingApprovalsCount;
                        this.activeBorrowingsCount = data.activeBorrowingsCount;

                        // Update lists
                        this.recentActivities      = data.recentActivities;
                        this.topExited             = data.topExited;
                        this.topEntered            = data.topEntered;
                        this.deadStockItems        = data.deadStockItems;
                        this.activeUsers           = data.activeUsers;
                        this.activeBorrowingsList  = data.activeBorrowingsList;
                        this.overdueBorrowingsList = data.overdueBorrowingsList;
                        this.lowStockItems         = data.lowStockItems;

                        if (window.updateDashboardCharts) {
                            window.updateDashboardCharts(data.movementData, data.stockByCategory, data.stockByLocation);
                        }
                    } catch (error) {
                        console.error('Failed to refresh admin dashboard data:', error);
                    }
                },

                toggle(key) {
                    this[key] = !this[key];
                    localStorage.setItem('admin_dashboard_' + key, this[key]);
                }
            };
        }

        // =====================================================================
        // Alpine Component: Tab Period Global
        // =====================================================================
        function globalPeriodFilter() {
            return {
                showCustom: {{ in_array($period ?? 'today', ['custom','custom_year']) ? 'true' : 'false' }},
            };
        }

        // =====================================================================
        // Quick-filter per-widget Pergerakan Stok
        // =====================================================================
        let movementActiveRange = 7;

        async function fetchMovementData(range) {
            movementActiveRange = range;

            document.querySelectorAll('.mov-range-btn').forEach(btn => {
                btn.classList.remove('bg-white', 'shadow-sm', 'text-primary-700');
                btn.classList.add('text-secondary-600');
            });
            const activeBtn = document.getElementById('mov-btn-' + range);
            if (activeBtn) {
                activeBtn.classList.add('bg-white', 'shadow-sm', 'text-primary-700');
                activeBtn.classList.remove('text-secondary-600');
            }

            const canvas = document.getElementById('stockMovementChart');
            if (canvas) canvas.style.opacity = '0.5';

            try {
                const response = await fetch('{{ route("dashboard.admin.movement-data") }}?range=' + range, {
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
                    }
                });
                if (!response.ok) throw new Error('Gagal memuat data movement');
                const data = await response.json();

                if (movementChart) {
                    movementChart.data.labels = (data.labels  || []).length > 0 ? data.labels  : ['Tidak ada data'];
                    movementChart.data.datasets[0].data = (data.masuk   || []).length > 0 ? data.masuk   : [0];
                    movementChart.data.datasets[1].data = (data.keluar  || []).length > 0 ? data.keluar  : [0];
                    movementChart.update('active');
                }
                updateMovementKPI(data);
            } catch (err) {
                console.error('fetchMovementData error:', err);
            } finally {
                if (canvas) canvas.style.opacity = '1';
            }
        }
    </script>
    @endpush

</x-app-layout>
"""

with open('resources/views/dashboard/admin.blade.php', 'a', encoding='utf-8') as f:
    f.write(part4)

print('Part 4 OK')
