@push('scripts')
<script>
    (function () {
        const STORAGE_KEY = 'dashboard_period';
        const params = new URLSearchParams(window.location.search);

        // Jika URL tidak punya ?period=, coba restore dari sessionStorage
        if (!params.has('period')) {
            const saved = sessionStorage.getItem(STORAGE_KEY);
            if (saved && saved !== 'today') {
                // Redirect ke URL yang sama + ?period=saved
                params.set('period', saved);
                window.location.replace(window.location.pathname + '?' + params.toString());
            }
        } else {
            // Simpan periode saat ini ke sessionStorage
            sessionStorage.setItem(STORAGE_KEY, params.get('period'));
        }

        // Fungsi global untuk dipanggil saat klik tab
        window.savePeriod = function (key) {
            sessionStorage.setItem(STORAGE_KEY, key);
        };

        // Fungsi global untuk dipakai tombol logout
        window.clearDashboardPeriod = function () {
            sessionStorage.removeItem(STORAGE_KEY);
        };
    })();
</script>

<script>
    function dashboardData() {
        const userSettings = @json(auth()->user()->settings ?? []);
        
        const parseBool = (val, defaultVal) => {
            if (val === undefined || val === null) return defaultVal;
            return val === true || String(val).toLowerCase() === 'true' || String(val) === '1';
        };

        return {
            showStats: parseBool(userSettings.showStats, localStorage.getItem('dashboard_{{ auth()->id() }}_showStats') !== 'false'),
            showCharts: parseBool(userSettings.showCharts, localStorage.getItem('dashboard_{{ auth()->id() }}_showCharts') !== 'false'),
            showLowStock: parseBool(userSettings.showLowStock, localStorage.getItem('dashboard_{{ auth()->id() }}_showLowStock') !== 'false'),
            showOverdue: parseBool(userSettings.showOverdue, localStorage.getItem('dashboard_{{ auth()->id() }}_showOverdue') !== 'false'),
            showNoPriceItems: parseBool(userSettings.showNoPriceItems, localStorage.getItem('dashboard_{{ auth()->id() }}_showNoPriceItems') !== 'false'),
            showMovement: parseBool(userSettings.showMovement, localStorage.getItem('dashboard_{{ auth()->id() }}_showMovement') !== 'false'),
            showRecent: parseBool(userSettings.showRecent, localStorage.getItem('dashboard_{{ auth()->id() }}_showRecent') !== 'false'),
            showTopItems: parseBool(userSettings.showTopItems, localStorage.getItem('dashboard_{{ auth()->id() }}_showTopItems') === 'true'),
            showDeadStock: parseBool(userSettings.showDeadStock, localStorage.getItem('dashboard_{{ auth()->id() }}_showDeadStock') === 'true'),
            showLeaderboard: parseBool(userSettings.showLeaderboard, localStorage.getItem('dashboard_{{ auth()->id() }}_showLeaderboard') === 'true'),
            
            isLoading: true,
            showActivityModal: false,
            selectedActivity: null,

            // Toast Helper
            showToast(type, message) {
                if (window.Toast) {
                    window.Toast.fire({ icon: type, title: message });
                } else if (window.Swal) {
                    window.Swal.fire({ toast: true, position: 'top-end', icon: type, title: message, showConfirmButton: false, timer: 3000 });
                } else {
                    window.showAlert('Info', message, 'info');
                }
            },

            async fetchDashboardData(form) {
                const url = new URL(form.action);
                const formData = new FormData(form);
                const searchParams = new URLSearchParams(formData);
                url.search = searchParams.toString();

                try {
                    const response = await fetch(url, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (response.ok) {
                        const data = await response.json();
                        
                        // Update Alpine state with the fetched JSON
                        this.movementData = data.movementData || {};
                        this.stockByCategory = data.stockByCategory || {};
                        this.stockByLocation = data.stockByLocation || {};
                        
                        this.recentActivities = data.recentActivities || [];
                        this.topExited = data.topExited || [];
                        this.topEntered = data.topEntered || [];
                        this.deadStockItems = data.deadStockItems || [];
                        this.activeUsers = data.activeUsers || [];
                        this.activeBorrowingsList = data.activeBorrowingsList || [];
                        this.overdueBorrowingsList = data.overdueBorrowingsList || [];
                        this.lowStockItems = data.lowStockItems || [];
                        this.noPriceItems = data.noPriceItems || [];

                        // Update charts if global function exists
                        if (window.updateDashboardCharts) {
                            window.updateDashboardCharts(this.movementData, this.stockByCategory, this.stockByLocation);
                        }

                        // Close custom date panel if open
                        if (typeof this.showCustom !== 'undefined') {
                            this.showCustom = false;
                        }

                        this.showToast('success', '{{ __("ui.dashboard_data_updated") ?? "Data dashboard berhasil diperbarui" }}');
                    } else {
                        throw new Error('Failed to fetch data');
                    }
                } catch (error) {
                    console.error('AJAX Error:', error);
                    this.showToast('error', 'Terjadi kesalahan saat memuat data');
                } finally {
                    this.isLoading = false;
                }
            },

            // Location Management
            showLocationModal: false,
            locationsList: [],
            isLoadingLocations: false,
            editingId: null,
            editingName: '',
            confirmDeleteId: null,
            confirmDeleteName: '',
            isDeleting: false,
            isUpdatingLocation: false,
            deleteLocationError: '',

            // Category Management
            showCategoryModal: false,
            categoriesList: [],
            isLoadingCategories: false,
            catEditingId: null,
            catEditingName: '',
            catConfirmDeleteId: null,
            catConfirmDeleteName: '',
            isDeletingCat: false,
            isUpdatingCategory: false,
            deleteCategoryError: '',

            // Brand Management
            showBrandModal: false,
            brandsList: [],
            isLoadingBrands: false,
            brandEditingId: null,
            brandEditingName: '',
            brandConfirmDeleteId: null,
            brandConfirmDeleteName: '',
            isDeletingBrand: false,
            isUpdatingBrand: false,
            deleteBrandError: '',
            newBrandName: '',
            isAddingBrand: false,

            // Add Location
            newLocationName: '',
            isAddingLocation: false,

            async addLocation() {
                if (!this.newLocationName.trim()) return;
                this.isAddingLocation = true;
                try {
                    const response = await fetch('{{ route("locations.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: this.newLocationName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message);
                        this.newLocationName = '';
                        this.fetchLocations();
                        this.refreshData();
                    } else {
                        const errorMsg = data.errors && Object.values(data.errors)[0][0] ? Object.values(data.errors)[0][0] : data.message;
                        this.showToast('warning', errorMsg);
                    }
                } catch (e) {
                    console.error('Add failed:', e);
                    this.showToast('error', 'Terjadi kesalahan.');
                } finally {
                    this.isAddingLocation = false;
                }
            },

            async openLocationModal() {
                this.showLocationModal = true;
                this.editingId = null;
                this.confirmDeleteId = null;
                this.fetchLocations();
            },

            startEdit(loc) {
                this.editingId = loc.id;
                this.editingName = loc.name;
                this.$nextTick(() => {
                    const input = document.getElementById('edit-input-' + loc.id);
                    if (input) input.focus();
                });
            },

            cancelEdit() {
                this.editingId = null;
                this.editingName = '';
            },

            async saveEdit(id) {
                if (!this.editingName || this.editingName.trim() === '') {
                    this.showToast('warning', 'Nama lokasi tidak boleh kosong.');
                    this.cancelEdit();
                    return;
                }

                // Cek jika tidak ada perubahan nama
                const loc = this.locationsList.find(l => l.id === id);
                if (loc && loc.name === this.editingName.trim()) {
                    this.cancelEdit();
                    return;
                }

                if (this.isUpdatingLocation) return;
                this.isUpdatingLocation = true;

                try {
                    await this.updateLocationName(id, this.editingName);
                } finally {
                    this.isUpdatingLocation = false;
                    this.cancelEdit();
                }
            },

            askDelete(loc) {
                this.confirmDeleteId = loc.id;
                this.confirmDeleteName = loc.name;
            },

            cancelDelete() {
                this.confirmDeleteId = null;
                this.confirmDeleteName = '';
                this.isDeleting = false;
                this.deleteLocationError = '';
            },

            // Category Methods
            // Add Category
            newCategoryName: '',
            isAddingCategory: false,

            async addCategory() {
                if (!this.newCategoryName.trim()) return;
                this.isAddingCategory = true;
                try {
                    const response = await fetch('{{ route("categories.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: this.newCategoryName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message);
                        this.newCategoryName = '';
                        this.fetchCategories();
                        this.refreshData();
                    } else {
                        const errorMsg = data.errors && Object.values(data.errors)[0][0] ? Object.values(data.errors)[0][0] : data.message;
                        this.showToast('warning', errorMsg);
                    }
                } catch (e) {
                    console.error('Add failed:', e);
                    this.showToast('error', 'Terjadi kesalahan.');
                } finally {
                    this.isAddingCategory = false;
                }
            },

            async openCategoryModal() {
                this.showCategoryModal = true;
                await this.fetchCategories();
            },

            async fetchCategories() {
                this.isLoadingCategories = true;
                try {
                    const response = await fetch('{{ route("categories.index") }}');
                    this.categoriesList = await response.json();
                } catch (e) {
                    console.error('Failed to fetch categories:', e);
                    this.showToast('error', 'Gagal memuat data kategori.');
                } finally {
                    this.isLoadingCategories = false;
                }
            },

            startCatEdit(cat) {
                this.catEditingId = cat.id;
                this.catEditingName = cat.name;
                this.$nextTick(() => {
                    const input = document.getElementById('cat-edit-input-' + cat.id);
                    if (input) input.focus();
                });
            },

            cancelCatEdit() {
                this.catEditingId = null;
                this.catEditingName = '';
            },

            async saveCatEdit(id) {
                if (!this.catEditingName || this.catEditingName.trim() === '') {
                    this.showToast('warning', 'Nama kategori tidak boleh kosong.');
                    this.cancelCatEdit();
                    return;
                }

                // Cek jika tidak ada perubahan nama
                const cat = this.categoriesList.find(c => c.id === id);
                if (cat && cat.name === this.catEditingName.trim()) {
                    this.cancelCatEdit();
                    return;
                }

                if (this.isUpdatingCategory) return;
                this.isUpdatingCategory = true;

                try {
                    const response = await fetch(`/categories/${id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: this.catEditingName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message);
                        this.fetchCategories();
                        this.cancelCatEdit();
                    } else {
                        const errorMsg = data.errors && Object.values(data.errors)[0][0] ? Object.values(data.errors)[0][0] : data.message;
                        this.showToast('warning', errorMsg);
                    }
                } catch (e) {
                    console.error('Update failed:', e);
                    this.showToast('error', 'Terjadi kesalahan saat memperbarui kategori.');
                } finally {
                    this.isUpdatingCategory = false;
                }
            },

            async toggleCategoryStatus(cat) {
                try {
                    const response = await fetch(`/categories/${cat.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ is_active: !cat.is_active, name: cat.name })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message || 'Status kategori berhasil diperbarui.');
                        this.fetchCategories();
                    } else {
                        this.showToast('warning', data.message);
                    }
                } catch (e) {
                    console.error('Toggle status failed:', e);
                }
            },

            askCatDelete(cat) {
                this.catConfirmDeleteId = cat.id;
                this.catConfirmDeleteName = cat.name;
            },

            cancelCatDelete() {
                this.catConfirmDeleteId = null;
                this.catConfirmDeleteName = '';
                this.isDeletingCat = false;
                this.deleteCategoryError = '';
            },

            // Brand Methods
            async addBrand() {
                if (!this.newBrandName.trim()) return;
                this.isAddingBrand = true;
                try {
                    const response = await fetch('{{ route("brands.store") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: this.newBrandName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        if (window.showToast) window.showToast('success', data.message);
                        this.newBrandName = '';
                        this.fetchBrands();
                        this.refreshData();
                    } else {
                        const errorMsg = data.errors && Object.values(data.errors)[0][0] ? Object.values(data.errors)[0][0] : data.message;
                        if (window.showToast) window.showToast('warning', errorMsg);
                    }
                } catch (e) {
                    console.error('Add failed:', e);
                    if (window.showToast) window.showToast('error', 'Terjadi kesalahan.');
                } finally {
                    this.isAddingBrand = false;
                }
            },

            async openBrandModal() {
                this.showBrandModal = true;
                await this.fetchBrands();
            },

            async fetchBrands() {
                this.isLoadingBrands = true;
                try {
                    const response = await fetch('{{ route("brands.index") }}');
                    this.brandsList = await response.json();
                } catch (e) {
                    console.error('Failed to fetch brands:', e);
                    if (window.showToast) window.showToast('error', 'Gagal memuat data merk.');
                } finally {
                    this.isLoadingBrands = false;
                }
            },

            startBrandEdit(brand) {
                this.brandEditingId = brand.id;
                this.brandEditingName = brand.name;
                this.$nextTick(() => {
                    const input = document.getElementById('brand-edit-input-' + brand.id);
                    if (input) input.focus();
                });
            },

            cancelBrandEdit() {
                this.brandEditingId = null;
                this.brandEditingName = '';
            },

            async saveBrandEdit(id) {
                if (!this.brandEditingName || this.brandEditingName.trim() === '') {
                    if (window.showToast) window.showToast('warning', 'Nama merk tidak boleh kosong.');
                    this.cancelBrandEdit();
                    return;
                }

                // Cek jika tidak ada perubahan nama
                const brand = this.brandsList.find(b => b.id === id);
                if (brand && brand.name === this.brandEditingName.trim()) {
                    this.cancelBrandEdit();
                    return;
                }

                if (this.isUpdatingBrand) return;
                this.isUpdatingBrand = true;

                try {
                    const response = await fetch(`/brands/${id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: this.brandEditingName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        if (window.showToast) window.showToast('success', data.message);
                        this.fetchBrands();
                        this.cancelBrandEdit();
                    } else {
                        const errorMsg = data.errors && Object.values(data.errors)[0][0] ? Object.values(data.errors)[0][0] : data.message;
                        if (window.showToast) window.showToast('warning', errorMsg);
                    }
                } catch (e) {
                    console.error('Update failed:', e);
                    if (window.showToast) window.showToast('error', 'Terjadi kesalahan saat memperbarui merk.');
                } finally {
                    this.isUpdatingBrand = false;
                }
            },

            async toggleBrandStatus(brand) {
                try {
                    const response = await fetch(`/brands/${brand.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ is_active: !brand.is_active, name: brand.name })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message || 'Status merk berhasil diperbarui.');
                        this.fetchBrands();
                    } else {
                        this.showToast('warning', data.message);
                    }
                } catch (e) {
                    console.error('Toggle status failed:', e);
                }
            },

            askBrandDelete(brand) {
                this.brandConfirmDeleteId = brand.id;
                this.brandConfirmDeleteName = brand.name;
            },

            cancelBrandDelete() {
                this.brandConfirmDeleteId = null;
                this.brandConfirmDeleteName = '';
                this.isDeletingBrand = false;
                this.deleteBrandError = '';
            },

            async deleteBrand(id) {
                if (this.isDeletingBrand) return;
                this.isDeletingBrand = true;
                this.deleteBrandError = '';
                try {
                    const response = await fetch(`/brands/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (response.ok) {
                        if (window.showToast) window.showToast('success', data.message);
                        this.fetchBrands();
                        this.cancelBrandDelete();
                    } else {
                        this.deleteBrandError = data.message || 'Tidak dapat menghapus merk ini.';
                    }
                } catch (e) {
                    console.error('Delete failed:', e);
                    this.deleteBrandError = 'Terjadi kesalahan. Silakan coba lagi.';
                } finally {
                    this.isDeletingBrand = false;
                }
            },

            resetWidgets() {
                const defaults = {
                    showStats: true,
                    showCharts: true,
                    showMovement: false,
                    showTopItems: false,
                    showLowStock: true,
                    showRecent: true,
                    showDeadStock: false,
                    showLeaderboard: false,
                    showBorrowings: true,
                    showOverdue: true,
                    showNoPriceItems: true
                };
                Object.keys(defaults).forEach(key => {
                    this[key] = defaults[key];
                    localStorage.setItem('dashboard_{{ auth()->id() }}_' + key, defaults[key]);
                });
                this.showToast('success', 'Tampilan dashboard telah direset.');
            },

            async deleteCategory(id) {
                if (this.isDeletingCat) return;
                this.isDeletingCat = true;
                this.deleteCategoryError = '';
                try {
                    const response = await fetch(`/categories/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message);
                        this.fetchCategories();
                        this.cancelCatDelete();
                    } else {
                        this.deleteCategoryError = data.message || 'Tidak dapat menghapus kategori ini.';
                    }
                } catch (e) {
                    console.error('Delete failed:', e);
                    this.deleteCategoryError = 'Terjadi kesalahan. Silakan coba lagi.';
                } finally {
                    this.isDeletingCat = false;
                }
            },

            async fetchLocations() {
                this.isLoadingLocations = true; // Fixed typo
                try {
                    const response = await fetch('{{ route("locations.index") }}');
                    this.locationsList = await response.json();
                } catch (e) {
                    console.error('Failed to fetch locations:', e);
                } finally {
                    this.isLoadingLocations = false; // Fixed typo
                }
            },

            async updateLocationName(id, newName) {
                if (!newName || newName.trim() === '') return;
                try {
                    const response = await fetch(`/locations/${id}`, {
                        method: 'PUT',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ name: newName })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        if (window.showToast) window.showToast('success', data.message);
                        this.fetchLocations();
                        this.refreshData(); // Refresh counts on dashboard
                    } else {
                        const errorMsg = data.errors && Object.values(data.errors)[0][0] ? Object.values(data.errors)[0][0] : data.message;
                        if (window.showToast) window.showToast('error', errorMsg);
                    }
                } catch (e) {
                    console.error('Update failed:', e);
                    if (window.showToast) window.showToast('error', 'Koneksi terputus atau terjadi kesalahan sistem.');
                }
            },

            async toggleLocationStatus(loc) {
                try {
                    const response = await fetch(`/locations/${loc.id}`, {
                        method: 'PATCH',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ is_active: !loc.is_active, name: loc.name })
                    });
                    const data = await response.json();
                    if (response.ok) {
                        if (window.showToast) window.showToast('success', data.message || 'Status lokasi berhasil diperbarui.');
                        this.fetchLocations();
                    } else {
                        if (window.showToast) window.showToast('error', data.message);
                    }
                } catch (e) {
                    console.error('Toggle status failed:', e);
                    if (window.showToast) window.showToast('error', 'Koneksi terputus atau terjadi kesalahan sistem.');
                }
            },

            async deleteLocation(id) {
                if (this.isDeleting) return;
                this.isDeleting = true;
                this.deleteLocationError = '';
                try {
                    const response = await fetch(`/locations/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (response.ok) {
                        this.showToast('success', data.message);
                        this.fetchLocations();
                        this.totalLocations--;
                        this.cancelDelete();
                    } else {
                        this.deleteLocationError = data.message || 'Tidak dapat menghapus lokasi ini.';
                    }
                } catch (e) {
                    console.error('Delete failed:', e);
                    this.deleteLocationError = 'Terjadi kesalahan. Silakan coba lagi.';
                } finally {
                    this.isDeleting = false;
                }
            },
            
            // Data Statis & List
            totalSpareparts: {{ $totalSpareparts }},
            totalStock: {{ $totalStock }},
            totalCategories: {{ $totalCategories }},
            totalBrands: {{ $totalBrands }},
            totalLocations: {{ $totalLocations }},
            pendingApprovalsCount: {{ $pendingApprovalsCount }},
            activeBorrowingsCount: {{ $activeBorrowingsCount }},

            // Arrays (untuk x-for)
            recentActivities: @json($recentActivities),
            topExited: @json($topExited),
            topEntered: @json($topEntered),
            deadStockItems: @json($deadStockItems),
            activeUsers: @json($activeUsers),
            activeBorrowingsList: @json($activeBorrowingsList),
            overdueBorrowingsList: @json($overdueBorrowingsList),
            lowStockItems: @json($lowStockItems),
            noPriceItems: @json($noPriceItems ?? []),

            init() {
                // Cek jika ada parameter untuk buka modal lokasi otomatis
                const params = new URLSearchParams(window.location.search);
                if (params.get('manage_locations') === 'true') {
                    this.openLocationModal();
                    window.history.replaceState({}, document.title, window.location.pathname);
                }

                if (params.get('manage_categories') === 'true') {
                    this.openCategoryModal();
                    window.history.replaceState({}, document.title, window.location.pathname);
                }

                // Tampilkan konten setelah loading selesai
                setTimeout(() => {
                    this.isLoading = false;
                    
                    // Gunakan nextTick + setTimeout agar DOM benar-benar selesai di-paint oleh browser
                    // sebelum Chart.js menghitung ulang dimensinya (menghindari ukuran 0x0)
                    this.$nextTick(() => {
                        setTimeout(() => {
                            if (window.updateDashboardCharts) {
                                window.updateDashboardCharts(this.movementData, this.stockByCategory, this.stockByLocation);
                            }
                            
                            if (this.showMovement && window.fetchMovementData) {
                                window.fetchMovementData(30);
                            }
                        }, 100);
                    });
                }, 300);

                // Listener untuk event real-time global (dari realtime-inventory.js)
                window.addEventListener('dashboard-refresh', (e) => {
                    this.updateState(e.detail);
                });
            },

            // Charts Data
            movementData: @json($movementData),
            stockByCategory: @json($stockByCategory),
            stockByLocation: @json($stockByLocation),
            noPriceItems: @json($noPriceItems ?? []),
            
            updateState(data) {
                if (!data) return;
                this.totalSpareparts = data.totalSpareparts ?? this.totalSpareparts;
                this.totalStock = data.totalStock ?? this.totalStock;
                this.totalCategories = data.totalCategories ?? this.totalCategories;
                this.totalBrands = data.totalBrands ?? this.totalBrands;
                this.totalLocations = data.totalLocations ?? this.totalLocations;
                this.pendingApprovalsCount = data.pendingApprovalsCount ?? this.pendingApprovalsCount;
                this.activeBorrowingsCount = data.activeBorrowingsCount ?? this.activeBorrowingsCount;

                if (data.recentActivities) this.recentActivities = data.recentActivities;
                if (data.activeBorrowingsList) this.activeBorrowingsList = data.activeBorrowingsList;
                if (data.overdueBorrowingsList) this.overdueBorrowingsList = data.overdueBorrowingsList;
                if (data.noPriceItems) this.noPriceItems = data.noPriceItems;

                if (window.updateDashboardCharts) {
                    window.updateDashboardCharts(data.movementData, data.stockByCategory, data.stockByLocation, data);
                }
            },

            async refreshData() {
               try {
                   const url = new URL('{{ route("dashboard.superadmin") }}');
                   const currentParams = new URLSearchParams(window.location.search);
                   currentParams.forEach((val, key) => url.searchParams.set(key, val));

                   const response = await fetch(url.toString(), {
                       headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                   });
                   
                   if (!response.ok) throw new Error('Refresh failed');
                   const data = await response.json();
                   this.updateState(data);
               } catch (error) {
                   console.error('Failed to refresh dashboard data:', error);
               }
            },

            viewActivityDetails(activity) {
                this.selectedActivity = activity;
                this.showActivityModal = true;
            },

            async toggle(key) {
                const widgetKeys = ['showStats', 'showCharts', 'showMovement', 'showTopItems', 'showLowStock', 'showRecent', 'showDeadStock', 'showLeaderboard', 'showBorrowings', 'showOverdue', 'showNoPriceItems'];
                
                // Parse strings to boolean just in case
                this[key] = (this[key] === true || String(this[key]) === 'true');

                const activeCount = widgetKeys.filter(k => this[k] === true || String(this[k]) === 'true').length;
                
                // If they unchecked the last one, activeCount will be 0
                if (activeCount < 1) {
                     if (window.showToast) window.showToast('warning', 'Minimal satu widget harus tetap aktif.');
                     this[key] = true; // Revert it
                     return;
                }

                localStorage.setItem('dashboard_{{ auth()->id() }}_' + key, this[key]);
                if (key === 'showMovement' && this[key] && window.fetchMovementData) {
                    window.fetchMovementData(30);
                }

                try {
                    await fetch('{{ route("profile.settings.update") }}', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ settings: { [key]: this[key] } })
                    });
                } catch (e) { console.error('Settings sync failed:', e); }
            },

            async resetWidgets() {
                const defaults = {
                    showStats: true,
                    showCharts: true,
                    showLowStock: true,
                    showBorrowings: true,
                    showOverdue: true,
                    showNoPriceItems: true,
                    showMovement: true,
                    showRecent: true,
                    showTopItems: false,
                    showDeadStock: false,
                    showLeaderboard: false
                };

                for (const [key, value] of Object.entries(defaults)) {
                    this[key] = value;
                    localStorage.setItem('dashboard_{{ auth()->id() }}_' + key, value);
                }

                if (window.showToast) window.showToast('success', 'Tampilan kembali ke default.');
                if (this.showMovement && window.fetchMovementData) window.fetchMovementData(30);

                try {
                    await fetch('{{ route("profile.settings.update") }}', {
                        method: 'PATCH',
                        headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Accept': 'application/json' },
                        body: JSON.stringify({ settings: defaults })
                    });
                } catch (e) { console.error('Settings sync failed:', e); }
            }
        };
    }
</script>
@endpush
